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

        $tenant->users()->create([
            'sequential' => 1,
            'name' => 'Higor Carneiro',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
