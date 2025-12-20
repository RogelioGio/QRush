<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableSessions extends Model
{
    protected $fillable = [
        'table_id',
        'status',
        'opened_at',
        'closed_at',
    ];

    public function table()
    {
        return $this->belongsTo(Tables::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_session_id');
    }

    public function orderItems()
    {
        return $this->hasManyThrough(OrderItem::class, Order::class, 'table_session_id', 'order_id');
    }
}
