<?php

namespace Database\Factories;

use App\Models\BucketListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BucketListItem> */
class BucketListItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'category' => fake()->randomElement(['Travel', 'Food', 'Adventure', 'Personal growth']),
            'description' => fake()->paragraph(),
            'is_fulfilled' => false,
        ];
    }

    public function fulfilled(): static
    {
        return $this->state(fn (array $attributes): array => ['is_fulfilled' => true]);
    }
}
