<?php

namespace Database\Seeders;

use App\Models\User;
use App\Order\Domain\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Order::factory()->count(20)->create();
    }
}
