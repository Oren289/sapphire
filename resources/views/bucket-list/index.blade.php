<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">{{ __('Our bucket list') }}</h1>
        {{-- <p class="mt-1 text-sm text-base-content/60">{{ __('Big dreams, little adventures, and everything in between.') }}</p> --}}
    </x-slot>

    <div class="space-y-6">
        <section class="card border border-primary/20 bg-linear-to-br from-base-100 to-base-200 shadow-sm">
            <div class="card-body gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold">{{ __('A world of things to try') }} <span
                            aria-hidden="true">&#10024;</span></h2>
                    <p class="mt-2 text-base-content/60">
                        {{ __('Everyone can see the dreams here. You can update the ones you create.') }}</p>
                </div>
                <div class="flex flex-wrap gap-2 sm:shrink-0">
                    <span class="badge badge-lg badge-soft badge-info">{{ $items->total() }} {{ __('dreams') }}</span>
                    <span class="badge badge-lg badge-soft badge-success">{{ $fulfilledCount }}
                        {{ __('fulfilled') }}</span>
                </div>
            </div>
        </section>

        @if (session('status'))
            <p role="status" class="rounded-box border border-success/20 bg-success/10 p-4 text-sm">
                {{ session('status') }}</p>
        @endif
        <x-input-error :messages="$errors->get('is_fulfilled')" />

        <form method="get" action="{{ route('bucket-list.index') }}"
            class="flex flex-col gap-4 rounded-box border border-base-300 bg-base-100 p-5 sm:flex-row sm:items-end sm:flex-wrap">
            <div class="sm:min-w-48">
                <label for="filter-category" class="mb-2 block text-sm font-semibold">{{ __('Category') }}</label>
                <select id="filter-category" name="category" class="select w-full">
                    <option value="">{{ __('All categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category')" />
            </div>
            <div class="sm:min-w-48">
                <label for="filter-status" class="mb-2 block text-sm font-semibold">{{ __('Status') }}</label>
                <select id="filter-status" name="status" class="select w-full">
                    <option value="">{{ __('All dreams') }}</option>
                    <option value="dreaming" @selected(($filters['status'] ?? '') === 'dreaming')>{{ __('Still dreaming') }}</option>
                    <option value="fulfilled" @selected(($filters['status'] ?? '') === 'fulfilled')>{{ __('Fulfilled') }}</option>
                </select>
                <x-input-error :messages="$errors->get('status')" />
            </div>
            <button type="submit" class="btn btn-soft btn-secondary">{{ __('Filter dreams') }}</button>
            <a href="{{ route('bucket-list.index') }}" class="btn btn-ghost">{{ __('Clear filters') }}</a>
        </form>

        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">
            <section class="card border border-base-300 bg-base-100 shadow-sm xl:sticky xl:top-6">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Add a bucket list') }} <span aria-hidden="true">&#9729;</span></h2>
                    <p class="text-sm text-base-content/60">{{ __('Give your next adventure a place to begin.') }}</p>
                    <form action="{{ route('bucket-list.store') }}" method="post" class="mt-2 space-y-4">
                        @csrf
                        @include('bucket-list.form')
                        <button class="btn btn-primary w-full" type="submit">{{ __('Add to bucket list') }}</button>
                    </form>
                </div>
            </section>

            <div class="space-y-4 xl:col-span-2">
                @forelse ($items as $item)
                    <article class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body gap-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span
                                    @class([
                                        'badge badge-soft',
                                        'badge-success' => $item->is_fulfilled,
                                        'badge-info' => !$item->is_fulfilled,
                                    ])>{{ $item->is_fulfilled ? __('Fulfilled') : __('Still dreaming') }}</span>
                                <span>
                                    <time datetime="{{ $item->created_at->toIso8601String() }}"
                                        title="{{ $item->created_at->timezone('Asia/Makassar')->format('d M Y, H:i') . ' WITA' }}"
                                        class="text-xs text-base-content/60">{{ $item->created_at->diffForHumans() }}</time>
                                    <span>•</span>
                                    <span class="text-xs text-base-content/60">{{ __('Created by') }} <span
                                            class="font-semibold">{{ $item->user->name }}</span></span>
                                </span>
                            </div>
                            <h2 @class([
                                'card-title break-words',
                                'line-through text-base-content/60' => $item->is_fulfilled,
                            ])>{{ $item->title }}</h2>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="badge badge-outline">{{ $item->category }}</span>
                            </div>
                            @if ($item->description)
                            <p class="whitespace-pre-wrap break-words text-sm leading-relaxed text-base-content/70">{{ $item->description }}</p>
                            @endif
                            @can('update', $item)
                                <div
                                    class="flex flex-col gap-4 border-t border-base-300 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                    <form method="post" action="{{ route('bucket-list.fulfill', $item) }}"
                                        class="flex flex-wrap items-center gap-3" x-data>
                                        @csrf
                                        @method('patch')
                                        <input type="hidden" name="is_fulfilled" value="0">
                                        <label class="flex cursor-pointer items-center gap-2 text-sm font-semibold">
                                            <input type="checkbox" name="is_fulfilled" value="1"
                                                class="checkbox checkbox-success" @checked($item->is_fulfilled)
                                                @change="$el.form.requestSubmit()">
                                            {{ __('Dream fulfilled') }}
                                        </label>
                                        <button type="submit"
                                            class="btn btn-sm btn-ghost hidden">{{ __('Save status') }}</button>
                                    </form>
                                    <div class="flex gap-2">
                                        <a class="btn btn-sm btn-soft"
                                            href="{{ route('bucket-list.edit', $item) }}">{{ __('Edit') }}</a>
                                        <form method="post" action="{{ route('bucket-list.destroy', $item) }}" x-data
                                            @submit="if (! confirm('Delete this bucket list item?')) { $event.preventDefault(); }">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-sm btn-ghost text-error"
                                                type="submit">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </article>
                @empty
                    <section class="card border border-dashed border-base-300 bg-base-100">
                        <div class="card-body items-center py-14 text-center">
                            <span class="text-5xl text-primary" aria-hidden="true">&#10022;</span>
                            <h2 class="card-title">
                                {{ !empty($filters['category']) || !empty($filters['status']) ? __('No dreams match these filters') : __('Every adventure starts with an idea') }}
                            </h2>
                            <p class="text-base-content/60">
                                {{ !empty($filters['category']) || !empty($filters['status']) ? __('Try another category or clear the filters to see all dreams.') : __('Be the first to add a dream to our bucket list.') }}
                            </p>
                        </div>
                    </section>
                @endforelse
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
