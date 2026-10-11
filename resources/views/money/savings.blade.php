<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">{{ __('Savings dashboard') }}</h1>
        <p class="mt-1 text-sm text-base-content/60">{{ __('A little clarity for our next big dream.') }}</p>
    </x-slot>
    <div class="space-y-6">
        <section class="card border border-primary/20 bg-linear-to-br from-base-100 to-base-200">
            <div class="card-body"><span class="badge badge-outline">{{ __('All-time shared savings') }}</span>
                <h2 class="break-words text-4xl font-extrabold tabular-nums">Rp
                    {{ number_format($allTime['savings'], 0, ',', '.') }}</h2>
                <p class="text-base-content/60">
                    {{ __('All recorded income minus spending.') }}
                </p>
                <div class="card-actions"><a href="{{ route('money.index') }}"
                        class="btn btn-soft">{{ __('Open money tracker') }}</a></div>
            </div>
        </section>
        @include('money.filters', ['filterAction' => route('savings.index')])
        <h2 class="text-lg font-bold">{{ __('Selected period') }}</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach (['income' => __('Income'), 'spending' => __('Spending'), 'savings' => __('Net savings'), 'largest_spending' => __('Largest expense')] as $key => $label)
                <div class="stats border border-base-300 bg-base-100 shadow-sm">
                    <div class="stat min-w-0">
                        <div class="stat-title">{{ $label }}</div>
                        <div class="stat-value break-words text-2xl tabular-nums">Rp
                            {{ number_format($totals[$key], 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach
            <div class="stats border border-base-300 bg-base-100 shadow-sm">
                <div class="stat">
                    <div class="stat-title">{{ __('Savings rate') }}</div>
                    <div class="stat-value text-2xl">{{ $savingsRate === null ? '?' : $savingsRate . '%' }}</div>
                    <div class="stat-desc">{{ __('Net savings / income') }}</div>
                </div>
            </div>
            <div class="stats border border-base-300 bg-base-100 shadow-sm">
                <div class="stat">
                    <div class="stat-title">{{ __('Transactions') }}</div>
                    <div class="stat-value text-2xl">{{ $totals['count'] }}</div>
                    <div class="stat-desc">{{ __('Across all users in this period.') }}</div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">
            <section class="card border border-base-300 bg-base-100 shadow-sm xl:col-span-2">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Monthly savings') }}</h2>
                    <p class="text-xs text-base-content/60">
                        {{ __('Each row includes only the dates selected above.') }}</p>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Income') }}</th>
                                    <th>{{ __('Spending') }}</th>
                                    <th>{{ __('Net savings') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($monthly as $row)
                                    <tr>
                                        <th>{{ $row->month }}</th>
                                        <td class="whitespace-nowrap">Rp {{ number_format($row->income, 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap">Rp
                                            {{ number_format($row->spending, 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap font-bold">Rp
                                            {{ number_format($row->income - $row->spending, 0, ',', '.') }}</td>
                                </tr>@empty<tr>
                                        <td colspan="4">{{ __('No transactions in this period.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>{{ $monthly->links() }}
                </div>
            </section>
            <section class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Top spending categories') }}</h2>
                    @forelse ($categories as $category)
                        <div class="flex flex-wrap justify-between gap-2 border-b border-base-300 py-3"><span
                                class="break-words">{{ $category->category }}</span><span class="font-bold">Rp
                            {{ number_format($category->total, 0, ',', '.') }}</span></div>@empty<p
                            class="text-sm text-base-content/60">{{ __('No spending in this period.') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
