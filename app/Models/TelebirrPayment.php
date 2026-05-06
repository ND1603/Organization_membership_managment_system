<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelebirrPayment extends Model
{
    protected $fillable = [
        'user_id',
        'out_trade_no',
        'amount',
        'description',
        'status',
        'trade_no',
        'msisdn',
        'pay_url',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    // Which user made this payment
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isPending(): bool { return $this->status === 'pending'; }
    public function isPaid(): bool    { return $this->status === 'paid'; }
    public function isFailed(): bool  { return $this->status === 'failed'; }
}