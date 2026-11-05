<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'Acme Supplies Inc.',
            'contact_person' => 'John Doe',
            'email' => 'contact@acmesupplies.com',
            'phone' => '555-0199',
            'lead_time_days' => 5,
        ]);

        Supplier::create([
            'name' => 'Global Tech Distributors',
            'contact_person' => 'Jane Smith',
            'email' => 'support@globaltech.com',
            'phone' => '555-0142',
            'lead_time_days' => 3,
        ]);
    }
}