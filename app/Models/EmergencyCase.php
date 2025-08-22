<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'description',
        'status',
        'refund_amount',
        'refund_status',
        'emergency_details',
        'resolved_at'
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'emergency_details' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Type constants
    const TYPE_ILLNESS = 'illness';
    const TYPE_CANCELLATION = 'cancellation';
    const TYPE_EARLY_CHECKOUT = 'early_checkout';

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    // Refund status constants
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_PROCESSED = 'processed';
    const REFUND_STATUS_COMPLETED = 'completed';
}
