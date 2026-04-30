<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanResult extends Model
{
    protected $fillable = [
        'domain_id', 'severity', 'template_id', 'template_name', 'matched_at', 'raw'
    ];

    protected $casts = [
        'raw' => 'array',
    ];

     public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
