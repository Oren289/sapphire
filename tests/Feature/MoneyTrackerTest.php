<?php

use App\Models\MoneyEntry;
use App\Models\User;

test('users can create income and spending without changing ownership', function (string $type) {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $this->actingAs($user)->post(route('money.store'), ['type' => $type, 'amount' => 250000, 'category' => 'Work', 'description' => 'A detailed entry', 'entry_date' => '2026-10-10', 'user_id' => $other->id])
        ->assertRedirect(route('money.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('money_entries', ['user_id' => $user->id, 'type' => $type, 'amount' => 250000, 'description' => 'A detailed entry']);
    expect(MoneyEntry::query()->sole()->entry_date->format('Y-m-d'))->toBe('2026-10-10');
})->with(['income', 'spending']);

test('everyone signed in can see entries but only owners see controls', function () {
    $entry = MoneyEntry::factory()->create(['description' => '<script>unsafe</script>']);
    $this->actingAs(User::factory()->create())->get(route('money.index'))->assertOk()->assertSee($entry->description)->assertDontSee($entry->description, false)
        ->assertSee($entry->user->name)->assertDontSee(route('money.edit', $entry));
});

test('owners can edit and delete their money entries', function () {
    $entry = MoneyEntry::factory()->create();
    $this->actingAs($entry->user)->get(route('money.edit', $entry))->assertOk();
    $this->patch(route('money.update', $entry), ['type' => 'spending', 'amount' => 40000, 'category' => 'Food', 'description' => 'Lunch', 'entry_date' => '2026-10-10', 'user_id' => 999])
        ->assertRedirect(route('money.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('money_entries', ['id' => $entry->id, 'user_id' => $entry->user_id, 'type' => 'spending', 'amount' => 40000, 'description' => 'Lunch']);
    $this->delete(route('money.destroy', $entry))->assertRedirect(route('money.index'));
    $this->assertModelMissing($entry);
});

test('other users cannot edit or delete money entries', function () {
    $entry = MoneyEntry::factory()->create();
    $this->actingAs(User::factory()->create())->get(route('money.edit', $entry))->assertForbidden();
    $this->patch(route('money.update', $entry), [])->assertForbidden();
    $this->delete(route('money.destroy', $entry))->assertForbidden();
    $this->assertModelExists($entry);
});

test('guests cannot access money tracker or savings actions', function () {
    $entry = MoneyEntry::factory()->create();
    $this->get(route('money.index'))->assertRedirect(route('login'));
    $this->post(route('money.store'))->assertRedirect(route('login'));
    $this->get(route('money.edit', $entry))->assertRedirect(route('login'));
    $this->patch(route('money.update', $entry))->assertRedirect(route('login'));
    $this->delete(route('money.destroy', $entry))->assertRedirect(route('login'));
    $this->get(route('savings.index'))->assertRedirect(route('login'));
});

test('invalid transaction details are rejected for create and edit', function (string $field, mixed $value) {
    $entry = MoneyEntry::factory()->create();
    $data = ['type' => 'income', 'amount' => 1000, 'category' => 'Salary', 'description' => 'Work', 'entry_date' => '2026-10-10'];
    $data[$field] = $value;
    $this->actingAs($entry->user)->post(route('money.store'), $data)->assertSessionHasErrors($field);
    $this->patch(route('money.update', $entry), $data)->assertSessionHasErrors($field);
    $this->assertDatabaseCount('money_entries', 1);
    expect($entry->fresh()->amount)->toBe($entry->amount);
})->with([
    ['type', 'invalid'], ['amount', 0], ['amount', -5], ['amount', '1.5'], ['amount', '1000000000000'],
    ['category', ''], ['category', str_repeat('a', 81)], ['description', ''], ['description', str_repeat('a', 2001)], ['entry_date', '2026-02-30'],
]);

test('savings are calculated across all users and monthly filters use transaction dates', function () {
    MoneyEntry::factory()->create(['amount' => 1000000, 'entry_date' => '2026-09-30']);
    MoneyEntry::factory()->create(['amount' => 500000, 'entry_date' => '2026-10-01']);
    MoneyEntry::factory()->spending()->create(['amount' => 150000, 'category' => 'Food', 'entry_date' => '2026-10-31']);
    MoneyEntry::factory()->spending()->create(['amount' => 100000, 'category' => 'Travel', 'entry_date' => '2026-11-01']);
    $this->actingAs(User::factory()->create())->get(route('savings.index', ['month' => '2026-10']))->assertOk()
        ->assertViewHas('totals', ['income' => 500000, 'spending' => 150000, 'savings' => 350000, 'count' => 2, 'largest_spending' => 150000])
        ->assertViewHas('allTime', fn ($totals) => $totals['savings'] === 1250000)
        ->assertViewHas('savingsRate', 70.0)
        ->assertViewHas('monthly', fn ($rows) => $rows->total() === 1 && $rows->first()->month === '2026-10' && (int) $rows->first()->income === 500000)
        ->assertViewHas('categories', fn ($categories) => $categories->count() === 1 && $categories->first()->category === 'Food');
});

test('date ranges include both endpoints and exclude entries outside the range', function () {
    MoneyEntry::factory()->create(['amount' => 10, 'entry_date' => '2026-10-09']);
    MoneyEntry::factory()->create(['amount' => 100, 'entry_date' => '2026-10-10']);
    MoneyEntry::factory()->spending()->create(['amount' => 30, 'entry_date' => '2026-10-12']);
    MoneyEntry::factory()->create(['amount' => 10, 'entry_date' => '2026-10-13']);
    $filters = ['start_date' => '2026-10-10', 'end_date' => '2026-10-12'];
    $this->actingAs(User::factory()->create())->get(route('savings.index', $filters))->assertOk()->assertViewHas('totals', fn ($totals) => $totals['savings'] === 70 && $totals['count'] === 2);
    $this->get(route('money.index', $filters))->assertOk()->assertViewHas('entries', fn ($entries) => $entries->total() === 2);
});

test('empty and spending only periods display meaningful balances without division by zero', function () {
    $this->actingAs(User::factory()->create())->get(route('savings.index'))->assertOk()->assertViewHas('savingsRate', null)->assertViewHas('totals', fn ($totals) => $totals['savings'] === 0);
    MoneyEntry::factory()->spending()->create(['amount' => 123456]);
    $this->get(route('savings.index'))->assertOk()->assertViewHas('savingsRate', null)->assertViewHas('totals', fn ($totals) => $totals['savings'] === -123456);
});

test('invalid or conflicting date filters are rejected', function (array $filters, string $field) {
    $this->actingAs(User::factory()->create())->get(route('savings.index', $filters))->assertSessionHasErrors($field);
    $this->get(route('money.index', $filters))->assertSessionHasErrors($field);
})->with([
    [['month' => '2026-13'], 'month'],
    [['start_date' => '2026-02-30'], 'start_date'],
    [['start_date' => '2026-10-12', 'end_date' => '2026-10-10'], 'end_date'],
    [['month' => '2026-10', 'start_date' => '2026-10-01'], 'month'],
]);

test('transaction pagination preserves monthly filters', function () {
    MoneyEntry::factory()->count(16)->create(['entry_date' => '2026-10-10']);
    $this->actingAs(User::factory()->create())->get(route('money.index', ['month' => '2026-10']))->assertOk()
        ->assertViewHas('entries', fn ($entries) => $entries->count() === 15 && str_contains($entries->nextPageUrl(), 'month=2026-10'));
});
