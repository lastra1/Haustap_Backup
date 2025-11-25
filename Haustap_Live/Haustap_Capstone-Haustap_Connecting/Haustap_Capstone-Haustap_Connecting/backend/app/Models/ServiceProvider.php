<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'description',
        'location',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
