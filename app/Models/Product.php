<?php

namespace App\Models;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'price',
    ];
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validStocks()
    {
        return $this->hasMany(Stock::class)
            ->where('expired', '>', Carbon::today());
    }
}
