<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'preco'
    ];

    public function vendas()
    {
        return $this->hasMany(Venda::class, 'produto_id');
    }
}
