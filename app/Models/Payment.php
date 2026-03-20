<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;

class Payment extends Model
{

    protected $fillable = [
        'enrollment_id',
        'month',
        'year',
        'amount',
        'paid',
        'paid_at',
    ];
    protected $casts = [
        'paid_at' => 'datetime',
        'paid' => 'boolean',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}
