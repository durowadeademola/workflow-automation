<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycSubmission extends Model
{
    protected $fillable = [
        'client_id',
        'status',
        'full_name',
        'document_type',
        'document_number',
        'document_front',
        'document_back',
        'selfie',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public const DOCUMENT_TYPES = [
        'nin' => 'National ID (NIN)',
        'passport' => 'International Passport',
        'drivers_license' => "Driver's License",
        'voters_card' => "Voter's Card",
        'cac' => 'CAC Certificate',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
