<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoneyEntryRequest;
use App\Http\Requests\MoneyFilterRequest;
use App\Models\MoneyEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MoneyEntryController extends Controller
{
    public function index(MoneyFilterRequest $request): View
    {
        $filters = $request->validated();
        $query = MoneyEntry::forPeriod($filters);

        return view('money.index', ['entries' => $query->with('user:id,name')->orderByDesc('entry_date')->orderByDesc('id')->paginate(15)->withQueryString(), 'filters' => $filters]);
    }

    public function store(MoneyEntryRequest $request): RedirectResponse
    {
        $entry = new MoneyEntry($request->validated());
        $entry->user()->associate($request->user());
        $entry->save();

        return redirect()->route('money.index')->with('status', __('Money entry added.'));
    }

    public function edit(MoneyEntry $moneyEntry): View
    {
        Gate::authorize('update', $moneyEntry);

        return view('money.edit', ['entry' => $moneyEntry]);
    }

    public function update(MoneyEntryRequest $request, MoneyEntry $moneyEntry): RedirectResponse
    {
        $moneyEntry->update($request->validated());

        return redirect()->route('money.index')->with('status', __('Money entry updated.'));
    }

    public function destroy(MoneyEntry $moneyEntry): RedirectResponse
    {
        Gate::authorize('delete', $moneyEntry);
        $moneyEntry->delete();

        return redirect()->route('money.index')->with('status', __('Money entry deleted.'));
    }
}
