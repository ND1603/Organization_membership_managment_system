<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentVerification extends Model
{
    // These fields can be filled by forms
    protected $fillable = [
        'user_id',
        'screenshot_path',
        'extracted_transaction_id',
        'extracted_amount',
        'extracted_date',
        'raw_ocr_text',
        'status',
        'admin_note',
        'verified_by',
        'verified_at',
    ];

    // Cast date field automatically
    protected $casts = [
        'extracted_date' => 'date',
        'verified_at'    => 'datetime',
        'extracted_amount' => 'decimal:2',
    ];

    // A payment verification belongs to a user (the member)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // A payment verification is verified by an admin (also a user)
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Helper to check status
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}
