<?php

use App\Models\BucketListItem;
use App\Models\User;

test('all signed in users can see dreams from other users with escaped content', function () {
    $item = BucketListItem::factory()->fulfilled()->create(['title' => '<script>alert(1)</script>']);
    $viewer = User::factory()->create();

    $this->actingAs($viewer)->get(route('bucket-list.index'))->assertOk()
        ->assertSee($item->title)->assertDontSee($item->title, false)
        ->assertSee($item->user->name)->assertSee('Fulfilled')
        ->assertDontSee(route('bucket-list.edit', $item))
        ->assertDontSee(route('bucket-list.destroy', $item))
        ->assertDontSee(route('bucket-list.fulfill', $item));
});

test('users can create dreams and cannot assign ownership or fulfillment from input', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($user)->post(route('bucket-list.store'), [
        'title' => 'See the northern lights', 'description' => 'In Norway', 'category' => 'Travel',
        'user_id' => $other->id, 'is_fulfilled' => true,
    ])->assertRedirect(route('bucket-list.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('bucket_list_items', ['user_id' => $user->id, 'title' => 'See the northern lights', 'description' => 'In Norway', 'is_fulfilled' => false]);
});

test('creators can edit their dream without transferring ownership or fulfillment', function () {
    $item = BucketListItem::factory()->fulfilled()->create();
    $other = User::factory()->create();
    $this->actingAs($item->user)->get(route('bucket-list.edit', $item))->assertOk()->assertSee($item->title);

    $this->patch(route('bucket-list.update', $item), ['title' => 'A new adventure', 'description' => null, 'category' => 'Adventure', 'user_id' => $other->id, 'is_fulfilled' => false])
        ->assertRedirect(route('bucket-list.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('bucket_list_items', ['id' => $item->id, 'user_id' => $item->user_id, 'title' => 'A new adventure', 'description' => null, 'is_fulfilled' => true]);
});

test('creators can mark dreams fulfilled and reopen them', function (bool $fulfilled) {
    $item = BucketListItem::factory()->create(['is_fulfilled' => ! $fulfilled]);

    $this->actingAs($item->user)->patch(route('bucket-list.fulfill', $item), ['is_fulfilled' => $fulfilled])
        ->assertRedirect(route('bucket-list.index'))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('bucket_list_items', ['id' => $item->id, 'is_fulfilled' => $fulfilled]);
})->with([true, false]);

test('creators can delete their dreams', function () {
    $item = BucketListItem::factory()->create();

    $this->actingAs($item->user)->delete(route('bucket-list.destroy', $item))->assertRedirect(route('bucket-list.index'));

    $this->assertModelMissing($item);
});

test('other users cannot edit delete or fulfill a dream', function () {
    $item = BucketListItem::factory()->create(['title' => 'Original dream']);
    $this->actingAs(User::factory()->create());

    $this->get(route('bucket-list.edit', $item))->assertForbidden();
    $this->patch(route('bucket-list.update', $item), ['title' => 'Changed'])->assertForbidden();
    $this->patch(route('bucket-list.fulfill', $item), ['is_fulfilled' => true])->assertForbidden();
    $this->delete(route('bucket-list.destroy', $item))->assertForbidden();

    $this->assertDatabaseHas('bucket_list_items', ['id' => $item->id, 'title' => 'Original dream', 'is_fulfilled' => false]);
});

test('guests must sign in before accessing bucket list routes', function () {
    $item = BucketListItem::factory()->create();

    $this->get(route('bucket-list.index'))->assertRedirect(route('login'));
    $this->post(route('bucket-list.store'))->assertRedirect(route('login'));
    $this->get(route('bucket-list.edit', $item))->assertRedirect(route('login'));
    $this->patch(route('bucket-list.update', $item))->assertRedirect(route('login'));
    $this->patch(route('bucket-list.fulfill', $item))->assertRedirect(route('login'));
    $this->delete(route('bucket-list.destroy', $item))->assertRedirect(route('login'));
});

test('invalid dream details are rejected on creation and update', function (array $data, string $field) {
    $item = BucketListItem::factory()->create();
    $this->actingAs($item->user);

    $this->post(route('bucket-list.store'), $data)->assertSessionHasErrors($field);
    $this->patch(route('bucket-list.update', $item), $data)->assertSessionHasErrors($field);

    $this->assertDatabaseCount('bucket_list_items', 1);
    expect($item->fresh()->title)->toBe($item->title);
})->with([
    'required title' => [[], 'title'],
    'long title' => [['title' => str_repeat('a', 256)], 'title'],
    'non string title' => [['title' => ['invalid']], 'title'],
    'long description' => [['title' => 'Valid', 'description' => str_repeat('a', 2001)], 'description'],
    'non string description' => [['title' => 'Valid', 'description' => ['invalid']], 'description'],
]);

test('invalid fulfillment values leave a dream unchanged', function (array $data) {
    $item = BucketListItem::factory()->create();

    $this->actingAs($item->user)->patch(route('bucket-list.fulfill', $item), $data)->assertSessionHasErrors('is_fulfilled');

    expect($item->fresh()->is_fulfilled)->toBeFalse();
})->with(['missing' => [[]], 'invalid' => [['is_fulfilled' => 'maybe']]]);

test('the shared list paginates and shows an empty state', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get(route('bucket-list.index'))->assertOk()->assertSee('Every adventure starts with an idea');
    BucketListItem::factory()->for($user)->count(13)->create();

    $this->get(route('bucket-list.index'))->assertOk()->assertViewHas('items', fn ($items) => $items->count() === 12 && $items->total() === 13);
    $this->get(route('bucket-list.index', ['page' => 2]))->assertOk()->assertViewHas('items', fn ($items) => $items->count() === 1);
});

test('dream categories can be created and changed', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post(route('bucket-list.store'), ['title' => 'Try pottery', 'category' => 'Creative hobbies'])
        ->assertSessionHasNoErrors()->assertRedirect(route('bucket-list.index'));
    $item = BucketListItem::query()->sole();
    expect($item->category)->toBe('Creative hobbies');

    $this->patch(route('bucket-list.update', $item), ['title' => $item->title, 'category' => 'Personal growth'])
        ->assertSessionHasNoErrors()->assertRedirect(route('bucket-list.index'));

    expect($item->fresh()->category)->toBe('Personal growth');
});

test('category and status filters can be combined', function () {
    $user = User::factory()->create();
    $matched = BucketListItem::factory()->fulfilled()->create(['category' => 'Travel', 'title' => 'Matched dream']);
    BucketListItem::factory()->create(['category' => 'Travel', 'title' => 'Unfulfilled dream']);
    BucketListItem::factory()->fulfilled()->create(['category' => 'Food', 'title' => 'Other category']);

    $this->actingAs($user)->get(route('bucket-list.index', ['category' => 'Travel', 'status' => 'fulfilled']))
        ->assertOk()->assertSee($matched->title)->assertDontSee('Unfulfilled dream')->assertDontSee('Other category')
        ->assertViewHas('fulfilledCount', 1)->assertViewHas('items', fn ($items) => $items->total() === 1);
});

test('category filters persist when moving between pages', function () {
    $user = User::factory()->create();
    BucketListItem::factory()->count(13)->create(['category' => 'Travel']);
    BucketListItem::factory()->create(['category' => 'Food']);

    $this->actingAs($user)->get(route('bucket-list.index', ['category' => 'Travel']))
        ->assertOk()->assertViewHas('items', fn ($items) => $items->total() === 13 && str_contains($items->nextPageUrl(), 'category=Travel'));
    $this->get(route('bucket-list.index', ['category' => 'Travel', 'page' => 2]))
        ->assertOk()->assertViewHas('items', fn ($items) => $items->count() === 1);
});

test('unfulfilled dreams can be filtered and filters can be cleared', function () {
    $user = User::factory()->create();
    BucketListItem::factory()->create(['title' => 'Pending adventure']);
    BucketListItem::factory()->fulfilled()->create(['title' => 'Completed adventure']);

    $this->actingAs($user)->get(route('bucket-list.index', ['status' => 'dreaming']))
        ->assertOk()->assertSee('Pending adventure')->assertDontSee('Completed adventure');
    $this->get(route('bucket-list.index'))->assertOk()->assertSee('Pending adventure')->assertSee('Completed adventure');
    $this->get(route('bucket-list.index', ['category' => 'Unknown category']))->assertOk()->assertSee('No dreams match these filters');
});

test('dreams display a relative posting timestamp', function () {
    $this->freezeTime();
    $user = User::factory()->create();
    BucketListItem::factory()->create(['created_at' => now()->subMinutes(30), 'updated_at' => now()->subMinutes(5)]);

    $this->actingAs($user)->get(route('bucket-list.index'))->assertOk()->assertSee('30 minutes ago');
});

test('invalid categories are rejected on create and edit', function (mixed $category) {
    $item = BucketListItem::factory()->create();
    $this->actingAs($item->user);

    $this->post(route('bucket-list.store'), ['title' => 'Dream', 'category' => $category])->assertSessionHasErrors('category');
    $this->patch(route('bucket-list.update', $item), ['title' => 'Dream', 'category' => $category])->assertSessionHasErrors('category');

    expect($item->fresh()->category)->toBe($item->category);
})->with(['missing' => [null], 'too long' => [str_repeat('a', 81)], 'non string' => [['invalid']]]);

test('invalid filter parameters are rejected', function () {
    $this->actingAs(User::factory()->create())->get(route('bucket-list.index', ['status' => 'invalid', 'category' => ['invalid']]))
        ->assertSessionHasErrors(['status', 'category']);
});
