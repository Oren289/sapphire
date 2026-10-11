<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-base-content">{{ __('Your profile') }}</h2>
        <p class="mt-1 text-sm text-base-content/60">{{ __('Your private space!') }}</p>
    </x-slot>

    <div class="space-y-6">
        <section class="card overflow-hidden border border-base-300 bg-base-100 shadow-sm" aria-label="{{ __('Profile overview') }}">
            <div class="relative h-44 bg-linear-to-br from-primary/25 via-secondary/15 to-accent/20 sm:h-56">
                @if ($user->background_picture_path)
                    <img src="{{ route('profile.images.show', ['type' => 'background']) }}" alt="{{ __('Your profile background') }}" class="h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 flex items-center justify-center text-6xl text-primary/30" aria-hidden="true">&#10022; &#9729; &#10022;</div>
                @endif
            </div>
            <div class="card-body gap-4 pt-0 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div @class(['avatar -mt-12 relative', 'avatar-placeholder' => ! $user->profile_picture_path])>
                        <div class="w-24 rounded-full bg-primary text-primary-content ring-4 ring-base-100 shadow-lg">
                            @if ($user->profile_picture_path)
                                <img src="{{ route('profile.images.show', ['type' => 'profile']) }}" alt="{{ __('Your profile picture') }}">
                            @else
                                <span class="text-3xl font-bold">{{ str($user->name)->substr(0, 1)->upper() }}</span>
                            @endif
                        </div>
                    </div>
                    <h1 class="mt-4 break-words text-2xl font-extrabold text-base-content">{{ $user->name }}</h1>
                    <p class="mt-1 break-all text-sm text-base-content/60">{{ $user->email }}</p>
                </div>
                <span class="badge badge-soft badge-info">{{ __('Be free, this is your safe space') }} <span aria-hidden="true">&#10024;</span></span>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">@include('profile.partials.update-profile-picture-form')</div>
            </div>
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">@include('profile.partials.update-background-picture-form')</div>
            </div>
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">@include('profile.partials.update-profile-information-form')</div>
            </div>
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">@include('profile.partials.update-password-form')</div>
            </div>
        </div>

        <div class="card border border-error/20 bg-base-100 shadow-sm">
            <div class="card-body">@include('profile.partials.delete-user-form')</div>
        </div>
    </div>
</x-app-layout>
