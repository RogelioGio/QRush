<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Tables extends Model
{
    public function hasActiveOrder(): Attribute {
        return Attribute::make(
            get: fn () => $this->orders()->whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_PREPARING,
                Order::STATUS_READY,
            ])->exists()
        );
    }


    protected $fillable = [
        'table_number',
        'is_active',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }

    public function tableSessions()
    {
        return $this->hasMany(TableSessions::class, 'table_id');
    }
}
