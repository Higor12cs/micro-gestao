<?php

namespace Database\Seeders;

use App\Models\Tenant;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::create([
            'name' => 'Example Company',
            'trial_ends_at' => now()->addDays(14),
            'subscription_ends_at' => now()->addMonths(1),
        ]);

        $user = $tenant->users()->create([
            'sequential' => 1,
            'name' => 'Higor Carneiro',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        for ($i = 1; $i <= 20; $i++) {
            \App\Models\Supplier::create([
                'tenant_id' => $tenant->id,
                'first_name' => 'Fornecedor '.str_pad($i, 3, '0', STR_PAD_LEFT),
                'created_by' => $user->id,
            ]);

            \App\Models\Customer::create([
                'tenant_id' => $tenant->id,
                'first_name' => 'Cliente '.str_pad($i, 3, '0', STR_PAD_LEFT),
                'created_by' => $user->id,
            ]);

            \App\Models\Product::create([
                'tenant_id' => $tenant->id,
                'name' => 'Produto '.str_pad($i, 3, '0', STR_PAD_LEFT),
                'cost_price' => 100,
                'sale_price' => 200,
                'created_by' => $user->id,
            ]);
        }
    }
}
