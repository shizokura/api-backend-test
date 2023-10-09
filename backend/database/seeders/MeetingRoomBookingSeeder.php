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
                'booked_by' => 'John Doe',
                'booking_date' => '2023-10-09',
                'booking_from' => '10:00:00',
                'booking_to' => '11:30:00',
            ],
            [
                'room_name' => 'Meeting Room B',
                'booked_by' => 'Jane Smith',
                'booking_date' => '2023-10-10',
                'booking_from' => '14:00:00',
                'booking_to' => '15:30:00',
            ],
            [
                'room_name' => 'Meeting Room C',
                'booked_by' => 'Alice Johnson',
                'booking_date' => '2023-10-11',
                'booking_from' => '09:30:00',
                'booking_to' => '10:30:00',
            ],
        ];

        // Insert data into the 'meeting_room_bookings' table
        foreach ($bookings as $booking) {
            MeetingRoomBooking::create($booking);
        }
    }
}
