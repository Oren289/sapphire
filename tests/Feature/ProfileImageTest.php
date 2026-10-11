<?php

use App\Models\User;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Psr\Http\Message\RequestInterface;

test('users can replace their own picture without changing another account', function (string $type) {
    Storage::fake('r2');
    $column = $type.'_picture_path';
    $user = User::factory()->create([$column => 'users/old.jpg']);
    $otherUser = User::factory()->create([$column => 'users/other.jpg']);
    Storage::disk('r2')->put('users/old.jpg', 'old');
    Storage::disk('r2')->put('users/other.jpg', 'other');

    $this->actingAs($user)->post(route('profile.images.update', ['type' => $type]), [
        $type.'_image' => UploadedFile::fake()->image('photo.jpg', 600, 400),
        'user_id' => $otherUser->id,
    ])->assertRedirect(route('profile.edit'))->assertSessionHasNoErrors()
        ->assertSessionHas('status', $type.'-picture-updated');

    $path = $user->fresh()->{$column};
    expect($path)->toStartWith('users/'.$user->id.'/'.$type.'/');
    Storage::disk('r2')->assertExists($path);
    Storage::disk('r2')->assertMissing('users/old.jpg');
    Storage::disk('r2')->assertExists('users/other.jpg');
    expect($otherUser->fresh()->{$column})->toBe('users/other.jpg');
})->with(['profile', 'background']);

test('guests cannot upload or view profile images', function (string $type) {
    $this->post(route('profile.images.update', ['type' => $type]))->assertRedirect(route('login'));
    $this->get(route('profile.images.show', ['type' => $type]))->assertRedirect(route('login'));
})->with(['profile', 'background']);

test('invalid pictures are rejected without replacing an existing picture', function (string $type, string $case) {
    Storage::fake('r2');
    $column = $type.'_picture_path';
    $user = User::factory()->create([$column => 'users/old.jpg']);
    Storage::disk('r2')->put('users/old.jpg', 'old');
    $file = match ($case) {
        'missing' => null,
        'document' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        'gif' => UploadedFile::fake()->image('photo.gif'),
        'oversized' => UploadedFile::fake()->image('photo.jpg')->size(5121),
        'dimensions' => UploadedFile::fake()->image('photo.jpg', 8001, 1),
    };

    $this->actingAs($user)->from(route('profile.edit'))
        ->post(route('profile.images.update', ['type' => $type]), [$type.'_image' => $file])
        ->assertRedirect(route('profile.edit'))->assertSessionHasErrors($type.'_image');

    expect($user->fresh()->{$column})->toBe('users/old.jpg');
    Storage::disk('r2')->assertExists('users/old.jpg');
    Storage::disk('r2')->assertCount('users', 1);
})->with(['profile', 'background'])->with(['missing', 'document', 'gif', 'oversized', 'dimensions']);

test('an upload failure keeps the previous image and displays an error', function () {
    $user = User::factory()->create(['profile_picture_path' => 'users/old.jpg']);
    Storage::shouldReceive('disk')->with('r2')->once()->andReturnSelf();
    Storage::shouldReceive('putFileAs')->once()->andThrow(UnableToWriteFile::atLocation('users/new.jpg'));

    $this->actingAs($user)->post(route('profile.images.update', ['type' => 'profile']), [
        'profile_image' => UploadedFile::fake()->image('photo.jpg'),
    ])->assertRedirect(route('profile.edit'))->assertSessionHasErrors([
        'profile_image' => 'We could not save your picture. Please try again.',
    ]);

    expect($user->fresh()->profile_picture_path)->toBe('users/old.jpg');
});

test('image requests redirect to temporary private storage URLs', function () {
    $this->freezeTime();
    $user = User::factory()->create(['profile_picture_path' => 'users/photo.jpg']);
    Storage::shouldReceive('disk')->with('r2')->once()->andReturnSelf();
    Storage::shouldReceive('temporaryUrl')->with('users/photo.jpg', Mockery::on(fn ($expires) => $expires->equalTo(now()->addMinutes(10))))
        ->once()->andReturn('https://example.com/photo.jpg?signature=test');

    $this->actingAs($user)->get(route('profile.images.show', ['type' => 'profile']))
        ->assertRedirect('https://example.com/photo.jpg?signature=test')
        ->assertHeader('Cache-Control', 'no-store, private');
});

test('missing images and unknown image types return not found', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('profile.images.show', ['type' => 'profile']))->assertNotFound();
    $this->get('/profile/images/password')->assertNotFound();
    $this->post('/profile/images/password')->assertNotFound();
});

test('profile page renders uploaded images and both upload forms', function () {
    $user = User::factory()->create(['profile_picture_path' => 'profile.jpg', 'background_picture_path' => 'background.jpg']);

    $this->actingAs($user)->get(route('profile.edit'))->assertOk()
        ->assertSee(route('profile.images.show', ['type' => 'profile']))
        ->assertSee(route('profile.images.show', ['type' => 'background']))
        ->assertSee('name="profile_image"', false)->assertSee('name="background_image"', false);
});

test('deleting an account removes its stored pictures', function () {
    Storage::fake('r2');
    $user = User::factory()->create(['profile_picture_path' => 'users/profile.jpg', 'background_picture_path' => 'users/background.jpg']);
    Storage::disk('r2')->put('users/profile.jpg', 'profile');
    Storage::disk('r2')->put('users/background.jpg', 'background');

    $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertRedirect('/')->assertSessionHasNoErrors();

    Storage::disk('r2')->assertMissing(['users/profile.jpg', 'users/background.jpg']);
    $this->assertModelMissing($user);
});

test('R2 uploads omit unsupported ACL headers', function () {
    $requests = [];
    config()->set('filesystems.disks.r2.key', 'test-key');
    config()->set('filesystems.disks.r2.secret', 'test-secret');
    config()->set('filesystems.disks.r2.bucket', 'test-bucket');
    config()->set('filesystems.disks.r2.endpoint', 'https://example.r2.cloudflarestorage.com');
    config()->set('filesystems.disks.r2.http_handler', function (RequestInterface $request) use (&$requests) {
        $requests[] = $request;

        return Create::promiseFor(new Response(200, ['ETag' => 'test']));
    });
    Storage::forgetDisk('r2');

    expect(Storage::disk('r2')->put('users/photo.jpg', 'image-content'))->toBeTrue();

    expect($requests)->toHaveCount(1);
    expect($requests[0]->hasHeader('x-amz-acl'))->toBeFalse();
    expect((string) $requests[0]->getUri())->toBe('https://example.r2.cloudflarestorage.com/test-bucket/users/photo.jpg');
});
