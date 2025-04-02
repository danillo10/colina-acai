<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adicional extends Model
{
    public $timestamps = false;

    protected $table = 'adicionais';

    protected $fillable = [
        'nome',
        'preco',
        'user_id'
    ];

    public function vendas()
    {
        return $this->belongsToMany(Venda::class, 'venda_adicionais', 'adicional_id', 'venda_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
