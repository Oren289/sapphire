<fieldset class="fieldset gap-3">
    <label for="title" class="font-semibold">{{ __('Title') }}</label>
    <input id="title" name="title" class="input w-full" placeholder="{{ __('Watch the sunrise by the sea') }}" value="{{ old('title', $item->title ?? '') }}" required maxlength="255">
    <x-input-error :messages="$errors->get('title')" />
    <label for="category" class="font-semibold">{{ __('Category') }}</label>
    <input id="category" name="category" list="dream-categories" class="input w-full" placeholder="{{ __('Travel, Food, Adventure…') }}" value="{{ old('category', $item->category ?? 'General') }}" required maxlength="80">
    <datalist id="dream-categories">
        @foreach ($categories as $category)
            <option value="{{ $category }}"></option>
        @endforeach
    </datalist>
    <p class="text-xs text-base-content/60">{{ __('Choose an existing category or type your own.') }}</p>
    <x-input-error :messages="$errors->get('category')" />
    <label for="description" class="font-semibold">{{ __('A few details (optional)') }}</label>
    <textarea id="description" name="description" class="textarea min-h-28 w-full" maxlength="2000" placeholder="{{ __('Where, when, or what makes it special?') }}">{{ old('description', $item->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" />
</fieldset>
