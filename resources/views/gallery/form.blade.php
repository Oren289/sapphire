<div class="space-y-4">
    <div>
        <label for="picture" class="mb-2 block text-sm font-semibold">{{ isset($picture) ? __('Replace picture (optional)') : __('Choose a picture') }}</label>
        <input id="picture" name="picture" type="file" accept="image/jpeg,image/png,image/webp" class="file-input w-full" @required(! isset($picture)) @change="selectImage($event)" aria-describedby="picture-hint">
        <p id="picture-hint" class="mt-2 text-xs text-base-content/60">{{ __('JPG, PNG or WebP. Up to 5 MB and 8000 ? 8000 pixels.') }}</p>
        <x-input-error :messages="$errors->get('picture')" class="mt-2" />
    </div>
    <div x-show="preview" style="display: none;" class="rounded-box bg-base-200 p-2">
        <img :src="preview" alt="{{ __('Square crop preview') }}" class="aspect-square w-full rounded-box object-cover">
        <p class="my-3 text-sm font-semibold">{{ __('Choose the part to keep') }}</p>
        <label for="crop-horizontal" class="block text-xs">{{ __('Move left / right') }}</label>
        <input id="crop-horizontal" type="range" min="0" max="100" x-model.number="horizontal" @input="renderPreview()" class="range range-sm my-2 w-full">
        <label for="crop-vertical" class="block text-xs">{{ __('Move up / down') }}</label>
        <input id="crop-vertical" type="range" min="0" max="100" x-model.number="vertical" @input="renderPreview()" class="range range-sm my-2 w-full">
        <label for="crop-zoom" class="block text-xs">{{ __('Zoom') }}</label>
        <input id="crop-zoom" type="range" min="1" max="3" step="0.05" x-model.number="zoom" @input="renderPreview()" class="range range-sm my-2 w-full">
        <p class="text-xs text-base-content/60">{{ __('The square preview is exactly what will be uploaded.') }}</p>
    </div>
    <p x-show="loading" style="display: none;" role="status" class="text-sm">{{ __('Preparing your picture…') }}</p>
    <p x-show="error" x-text="error" style="display: none;" role="alert" class="text-sm text-error"></p>
    <div>
        <label for="location" class="mb-2 block text-sm font-semibold">{{ __('Location') }}</label>
        <input id="location" name="location" class="input w-full" value="{{ old('location', $picture->location ?? '') }}" placeholder="{{ __('Type a place.') }}" required maxlength="120">
        <x-input-error :messages="$errors->get('location')" class="mt-2" />
    </div>
    <div>
        <label for="caption" class="mb-2 block text-sm font-semibold">{{ __('Caption') }}</label>
        <textarea id="caption" name="caption" class="textarea min-h-28 w-full" placeholder="{{ __('Tell the story behind this moment.') }}" required maxlength="2000">{{ old('caption', $picture->caption ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('caption')" class="mt-2" />
    </div>
</div>
