<?php

namespace App\Http\Controllers;

use App\Http\Requests\BucketListItemRequest;
use App\Models\BucketListItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BucketListItemController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', Rule::in(['dreaming', 'fulfilled'])],
        ]);
        $query = BucketListItem::query();

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['status'])) {
            $query->where('is_fulfilled', $filters['status'] === 'fulfilled');
        }

        return view('bucket-list.index', [
            'items' => (clone $query)->with('user:id,name')->latest('id')->paginate(12)->withQueryString(),
            'fulfilledCount' => (clone $query)->where('is_fulfilled', true)->count(),
            'categories' => BucketListItem::query()->distinct()->orderBy('category')->pluck('category'),
            'filters' => $filters,
        ]);
    }

    public function store(BucketListItemRequest $request): RedirectResponse
    {
        $item = new BucketListItem($request->validated());
        $item->user()->associate($request->user());
        $item->save();

        return redirect()->route('bucket-list.index')->with('status', __('A new dream added to the list!'));
    }

    public function edit(BucketListItem $bucketListItem): View
    {
        Gate::authorize('update', $bucketListItem);

        return view('bucket-list.edit', [
            'item' => $bucketListItem,
            'categories' => BucketListItem::query()->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function update(BucketListItemRequest $request, BucketListItem $bucketListItem): RedirectResponse
    {
        $bucketListItem->update($request->validated());

        return redirect()->route('bucket-list.index')->with('status', __('Your bucket list item has been updated.'));
    }

    public function fulfill(Request $request, BucketListItem $bucketListItem): RedirectResponse
    {
        Gate::authorize('update', $bucketListItem);
        $request->validate(['is_fulfilled' => ['required', 'boolean']]);
        $bucketListItem->is_fulfilled = $request->boolean('is_fulfilled');
        $bucketListItem->save();

        return redirect()->route('bucket-list.index')->with('status', $bucketListItem->is_fulfilled
            ? __('Dream fulfilled! Here is to the next adventure.')
            : __('Your dream is back on the to-do list.'));
    }

    public function destroy(BucketListItem $bucketListItem): RedirectResponse
    {
        Gate::authorize('delete', $bucketListItem);
        $bucketListItem->delete();

        return redirect()->route('bucket-list.index')->with('status', __('Bucket list item deleted.'));
    }
}
