<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produks';

    protected $fillable = [
        'name',
    ];
    public function loans()
{
    return $this->hasMany(Loan::class);
}

}
