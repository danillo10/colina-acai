<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutosTable extends Migration
{
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id(); // Chave primária auto-incrementável
            $table->string('nome', 50);   // Ex: "Açai 300ml"
            $table->decimal('preco', 10, 2);
        });
    }

    public function down()
    {
        Schema::dropIfExists('produtos');
    }
}
