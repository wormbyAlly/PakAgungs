<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    const STATUS_BORROWED = 'borrowed';
    const STATUS_RETURNED = 'returned';
    const STATUS_CANCELED = 'canceled';

    // App\Models\Loan.php
    protected $fillable = [
        'user_id',
        'item_id',
        'teacher_id',
        'quantity',
        'location',
        'loan_date',
        'return_date',
        'status',
    ];

    /* =====================
     | RELATIONS
     ===================== */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /* =====================
     | HELPERS
     ===================== */

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function return()
    {
        return $this->hasOne(LoanReturn::class);
    }
}
