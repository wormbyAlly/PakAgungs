<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'journal_no',
        'journal_date',
        'sale_id',
        'coa_id',
        'amount',
        'type',
        'description',
    ];

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
