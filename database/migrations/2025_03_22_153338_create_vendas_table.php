<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendasTable extends Migration
{
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produto_id'); // Produto escolhido
            $table->foreign('produto_id')->references('id')->on('produtos');
            $table->string('forma_pagamento', 10); // ex: credit, debit, cash
            $table->decimal('troco', 10, 2)->nullable(); // Valor para troco, se necessário
            // Dados do cliente e endereço
            $table->string('nome_cliente', 100);
            $table->string('whatsapp', 20);
            $table->string('rua', 200);
            $table->string('numero', 20);
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100);
            $table->decimal('valor_total', 10, 2);
            // Pode incluir campos específicos para entrega se necessário
            $table->boolean('entrega')->default(false); // true se for entrega
            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
