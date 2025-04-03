<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToVendasTable extends Migration
{
    public function up()
    {
        Schema::table('vendas', function (Blueprint $table) {
            // Adiciona a coluna "status" com valores possíveis
            $table->enum('status', ['Entregue', 'Cancelada', 'Aguardando', 'Preparando', 'A caminho'])
                  ->default('Aguardando')
                  ->after('valor_total');
        });
    }

    public function down()
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
