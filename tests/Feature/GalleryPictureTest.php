<?php

use App\Models\GalleryPicture;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;

test('signed in users see all pictures with escaped captions and posting timestamps', function () {
    $this->freezeTime();
    $picture = GalleryPicture::factory()->create(['caption' => '<script>alert(1)</script>', 'location' => 'Makassar', 'created_at' => now()->subMinutes(30)]);
    $this->actingAs(User::factory()->create())->get(route('gallery.index'))->assertOk()
        ->assertSee($picture->caption)->assertDontSee($picture->caption, false)->assertSee('Makassar')->assertSee($picture->user->name)
        ->assertSee('30 minutes ago')->assertSee(route('gallery.image', $picture))
        ->assertDontSee(route('gallery.edit', $picture))->assertDontSee('action="'.route('gallery.destroy', $picture).'"', false);
});

test('users upload pictures to R2 without assigning another owner', function () {
    Storage::fake('r2');
    $user = User::factory()->create();
    $other = User::factory()->create();
    $this->actingAs($user)->post(route('gallery.store'), ['picture' => UploadedFile::fake()->image('photo.jpg'), 'caption' => 'Sunset', 'location' => 'Beach', 'user_id' => $other->id])
        ->assertRedirect(route('gallery.index'))->assertSessionHasNoErrors();
    $picture = GalleryPicture::query()->sole();
    expect($picture->user_id)->toBe($user->id);
    expect($picture->caption)->toBe('Sunset');
    expect($picture->location)->toBe('Beach');
    Storage::disk('r2')->assertExists($picture->picture_path);
});

