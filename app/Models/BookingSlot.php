<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSlot extends Model
{
    /** @use HasFactory<\Database\Factories\BookingSlotFactory> */
    use HasFactory;

    protected $fillable = ['booking_id', 'start_time', 'end_time'];

    public function bookings(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
