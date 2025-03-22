<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run()
    {
        DB::table('produtos')->insert([
            [
                'nome'  => 'Açai 300ml',
                'preco' => 10.00,
            ],
            [
                'nome'  => 'Açai 500ml',
                'preco' => 15.00,
            ],
            [
                'nome'  => 'Açai 700ml',
                'preco' => 20.00,
            ],
            [
                'nome'  => 'Açai 1L',
                'preco' => 25.00,
            ],
        ]);
    }
}
