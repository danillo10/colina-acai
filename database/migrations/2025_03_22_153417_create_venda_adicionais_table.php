<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendaAdicionaisTable extends Migration
{
    public function up()
    {
        Schema::create('venda_adicionais', function (Blueprint $table) {
            $table->unsignedBigInteger('venda_id');
            $table->unsignedBigInteger('adicional_id');

            $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('cascade');
            $table->foreign('adicional_id')->references('id')->on('adicionais');

            $table->primary(['venda_id', 'adicional_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('venda_adicionais');
    }
}
