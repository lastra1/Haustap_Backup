<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'service_name',
        'scheduled_date',
        'scheduled_time',
        'address',
        'status',
        'price',
        'notes',
        'rating',
        'rated_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'price' => 'decimal:2',
        'rated_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Allowed booking statuses aligned with UI tabs
    public const STATUS_PENDING = 'pending';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }
}

