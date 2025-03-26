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
            $table->string('nome_cliente', 100);
            $table->string('whatsapp', 20);
            $table->string('rua', 200);
            $table->string('numero', 20);
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100);
            $table->string('forma_pagamento', 10);
            $table->decimal('troco', 10,2)->nullable();
            $table->decimal('valor_total', 10,2);
            $table->boolean('entrega');
            $table->string('status', 20)->default('Em preparo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
