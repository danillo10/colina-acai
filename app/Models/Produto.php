<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'preco',
        'user_id'
    ];

    public function vendas()
    {
        return $this->hasMany(Venda::class, 'produto_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
