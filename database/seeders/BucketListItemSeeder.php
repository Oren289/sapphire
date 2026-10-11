<?php

namespace Database\Seeders;

use App\Models\BucketListItem;
use Illuminate\Database\Seeder;

class BucketListItemSeeder extends Seeder
{
    public function run(): void
    {
        BucketListItem::factory()->count(8)->create();
        BucketListItem::factory()->fulfilled()->count(2)->create();
    }
}
