<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoomBooking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = MeetingRoomBooking::with('user')->paginate(10);
        return response()->json(['bookings' => $bookings], 200);
    }
}
