<?php

namespace Database\Seeders;

use App\Models\MoneyEntry;
use Illuminate\Database\Seeder;

class MoneyEntrySeeder extends Seeder
{
    public function run(): void
    {
        MoneyEntry::factory()->count(5)->create();
        MoneyEntry::factory()->spending()->count(10)->create();
    }
}
