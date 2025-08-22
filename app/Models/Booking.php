<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'nights',
        'adults',
        'children',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'special_requests',
        'emergency_contact',
        'emergency_phone'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function events()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function emergencyCase()
    {
        return $this->hasOne(EmergencyCase::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'guest_id');
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EMERGENCY_CANCELLED = 'emergency_cancelled';

    // Payment status constants
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_COMPLETED = 'completed';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
}
