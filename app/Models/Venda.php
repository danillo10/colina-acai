<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $fillable = [
        'produto_id',
        'forma_pagamento',
        'troco',
        'nome_cliente',
        'whatsapp',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'valor_total',
        'entrega',
        'status',
        'user_id'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function adicionais()
    {
        return $this->belongsToMany(Adicional::class, 'venda_adicionais', 'venda_id', 'adicional_id');
    }

    public function vendaProdutos()
    {
        return $this->hasMany(VendaProduto::class, 'venda_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

}
