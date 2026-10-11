<?php

namespace Database\Factories;

use App\Models\MoneyEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MoneyEntry> */
class MoneyEntryFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'type' => 'income', 'amount' => fake()->numberBetween(10000, 1000000), 'category' => 'Salary', 'description' => fake()->sentence(), 'entry_date' => fake()->date()];
    }

    public function spending(): static
    {
        return $this->state(fn (array $attributes): array => ['type' => 'spending', 'category' => 'Food']);
    }
}
