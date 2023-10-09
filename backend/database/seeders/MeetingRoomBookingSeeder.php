<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MeetingRoomBooking;

class MeetingRoomBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample meeting room bookings
        $bookings = [
            [
                'room_name' => 'Meeting Room A',
                'user_id' => 1,
                'booking_date' => '2023-10-09',
                'booking_from' => '10:00:00',
                'booking_to' => '11:00:00',
            ],
            [
                'room_name' => 'Meeting Room B',
                'user_id' => 1,
                'booking_date' => '2023-10-09',
                'booking_from' => '11:00:00',
                'booking_to' => '11:30:00',
            ],
            [
                'room_name' => 'Meeting Room C',
                'user_id' => 1,
                'booking_date' => '2023-10-09',
                'booking_from' => '11:30:00',
                'booking_to' => '12:30:00',
            ],
        ];

        // Insert data into the 'meeting_room_bookings' table
        foreach ($bookings as $booking) {
            MeetingRoomBooking::create($booking);
        }
    }
}
