<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">{{ __('Money tracker') }}</h1><p class="mt-1 text-sm text-base-content/60">{{ __('Every little contribution counts.') }}</p></x-slot>
    <div class="space-y-6">
        <section class="card border border-primary/20 bg-linear-to-br from-base-100 to-base-200"><div class="card-body"><h2 class="text-3xl font-extrabold">{{ __('Our money, in one place') }} <span aria-hidden="true">&#10024;</span></h2><p class="text-base-content/60">{{ __('Everyone can see the shared ledger. You can change the entries you add.') }}</p><div class="card-actions"><a href="{{ route('savings.index') }}" class="btn btn-soft">{{ __('View savings dashboard') }}</a></div></div></section>
        @if (session('status'))<p role="status" class="rounded-box bg-success/10 p-4 text-sm">{{ session('status') }}</p>@endif
        @include('money.filters', ['filterAction' => route('money.index')])
        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">
            <section class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body"><h2 class="card-title">{{ __('Add an entry') }}</h2><form method="post" action="{{ route('money.store') }}" class="space-y-4">@csrf @include('money.form')<button type="submit" class="btn btn-primary w-full">{{ __('Save entry') }}</button></form></div></section>
            <div class="space-y-4 xl:col-span-2">
                @forelse ($entries as $entry)
                    <article class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body gap-3">
                        <div class="flex flex-wrap items-center justify-between gap-3"><span @class(['badge badge-soft', 'badge-success' => $entry->type === 'income', 'badge-error' => $entry->type === 'spending'])>{{ $entry->type === 'income' ? __('Income') : __('Spending') }}</span><p class="text-xl font-extrabold tabular-nums">Rp {{ number_format($entry->amount, 0, ',', '.') }}</p></div>
                        <p class="whitespace-pre-wrap break-words text-sm">{{ $entry->description }}</p>
                        <div class="flex flex-wrap gap-3 text-xs text-base-content/60"><span class="badge badge-outline">{{ $entry->category }}</span><time datetime="{{ $entry->entry_date->format('Y-m-d') }}">{{ $entry->entry_date->format('d M Y') }}</time><span>{{ __('Added by') }} {{ $entry->user->name }}</span></div>
                        @can('update', $entry)<div class="card-actions justify-end"><a class="btn btn-sm btn-soft" href="{{ route('money.edit', $entry) }}">{{ __('Edit') }}</a><form method="post" action="{{ route('money.destroy', $entry) }}" x-data @submit="if (! confirm('Delete this money entry?')) { $event.preventDefault(); }">@csrf @method('delete')<button type="submit" class="btn btn-sm btn-ghost text-error">{{ __('Delete') }}</button></form></div>@endcan
                    </div></article>
                @empty
                    <section class="card border border-dashed border-base-300 bg-base-100"><div class="card-body py-12 text-center"><h2 class="card-title justify-center">{{ __('No entries in this period') }}</h2><p class="text-base-content/60">{{ __('Add an income or spending entry, or try another period.') }}</p></div></section>
                @endforelse
                {{ $entries->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
