<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendaProduto extends Model
{
    protected $table = 'venda_produtos';
    public $timestamps = false;
    protected $fillable = ['venda_id', 'produto_id'];

    public function produto()
    {
        return $this->belongsTo(\App\Models\Produto::class, 'produto_id');
    }

    public function adicionais()
    {
        return $this->belongsToMany(\App\Models\Adicional::class, 'venda_produto_adicionais', 'venda_produto_id', 'adicional_id');
    }
}
