<?php

namespace Database\Factories;

use App\Models\GalleryPicture;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<GalleryPicture> */
class GalleryPictureFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'caption' => fake()->sentence(), 'location' => fake()->city(), 'picture_path' => 'gallery/'.fake()->uuid().'.jpg'];
    }
}
