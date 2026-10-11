<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">{{ __('Edit your memory') }}</h1></x-slot>
    <section class="card mx-auto max-w-2xl overflow-hidden border border-base-300 bg-base-100 shadow-sm">
        <figure class="bg-base-200"><img src="{{ route('gallery.image', $picture) }}" alt="{{ $picture->caption }}" class="max-h-80 w-full object-contain"></figure>
        <div class="card-body">
            <h2 class="card-title">{{ __('A little touch-up') }}</h2>
            <p class="text-sm text-base-content/60">{{ __('Update the story or choose a new picture. Leave the file empty to keep the current one.') }}</p>
            <form method="post" action="{{ route('gallery.update', $picture) }}" enctype="multipart/form-data" class="mt-4 space-y-6" x-data="imageCrop" @submit.prevent="submit($event)">
                @csrf
                @method('patch')
                @include('gallery.form')
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="btn btn-primary" :disabled="busy || loading || !!error">{{ __('Save changes') }}</button>
                    <a href="{{ route('gallery.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
