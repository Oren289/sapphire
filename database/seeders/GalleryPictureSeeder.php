<?php

namespace Database\Seeders;

use App\Models\GalleryPicture;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class GalleryPictureSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();
        if ($user === null) {
            return;
        }
        for ($index = 0; $index < 3; $index++) {
            $path = UploadedFile::fake()->image('sample.jpg', 800, 600)->store('gallery/'.$user->id, 'r2');
            GalleryPicture::factory()->for($user)->create(['picture_path' => $path]);
        }
    }
}
