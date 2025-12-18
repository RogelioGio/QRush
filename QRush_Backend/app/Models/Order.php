<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_SERVED = 'served';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_TRANSITION = [
        self::STATUS_PENDING => [
            self::STATUS_CONFIRMED,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_CONFIRMED => [
            self::STATUS_PREPARING,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_PREPARING => [
            self::STATUS_READY,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_READY => [
            self::STATUS_SERVED,
        ],
        self::STATUS_SERVED => [],
        self::STATUS_CANCELLED => [],
    ];

    public const ActiveStatuses = [
        'pending',
        'confirmed',
        'preparing',
        'ready',
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::STATUS_TRANSITION[$this->status] ?? [], true);
    }

    public function getTotalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->orderItems->sum(fn ($item) => $item->price_snapshot * $item->quantity)
        );
    }



    protected $fillable = [
        'table_id',
        'status',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(Tables::class, 'table_id');
    }
}
