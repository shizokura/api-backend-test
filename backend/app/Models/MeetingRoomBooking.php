<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
                    if ($duration_minutes < 30 || $duration_minutes > 60) {
                        $fail('The booking duration must be either 30 minutes or 1 hour long.');
                    }

                    // if ($duration_minutes !== 30 && $duration_minutes !== 60) {
                    //     $fail('The booking duration must be either 30 minutes or 1 hour.');
                    // }
                }
            ],
        ];
    }
}
