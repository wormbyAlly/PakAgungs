<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisCoa extends Model
{
    protected $fillable = ['code', 'nama', 'induk_id'];

    public function parent()
    {
        return $this->belongsTo(JenisCoa::class, 'induk_id');
    }

    public function children()
    {
        return $this->hasMany(JenisCoa::class, 'induk_id');
    }

    public function coas()
    {
        return $this->hasMany(Coa::class);
    }
}
