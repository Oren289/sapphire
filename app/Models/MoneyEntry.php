<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\MoneyEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'amount', 'category', 'description', 'entry_date'])]
class MoneyEntry extends Model
{
    /** @use HasFactory<MoneyEntryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['amount' => 'integer', 'entry_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @param array{month?: ?string, start_date?: ?string, end_date?: ?string} $filters */
    public static function forPeriod(array $filters): Builder
    {
        $query = static::query();
        if (! empty($filters['month'])) {
            $start = CarbonImmutable::createFromFormat('!Y-m', $filters['month']);
            $query->whereDate('entry_date', '>=', $start->toDateString())->whereDate('entry_date', '<=', $start->endOfMonth()->toDateString());
        }
        if (! empty($filters['start_date'])) {
            $query->whereDate('entry_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('entry_date', '<=', $filters['end_date']);
        }

        return $query;
    }

    /** @return array{income: int, spending: int, savings: int, count: int, largest_spending: int} */
    public static function totals(Builder $query): array
    {
        $totals = (clone $query)->toBase()->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS income, COALESCE(SUM(CASE WHEN type = 'spending' THEN amount ELSE 0 END), 0) AS spending, COUNT(*) AS count, COALESCE(MAX(CASE WHEN type = 'spending' THEN amount ELSE 0 END), 0) AS largest_spending")->first();

        return ['income' => (int) $totals->income, 'spending' => (int) $totals->spending, 'savings' => (int) $totals->income - (int) $totals->spending, 'count' => (int) $totals->count, 'largest_spending' => (int) $totals->largest_spending];
    }
}
