<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['fee_id', 'amount', 'payment_date', 'payment_method', 'transaction_reference', 'receipt_no'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
}
