<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoomBooking;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = MeetingRoomBooking::with('user')->select('meeting_room_bookings.*')->join('users', 'meeting_room_bookings.user_id', '=', 'users.id');

        if ($request->sort_column && $request->sort_order) {
            $query = $query->orderBy($request->sort_column, $request->sort_order);
        }

        if ($request->from && $request->to) {
            $query = $query->whereBetween('booking_date', [$request->from, $request->to]);
        }

        if ($request->meeting_room) {
            $query = $query->where('room_name', $request->meeting_room);
        }

        if (auth('sanctum')->user() && $request->user === 'My Bookings') {
            $query = $query->where('user_id', auth('sanctum')->user()->id);
        }

        if ($request->search) {
            $query = $query->where('users.name', 'LIKE', "%$request->search%")->orWhere('room_name', 'LIKE', "%$request->search%");
        }

        $bookings = $query->paginate(10);
        return response()->json(['bookings' => $bookings], 200);
    }

    public function create(Request $request)
    {
        try {
            $request->validate(MeetingRoomBooking::rules());
        } catch (ValidationException $e) {
            return response()->json([ 'status' => 'error', 'message' => 'Validation Error', 'messages' => $e->errors() ], 503);
        }

        $booking = MeetingRoomBooking::create([
            'room_name' => $request->room_name,
            'booking_date' => $request->booking_date,
            'booking_from' => $request->booking_from,
            'booking_to' => $request->booking_to,
            'user_id' => $request->user()->id
        ]);

        return response()->json(['booking' => $booking], 200);
    }

    public function get($id)
    {
        $booking = MeetingRoomBooking::with('user')->find($id);
        return response()->json(['booking' => $booking], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate(MeetingRoomBooking::rules($id));
        } catch (ValidationException $e) {
            return response()->json([ 'status' => 'error', 'message' => 'Validation Error', 'messages' => $e->errors() ], 503);
        }

        // validate current logged in user
        $user = User::find($request->user()->id);
        if ($user->role !== 'admin' && $request->user()->id !== MeetingRoomBooking::where('id', $id)->first()->user_id) {
            return response()->json([ 'status' => 'error', 'message' => 'Insufficient Permission' ], 503);
        }

        $booking = MeetingRoomBooking::where('id', $id)->update([
            'room_name' => $request->room_name,
            'booking_date' => $request->booking_date,
            'booking_from' => $request->booking_from,
            'booking_to' => $request->booking_to
        ]);

        return response()->json(['booking' => $booking], 200);
    }

    public function delete($id)
    {
        $booking = MeetingRoomBooking::where('id', $id)->delete();

        return response()->json(['booking' => $booking], 200);
    }

    public function group()
    {
        $groupedBookings = MeetingRoomBooking::select('room_name')
                                             ->selectRaw('COUNT(*) as count')
                                             ->groupBy('room_name')
                                             ->get();

        return response()->json(['bookings' => $groupedBookings], 200);
    }
}
