<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    protected $fillable = [
        'room_id',
        'date',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    //
}
