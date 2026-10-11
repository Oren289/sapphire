<fieldset class="fieldset gap-3">
    <legend class="fieldset-legend">{{ __('Transaction details') }}</legend>
    <label for="type" class="font-semibold">{{ __('Type') }}</label>
    <select id="type" name="type" class="select w-full" required>
        <option value="income" @selected(old('type', $entry->type ?? 'income') === 'income')>{{ __('Income') }}</option>
        <option value="spending" @selected(old('type', $entry->type ?? '') === 'spending')>{{ __('Spending') }}</option>
    </select>
    <x-input-error :messages="$errors->get('type')" />
    <label for="amount" class="font-semibold">{{ __('Amount (Rp)') }}</label>
    <input id="amount" name="amount" type="number" min="1" max="999999999999" step="1" class="input w-full" value="{{ old('amount', $entry->amount ?? '') }}" required>
    <p class="text-xs text-base-content/60">{{ __('Enter whole rupiah without separators.') }}</p>
    <x-input-error :messages="$errors->get('amount')" />
    <label for="entry_date" class="font-semibold">{{ __('Transaction date') }}</label>
    <input id="entry_date" name="entry_date" type="date" class="input w-full" value="{{ old('entry_date', isset($entry) ? $entry->entry_date->format('Y-m-d') : now('Asia/Makassar')->format('Y-m-d')) }}" required>
    <x-input-error :messages="$errors->get('entry_date')" />
    <label for="category" class="font-semibold">{{ __('Category') }}</label>
    <input id="category" name="category" class="input w-full" placeholder="{{ __('Salary, Food, Travel?') }}" value="{{ old('category', $entry->category ?? '') }}" required maxlength="80">
    <x-input-error :messages="$errors->get('category')" />
    <label for="description" class="font-semibold">{{ __('Description / notes') }}</label>
    <textarea id="description" name="description" class="textarea min-h-24 w-full" required maxlength="2000">{{ old('description', $entry->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" />
</fieldset>
