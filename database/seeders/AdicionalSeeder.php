<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdicionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('adicionais')->insert([
            [
                'nome'  => 'Morango',
                'preco' => 2.00,
            ],
            [
                'nome'  => 'Uva',
                'preco' => 2.00,
            ],
            [
                'nome'  => 'Leite Condensado',
                'preco' => 0.00,
            ],
            [
                'nome'  => 'Nutela',
                'preco' => 3.00,
            ],
            [
                'nome'  => 'Leite ninho',
                'preco' => 0.00,
            ],
            [
                'nome'  => 'Granola',
                'preco' => 1.00,
            ],
            [
                'nome'  => 'Canudo Recheado',
                'preco' => 1.00,
            ],
            [
                'nome'  => 'Tapioca',
                'preco' => 1.00,
            ],
            [
                'nome'  => 'Amendoim',
                'preco' => 1.00,
            ],
            [
                'nome'  => 'Biz',
                'preco' => 1.00,
            ],
            [
                'nome'  => 'Disquete',
                'preco' => 2.00,
            ],
            [
                'nome'  => 'Gotas de chocolate',
                'preco' => 2.00,
            ],
            [
                'nome'  => 'Banana',
                'preco' => 1.00,
            ],
        ]);
    }
}
