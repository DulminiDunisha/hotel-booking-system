<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'event_date',
        'type',
        'guest_name',
        'room_name',
        'time'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    //
}
