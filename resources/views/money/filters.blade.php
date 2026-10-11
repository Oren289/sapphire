<form method="get" action="{{ $filterAction }}" class="card border border-base-300 bg-base-100 shadow-sm">
    <div class="card-body gap-4">
        <p class="text-sm text-base-content/60">{{ __('Choose a month or a date range. Leave both empty to see all time.') }}</p>
        <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div><label for="month" class="mb-2 block text-sm font-semibold">{{ __('Month') }}</label><input id="month" type="month" name="month" class="input w-full" value="{{ $filters['month'] ?? '' }}"><x-input-error :messages="$errors->get('month')" /></div>
            <div><label for="start_date" class="mb-2 block text-sm font-semibold">{{ __('From') }}</label><input id="start_date" type="date" name="start_date" class="input w-full" value="{{ $filters['start_date'] ?? '' }}"><x-input-error :messages="$errors->get('start_date')" /></div>
            <div><label for="end_date" class="mb-2 block text-sm font-semibold">{{ __('Through') }}</label><input id="end_date" type="date" name="end_date" class="input w-full" value="{{ $filters['end_date'] ?? '' }}"><x-input-error :messages="$errors->get('end_date')" /></div>
            <button type="submit" class="btn btn-soft">{{ __('Apply filters') }}</button>
            <a href="{{ $filterAction }}" class="btn btn-ghost">{{ __('Clear') }}</a>
        </div>
    </div>
</form>
