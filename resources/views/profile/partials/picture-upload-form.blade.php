<form method="post" action="{{ route('profile.images.update', ['type' => $type]) }}" enctype="multipart/form-data" class="mt-6 space-y-4" x-data="imagePreview">
    @csrf
    <div x-show="preview" style="display: none;" class="overflow-hidden rounded-box bg-base-200">
        <img :src="preview" alt="{{ __('Selected picture preview') }}" class="h-40 w-full object-contain">
    </div>
    <div>
        <label for="{{ $type }}_image" class="mb-2 block text-sm font-semibold">{{ $label }}</label>
        <input id="{{ $type }}_image" name="{{ $type }}_image" type="file" accept="image/jpeg,image/png,image/webp" required class="file-input w-full" @change="selectImage($event)" aria-describedby="{{ $type }}-image-hint">
        <p id="{{ $type }}-image-hint" class="mt-2 text-xs text-base-content/60">{{ $hint }} {{ __('JPG, PNG or WebP. Up to 5 MB and 8000 × 8000 pixels.') }}</p>
        <x-input-error :messages="$errors->get($type.'_image')" class="mt-2" />
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="btn btn-soft btn-secondary">{{ __('Save picture') }}</button>
        @if (session('status') === $type.'-picture-updated')
            <p role="status" class="text-sm text-success">{{ __('Picture updated!') }}</p>
        @endif
    </div>
</form>
