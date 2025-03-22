<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdicionaisTable extends Migration
{
    public function up()
    {
        Schema::create('adicionais', function (Blueprint $table) {
            $table->id(); // Chave primária auto-incrementável
            $table->string('nome', 50);
            $table->decimal('preco', 10, 2);
        });
    }

    public function down()
    {
        Schema::dropIfExists('adicionais');
    }
}
