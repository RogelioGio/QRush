<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tables extends Model
{
    protected $fillable = [
        'table_number',
        'is_active',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
