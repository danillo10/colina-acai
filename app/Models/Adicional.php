<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adicional extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'preco'
    ];

    public function vendas()
    {
        return $this->belongsToMany(Venda::class, 'venda_adicionais', 'adicional_id', 'venda_id');
    }
}
