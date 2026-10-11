<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold">{{ __('Edit money entry') }}</h1></x-slot>
    <section class="card mx-auto max-w-2xl border border-base-300 bg-base-100 shadow-sm"><div class="card-body"><h2 class="card-title">{{ __('Fine-tune the details') }}</h2><form method="post" action="{{ route('money.update', $entry) }}" class="space-y-5">@csrf @method('patch') @include('money.form')<div class="flex gap-3"><button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button><a href="{{ route('money.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a></div></form></div></section>
</x-app-layout>
