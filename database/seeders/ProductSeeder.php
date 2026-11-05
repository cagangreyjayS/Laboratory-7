<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'subject'  => 'Web Systems',
                'cn'       => 'WS-001',
                'category' => 'Web Systems',
                'name'     => 'Submitted Web Systems Laboratory',
                'status'   => 'completed',
                'score'    => 95,
            ],
            [
                'subject'  => 'JavaScript',
                'cn'       => 'JS-001',
                'category' => 'JavaScript',
                'name'     => 'Completed JavaScript Quiz',
                'status'   => 'completed',
                'score'    => 92,
            ],
            [
                'subject'  => 'Database',
                'cn'       => 'DB-001',
                'category' => 'Database',
                'name'     => 'Database activity due soon',
                'status'   => 'pending',
                'score'    => null,
            ],
            [
                'subject'  => 'JavaScript',
                'cn'       => 'JS-002',
                'category' => 'JavaScript',
                'name'     => 'DOM manipulation exercise',
                'status'   => 'completed',
                'score'    => 88,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}