<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendaProdutoAdicionaisTable extends Migration
{
    public function up()
    {
        Schema::create('venda_produto_adicionais', function (Blueprint $table) {
            $table->unsignedBigInteger('venda_produto_id');
            $table->unsignedBigInteger('adicional_id');

            $table->foreign('venda_produto_id')->references('id')->on('venda_produtos')->onDelete('cascade');
            $table->foreign('adicional_id')->references('id')->on('adicionais');
            $table->primary(['venda_produto_id', 'adicional_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('venda_produto_adicionais');
    }
}
