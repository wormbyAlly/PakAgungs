<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $fillable = ['code', 'nama', 'jenis_coa_id'];

    public function jenis()
    {
        return $this->belongsTo(JenisCoa::class, 'jenis_coa_id');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}
