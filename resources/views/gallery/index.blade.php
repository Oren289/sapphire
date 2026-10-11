<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">{{ __('Our picture gallery') }}</h1>
        {{-- <p class="mt-1 text-sm text-base-content/60">{{ __('Little moments, shared memories.') }}</p> --}}
    </x-slot>
    <div class="space-y-6">
        <section class="card border border-primary/20 bg-linear-to-br from-base-100 to-base-200 shadow-sm">
            <div class="card-body">
                <span class="badge badge-outline">{{ __('Our shared gallery') }}</span>
                <h2 class="mt-2 text-3xl font-extrabold">{{ __('Keep the moments that matter') }} <span aria-hidden="true">&#10024;</span></h2>
                <p class="text-base-content/60">{{ __('Share a picture and its story. Everyone can enjoy it; only you can change your uploads.') }}</p>
                <span class="badge badge-soft badge-info">{{ $pictures->total() }} {{ __('memories shared') }}</span>
            </div>
        </section>
        @if (session('status'))
            <p role="status" class="rounded-box bg-success/10 p-4 text-sm">{{ session('status') }}</p>
        @endif
        <x-input-error :messages="$errors->get('gallery')" />
        <form method="get" action="{{ route('gallery.index') }}" class="flex flex-col gap-3 rounded-box border border-base-300 bg-base-100 p-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div>
                <label for="filter-location" class="mb-2 block text-sm font-semibold">{{ __('Location') }}</label>
                <select id="filter-location" name="location" class="select w-full sm:w-64">
                    <option value="">{{ __('All locations') }}</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location }}" @selected(($filters['location'] ?? '') === $location)>{{ $location }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('location')" />
            </div>
            <div>
                <label for="filter-user" class="mb-2 block text-sm font-semibold">{{ __('Uploaded by') }}</label>
                <select id="filter-user" name="user_id" class="select w-full sm:w-64">
                    <option value="">{{ __('All users') }}</option>
                    @foreach ($uploaders as $uploader)
                        <option value="{{ $uploader->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $uploader->id)>{{ $uploader->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('user_id')" />
            </div>
            <button type="submit" class="btn btn-soft">{{ __('Filter pictures') }}</button>
            <a href="{{ route('gallery.index') }}" class="btn btn-ghost">{{ __('Clear filter') }}</a>
        </form>
        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-4">
            <section class="card border border-base-300 bg-base-100 shadow-sm xl:sticky xl:top-6">
                <div class="card-body">
                    <h2 class="card-title">{{ __('Share a memory') }}</h2>
                    <form method="post" action="{{ route('gallery.store') }}" enctype="multipart/form-data" class="mt-3 space-y-5" x-data="imageCrop" @submit.prevent="submit($event)">
                        @csrf
                        @include('gallery.form')
                        <button type="submit" class="btn btn-primary w-full" :disabled="busy || loading || !!error" x-text="busy ? 'Uploading…' : 'Upload picture'">{{ __('Upload picture') }}</button>
                    </form>
                </div>
            </section>
            <div class="space-y-6 xl:col-span-3">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 2xl:grid-cols-3">
                    @forelse ($pictures as $picture)
                        <article class="card overflow-hidden border border-base-300 bg-base-100 shadow-sm">
                            <figure class="aspect-square bg-base-200">
                                <a href="{{ route('gallery.image', $picture) }}" target="_blank" rel="noopener" class="h-full w-full" aria-label="{{ __('View full picture') }}">
                                    <img src="{{ route('gallery.image', $picture) }}" alt="{{ $picture->caption }}" loading="lazy" class="h-full w-full object-cover">
                                </a>
                            </figure>
                            <div class="card-body gap-3">
                                <span class="badge badge-soft badge-info h-auto max-w-full whitespace-normal break-words py-2"><span aria-hidden="true">&#128205;</span> {{ $picture->location }}</span>
                                <p class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $picture->caption }}</p>
                                <div class="border-t border-base-300 pt-3 text-xs text-base-content/60">
                                    <p>{{ __('Uploaded by') }} <span class="font-semibold text-base-content">{{ $picture->user->name }}</span></p>
                                    <time datetime="{{ $picture->created_at->toIso8601String() }}" title="{{ $picture->created_at->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA' }}">{{ $picture->created_at->diffForHumans() }}</time>
                                </div>
                                <div class="card-actions mt-1 justify-end">
                                    <a href="{{ route('gallery.download', $picture) }}" class="btn btn-sm btn-soft">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-down" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1z"/>
                                            <path fill-rule="evenodd" d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                        </svg>
                                    </a>
                                    @can('update', $picture)
                                        <a href="{{ route('gallery.edit', $picture) }}" class="btn btn-sm btn-soft">{{ __('Edit') }}</a>
                                        <form method="post" action="{{ route('gallery.destroy', $picture) }}" x-data @submit="if (! confirm('Delete this picture?')) { $event.preventDefault(); }">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-ghost text-error">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </article>
                    @empty
                        <section class="card border border-dashed border-base-300 bg-base-100 md:col-span-2 2xl:col-span-3">
                            <div class="card-body items-center py-16 text-center">
                                <span class="text-5xl text-primary" aria-hidden="true">&#9635;</span>
                                <h2 class="card-title">{{ ! empty($filters['user_id']) ? __('No pictures match these filters') : (! empty($filters['location']) ? __('No pictures at this location yet') : __('The first memory is waiting')) }}</h2>
                                <p class="text-base-content/60">{{ __('Upload a picture to start our shared scrapbook.') }}</p>
                            </div>
                        </section>
                    @endforelse
                </div>
                {{ $pictures->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
