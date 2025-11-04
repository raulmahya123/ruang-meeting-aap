<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// app/Models/Booking.php
class Booking extends Model
{
    protected $fillable = [
        'room_id',
        'title',
        'start_at',
        'end_at',
        'booked_by_name',
        'booked_by_email',
        'notes',
        'cancel_token',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
