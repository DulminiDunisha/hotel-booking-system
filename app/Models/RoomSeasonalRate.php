<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomSeasonalRate extends Model
{
    protected $fillable = [
        'room_id',
        'name',
        'start_date',
        'end_date',
        'multiplier',
    ];

    protected $casts = [
        'multiplier' => 'float',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    //
}
