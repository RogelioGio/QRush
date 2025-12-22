<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'table_session_id',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'reference_no',
    ];

    public function tableSession()
    {
        return $this->belongsTo(TableSessions::class, 'table_session_id');
    }

}
