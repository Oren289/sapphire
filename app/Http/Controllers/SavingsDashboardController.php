<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoneyFilterRequest;
use App\Models\MoneyEntry;
use Illuminate\View\View;

class SavingsDashboardController extends Controller
{
    public function index(MoneyFilterRequest $request): View
    {
        $filters = $request->validated();
        $query = MoneyEntry::forPeriod($filters);
        $totals = MoneyEntry::totals($query);
        $monthExpression = 'SUBSTR(CAST(entry_date AS VARCHAR), 1, 7)';

        return view('money.savings', [
            'filters' => $filters,
            'totals' => $totals,
            'allTime' => MoneyEntry::totals(MoneyEntry::query()),
            'savingsRate' => $totals['income'] > 0 ? round($totals['savings'] / $totals['income'] * 100, 1) : null,
            'monthly' => (clone $query)->toBase()->selectRaw($monthExpression." AS month, SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS income, SUM(CASE WHEN type = 'spending' THEN amount ELSE 0 END) AS spending")->groupByRaw($monthExpression)->orderByDesc('month')->paginate(12)->withQueryString(),
            'categories' => (clone $query)->where('type', 'spending')->toBase()->select('category')->selectRaw('SUM(amount) AS total')->groupBy('category')->orderByDesc('total')->limit(5)->get(),
        ]);
    }
}
