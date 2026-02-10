<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'returned_by_user_id',
        'condition',
        'condition_note',
        'returned_at',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by_user_id');
    }
}
