<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class MeetingRoomBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_name',
        'booking_date',
        'booking_from',
        'booking_to',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBookingFromAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->format('g:i A');
    }

    public function getBookingToAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->format('g:i A');
    }

    public function getBookingDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('F d, Y');
    }

    public static function rules()
    {
        return [
            'room_name' => 'required',
            'booking_date' => 'required|date',
            'booking_from' => [
                'required',
                'date_format:H:i:s',
                function ($attribute, $value, $fail) {
                    // Validate the start time is between 8:00 AM and 5:00 PM
                    if (strtotime($value) < strtotime('08:00:00') || strtotime($value) > strtotime('17:00:00')) {
                        $fail('The booking start time must be between 8:00 AM and 5:00 PM.');
                    }

                    // validate if already exists
                    $existing_booking = self::where('booking_date', request('booking_date'))->where('booking_from', $value)->first();

                    if ($existing_booking) {
                        $fail('The room is already booked for the chosen time.');
                    }
                },
            ],
            'booking_to' => [
                'required',
                'date_format:H:i:s',
                'after:booking_from', // Ensure end time is after start time
                function ($attribute, $value, $fail) {
                    // Calculate the booking duration in minutes
                    $start_time = strtotime(request('booking_from'));
                    $end_time = strtotime($value);
                    $duration_minutes = ($end_time - $start_time) / 60;

                    // Validate that the booking is either 30 minutes or 1 hour
                    // if ($duration_minutes < 30 || $duration_minutes > 60) {
                    //     $fail('The booking duration must be either 30 minutes or 1 hour long.');
                    // }

                    if ($duration_minutes !== 30 && $duration_minutes !== 60) {
                        $fail('The booking duration must be either 30 minutes or 1 hour.');
                    }

                    $existing_booking = self::where('booking_date', request('booking_date'))->where('booking_to', $value)->first();

                    if ($existing_booking) {
                        $fail('The room is already booked for the chosen time.');
                    }
                }
            ],
        ];
    }
}
