<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendaStatusHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('venda_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venda_id');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            // Chave estrangeira para a tabela vendas
            $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('cascade');
            // Chave estrangeira para a tabela users (quem alterou)
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('venda_status_histories');
    }
}
