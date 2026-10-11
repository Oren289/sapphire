<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">{{ __('Edit your dream') }}</h1>
    </x-slot>
    <section class="card mx-auto max-w-2xl border border-base-300 bg-base-100 shadow-sm">
        <div class="card-body">
            <h2 class="card-title">{{ __('A little change of plans') }} <span aria-hidden="true">&#10024;</span></h2>
            <p class="text-sm text-base-content/60">{{ __('Fine-tune the details of your next adventure.') }}</p>
            <form method="post" action="{{ route('bucket-list.update', $item) }}" class="mt-4 space-y-6">
                @csrf
                @method('patch')
                @include('bucket-list.form')
                <div class="flex flex-wrap gap-3">
                    <button class="btn btn-primary" type="submit">{{ __('Save changes') }}</button>
                    <a class="btn btn-ghost" href="{{ route('bucket-list.index') }}">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