test('uploaders edit metadata without replacing the image', function () {
    $picture = GalleryPicture::factory()->create();
    $this->actingAs($picture->user)->get(route('gallery.edit', $picture))->assertOk();
    $this->patch(route('gallery.update', $picture), ['caption' => 'New caption', 'location' => 'New location', 'user_id' => 999])
        ->assertRedirect(route('gallery.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('gallery_pictures', ['id' => $picture->id, 'caption' => 'New caption', 'location' => 'New location', 'picture_path' => $picture->picture_path, 'user_id' => $picture->user_id]);
});

test('uploaders replace images and old files are removed', function () {
    Storage::fake('r2');
    $picture = GalleryPicture::factory()->create();
    Storage::disk('r2')->put($picture->picture_path, 'old');
    $this->actingAs($picture->user)->patch(route('gallery.update', $picture), ['picture' => UploadedFile::fake()->image('new.png'), 'caption' => 'Changed', 'location' => 'Home'])
        ->assertRedirect(route('gallery.index'))->assertSessionHasNoErrors();
    Storage::disk('r2')->assertMissing($picture->picture_path);
    Storage::disk('r2')->assertExists($picture->fresh()->picture_path);
});

test('uploaders delete pictures and their R2 objects', function () {
    Storage::fake('r2');
    $picture = GalleryPicture::factory()->create();
    Storage::disk('r2')->put($picture->picture_path, 'old');
    $this->actingAs($picture->user)->delete(route('gallery.destroy', $picture))->assertRedirect(route('gallery.index'));
    $this->assertModelMissing($picture);
    Storage::disk('r2')->assertMissing($picture->picture_path);
});

test('other users can view private image links but cannot change or delete uploads', function () {
    $picture = GalleryPicture::factory()->create();
    Storage::shouldReceive('disk')->with('r2')->once()->andReturnSelf();
    Storage::shouldReceive('temporaryUrl')->once()->andReturn('https://example.com/signed.jpg');
    $this->actingAs(User::factory()->create())->get(route('gallery.image', $picture))->assertRedirect('https://example.com/signed.jpg');
    $this->get(route('gallery.edit', $picture))->assertForbidden();
    $this->patch(route('gallery.update', $picture), ['caption' => 'Changed', 'location' => 'Home'])->assertForbidden();
    $this->delete(route('gallery.destroy', $picture))->assertForbidden();
    $this->assertDatabaseHas('gallery_pictures', ['id' => $picture->id, 'caption' => $picture->caption]);
});

test('guests must sign in for every gallery action', function () {
    $picture = GalleryPicture::factory()->create();
    $this->get(route('gallery.index'))->assertRedirect(route('login'));
    $this->post(route('gallery.store'))->assertRedirect(route('login'));
    $this->get(route('gallery.image', $picture))->assertRedirect(route('login'));
    $this->get(route('gallery.edit', $picture))->assertRedirect(route('login'));
    $this->patch(route('gallery.update', $picture))->assertRedirect(route('login'));
    $this->delete(route('gallery.destroy', $picture))->assertRedirect(route('login'));
});

test('invalid gallery uploads are rejected', function (string $case, string $field) {
    Storage::fake('r2');
    $data = ['caption' => 'Memory', 'location' => 'Home', 'picture' => UploadedFile::fake()->image('photo.jpg')];
    match ($case) {
        'missing image' => $data['picture'] = null,
        'document' => $data['picture'] = UploadedFile::fake()->create('file.pdf', 10, 'application/pdf'),
        'gif' => $data['picture'] = UploadedFile::fake()->image('photo.gif'),
        'large' => $data['picture'] = UploadedFile::fake()->image('photo.jpg')->size(5121),
        'dimensions' => $data['picture'] = UploadedFile::fake()->image('photo.jpg', 8001, 1),
        'caption' => $data['caption'] = '',
        'caption length' => $data['caption'] = str_repeat('a', 2001),
        'location' => $data['location'] = '',
        'location length' => $data['location'] = str_repeat('a', 121),
    };
    $this->actingAs(User::factory()->create())->post(route('gallery.store'), $data)->assertSessionHasErrors($field);
    $this->assertDatabaseCount('gallery_pictures', 0);
    Storage::disk('r2')->assertDirectoryEmpty('gallery');
})->with([
    ['missing image', 'picture'], ['document', 'picture'], ['gif', 'picture'], ['large', 'picture'], ['dimensions', 'picture'],
    ['caption', 'caption'], ['caption length', 'caption'], ['location', 'location'], ['location length', 'location'],
]);

test('failed uploads do not create gallery records', function () {
    Storage::shouldReceive('disk')->with('r2')->once()->andReturnSelf();
    Storage::shouldReceive('putFileAs')->once()->andThrow(UnableToWriteFile::atLocation('photo.jpg'));
    $this->actingAs(User::factory()->create())->post(route('gallery.store'), ['picture' => UploadedFile::fake()->image('photo.jpg'), 'caption' => 'Memory', 'location' => 'Home'])
        ->assertRedirect(route('gallery.index'))->assertSessionHasErrors('picture');
    $this->assertDatabaseCount('gallery_pictures', 0);
});

test('gallery has an empty state and paginates shared pictures', function () {
    $this->actingAs(User::factory()->create())->get(route('gallery.index'))->assertOk()->assertSee('The first memory is waiting');
    GalleryPicture::factory()->count(13)->create();
    $this->get(route('gallery.index'))->assertOk()->assertViewHas('pictures', fn ($pictures) => $pictures->count() === 12 && $pictures->total() === 13);
    $this->get(route('gallery.index', ['page' => 2]))->assertOk()->assertViewHas('pictures', fn ($pictures) => $pictures->count() === 1);
});

test('failed image replacements preserve the current picture and metadata', function () {
    $picture = GalleryPicture::factory()->create();
    Storage::shouldReceive('disk')->with('r2')->once()->andReturnSelf();
    Storage::shouldReceive('putFileAs')->once()->andThrow(UnableToWriteFile::atLocation('photo.jpg'));
    $this->actingAs($picture->user)->patch(route('gallery.update', $picture), ['picture' => UploadedFile::fake()->image('photo.jpg'), 'caption' => 'Changed', 'location' => 'Home'])
        ->assertRedirect(route('gallery.edit', $picture))->assertSessionHasErrors('picture');
    $this->assertDatabaseHas('gallery_pictures', ['id' => $picture->id, 'picture_path' => $picture->picture_path, 'caption' => $picture->caption]);
});

test('failed storage deletion keeps the gallery entry for retry', function () {
    $picture = GalleryPicture::factory()->create();
    Storage::shouldReceive('disk')->with('r2')->once()->andReturnSelf();
    Storage::shouldReceive('delete')->once()->andThrow(new RuntimeException('Storage unavailable'));
    $this->actingAs($picture->user)->delete(route('gallery.destroy', $picture))->assertRedirect(route('gallery.index'))->assertSessionHasErrors('gallery');
    $this->assertModelExists($picture);
});

test('account deletion removes its gallery entries and images', function () {
    Storage::fake('r2');
    $picture = GalleryPicture::factory()->create();
    Storage::disk('r2')->put($picture->picture_path, 'old');
    $this->actingAs($picture->user)->delete(route('profile.destroy'), ['password' => 'password'])->assertRedirect('/');
    $this->assertModelMissing($picture);
    Storage::disk('r2')->assertMissing($picture->picture_path);
});

test('location filters select matching pictures and persist through pagination', function () {
    GalleryPicture::factory()->count(13)->create(['location' => 'Beach']);
    GalleryPicture::factory()->create(['location' => 'Mountains', 'caption' => 'Mountain memory']);
    $this->actingAs(User::factory()->create())->get(route('gallery.index', ['location' => 'Beach']))
        ->assertOk()->assertDontSee('Mountain memory')
        ->assertViewHas('pictures', fn ($pictures) => $pictures->total() === 13 && str_contains($pictures->nextPageUrl(), 'location=Beach'));
    $this->get(route('gallery.index', ['location' => 'Beach', 'page' => 2]))->assertOk()->assertViewHas('pictures', fn ($pictures) => $pictures->count() === 1);
    $this->get(route('gallery.index', ['location' => 'Unknown']))->assertOk()->assertSee('No pictures at this location yet');
    $this->get(route('gallery.index'))->assertOk()->assertSee('Mountain memory');
});

test('invalid location filters are rejected', function () {
    $this->actingAs(User::factory()->create())->get(route('gallery.index', ['location' => ['invalid']]))->assertSessionHasErrors('location');
});

test('all signed in users can download an image with attachment disposition', function () {
    $this->freezeTime();
    $picture = GalleryPicture::factory()->create(['picture_path' => 'gallery/photo.jpg']);
    Storage::shouldReceive('disk')->with('r2')->once()->andReturnSelf();
    Storage::shouldReceive('temporaryUrl')->with('gallery/photo.jpg', Mockery::on(fn ($expires) => $expires->equalTo(now()->addMinutes(10))), [
        'ResponseContentDisposition' => 'attachment; filename="memory-'.$picture->id.'.jpg"',
    ])->once()->andReturn('https://example.com/download.jpg');
    $this->actingAs(User::factory()->create())->get(route('gallery.download', $picture))
        ->assertRedirect('https://example.com/download.jpg')->assertHeader('Cache-Control', 'no-store, private');
});

test('guests cannot download gallery images', function () {
    $picture = GalleryPicture::factory()->create();
    $this->get(route('gallery.download', $picture))->assertRedirect(route('login'));
});

test('uploader and location filters combine and persist across pages', function () {
    $uploader = User::factory()->create();
    GalleryPicture::factory()->for($uploader)->count(13)->create(['location' => 'Beach']);
    GalleryPicture::factory()->for($uploader)->create(['location' => 'Home', 'caption' => 'Different location']);
    GalleryPicture::factory()->create(['location' => 'Beach', 'caption' => 'Different uploader']);

    $this->actingAs(User::factory()->create())->get(route('gallery.index', ['user_id' => $uploader->id, 'location' => 'Beach']))
        ->assertOk()->assertDontSee('Different location')->assertDontSee('Different uploader')
        ->assertViewHas('pictures', fn ($pictures) => $pictures->total() === 13 && str_contains($pictures->nextPageUrl(), 'user_id='.$uploader->id) && str_contains($pictures->nextPageUrl(), 'location=Beach'));
    $this->get(route('gallery.index', ['user_id' => $uploader->id, 'location' => 'Beach', 'page' => 2]))
        ->assertOk()->assertViewHas('pictures', fn ($pictures) => $pictures->count() === 1);
    $this->get(route('gallery.index', ['user_id' => $uploader->id]))
        ->assertOk()->assertViewHas('pictures', fn ($pictures) => $pictures->total() === 14);
});

test('invalid uploader filters are rejected', function (mixed $userId) {
    $this->actingAs(User::factory()->create())->get(route('gallery.index', ['user_id' => $userId]))->assertSessionHasErrors('user_id');
})->with(['unknown user' => [999999], 'non integer' => ['invalid'], 'array' => [['invalid']]]);

test('uploader dropdown only lists users with pictures', function () {
    $picture = GalleryPicture::factory()->create();
    $viewer = User::factory()->create();
    $this->actingAs($viewer)->get(route('gallery.index'))->assertOk()
        ->assertViewHas('uploaders', fn ($uploaders) => $uploaders->modelKeys() === [$picture->user_id]);
    $this->get(route('gallery.index', ['user_id' => $viewer->id]))->assertOk()->assertSee('No pictures match these filters');
});
