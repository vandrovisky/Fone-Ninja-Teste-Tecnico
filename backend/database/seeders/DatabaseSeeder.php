<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@fone-ninja.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        $products = [
            ['name' => 'Cabo USB-C 1m',        'sale_price' => 29.90],
            ['name' => 'Película Protetora',    'sale_price' => 19.90],
            ['name' => 'Carregador 20W',        'sale_price' => 79.90],
            ['name' => 'Fone Bluetooth',        'sale_price' => 149.90],
            ['name' => 'Capa Silicone iPhone',  'sale_price' => 39.90],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['name' => $data['name']],
                [
                    'sale_price'    => $data['sale_price'],
                    'average_cost'  => 0,
                    'current_stock' => 0,
                ]
            );
        }
    }
}
