<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoomBooking;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = MeetingRoomBooking::with('user')->paginate(10);
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
}
