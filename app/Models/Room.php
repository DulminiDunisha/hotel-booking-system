<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'number',
        'name',
        'type',
        'base_price',
        'capacity',
        'status',
        'description',
        'amenities',
        'image',
    ];

    protected $casts = [
        'base_price' => 'float',
        'capacity' => 'integer',
        'amenities' => 'array',
    ];

    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class);
    }

    public function seasonalRates()
    {
        return $this->hasMany(RoomSeasonalRate::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    //
}
