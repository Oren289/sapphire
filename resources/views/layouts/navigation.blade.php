<div class="drawer lg:drawer-open">
    <input id="app-sidebar" type="checkbox" class="drawer-toggle">

    <div class="drawer-content flex min-h-screen flex-col">
        <header class="navbar sticky top-0 z-30 border-b border-base-300/70 bg-base-100/90 px-4 backdrop-blur lg:hidden">
            <div class="flex-none">
                <label for="app-sidebar" aria-label="Open navigation" class="btn btn-square btn-ghost drawer-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>
            </div>
            <div class="flex-1 px-3">
                <a href="{{ route('dashboard') }}" class="text-lg font-bold tracking-tight text-primary">
                    {{ config('app.name', 'Sapphire') }} 
                    <span aria-hidden="true">♡</span>
                </a>
            </div>
            <div class="text-xs font-medium text-base-content/60">A little space for us</div>
        </header>

        @isset($header)
            <header class="border-b border-base-300/70 bg-base-100 px-5 py-5 sm:px-8 lg:px-10">
                <div class="mx-auto w-full max-w-7xl">{{ $header }}</div>
            </header>
        @endisset

        <main class="w-full flex-1 px-4 py-6 sm:px-8 lg:px-10">
            <div class="mx-auto w-full max-w-7xl">{{ $slot }}</div>
        </main>
    </div>

    <div class="drawer-side z-40">
        <label for="app-sidebar" aria-label="Close navigation" class="drawer-overlay"></label>
        <aside
            class="flex min-h-full w-72 flex-col border-r border-base-300/70 bg-base-100 px-5 py-6 text-base-content shadow-xl lg:shadow-none">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-box px-2 py-3">
                {{-- <span
                    class="grid size-11 place-items-center rounded-2xl bg-primary text-2xl text-primary-content shadow-md shadow-primary/20"
                    aria-hidden="true">♡</span> --}}
                <img src="{{ asset('images/sapphire_db.png') }}" alt="Sapphire logo"
                    class="h-12 w-auto shrink-0 object-contain">
                <span class="min-w-0">
                    <span
                        class="block truncate text-lg font-extrabold tracking-tight">{{ config('app.name', 'Sapphire') }}</span>
                    <span class="block text-xs font-medium text-base-content/55">A little space for us</span>
                </span>
            </a>

            <div class="my-5 h-px bg-base-300"></div>

            <p class="px-3 pb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-base-content/45">Our space</p>
            <ul class="menu menu-md w-full gap-1 p-0">
                <li>
                    <a href="{{ route('dashboard') }}" @class(['active font-semibold' => request()->routeIs('dashboard')])>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z" />
                        </svg>
                        {{ __('Dashboard') }}
                        @if (request()->routeIs('dashboard'))
                            <span class="ml-auto text-primary" aria-hidden="true">✦</span>
                        @endif
                    </a>
                </li>
            </ul>
            <ul class="menu menu-md w-full p-0">
                <li>
                    <a href="{{ route('bucket-list.index') }}" @class([
                        'active font-semibold' => request()->routeIs('bucket-list.*'),
                    ])>
                        <span class="text-lg" aria-hidden="true">&#10003;</span>
                        {{ __('Bucket list') }}
                        @if (request()->routeIs('bucket-list.index'))
                            <span class="ml-auto text-primary" aria-hidden="true">✦</span>
                        @endif
                    </a>
                </li>
            </ul>

            <ul class="menu menu-md mb-5 w-full p-0">
                <li><a href="{{ route('gallery.index') }}" @class(['active font-semibold' => request()->routeIs('gallery.*')])>
                        <span aria-hidden="true">&#9635;</span>
                        {{ __('Picture gallery') }}
                        @if (request()->routeIs('gallery.index'))
                            <span class="ml-auto text-primary" aria-hidden="true">✦</span>
                        @endif
                    </a>
                </li>
            </ul>

            <ul class="menu menu-md mb-5 w-full p-0">
                <li>
                    <a href="{{ route('money.index') }}" @class(['active font-semibold' => request()->routeIs('money.*')])>
                        <span aria-hidden="true">&#128179;</span>
                        {{ __('Money tracker') }}
                        @if (request()->routeIs('money.index'))
                            <span class="ml-auto text-primary" aria-hidden="true">✦</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('savings.index') }}" @class(['active font-semibold' => request()->routeIs('savings.*')])>
                        <span aria-hidden="true">&#8599;</span>
                        {{ __('Savings dashboard') }}
                        @if (request()->routeIs('savings.index'))
                            <span class="ml-auto text-primary" aria-hidden="true">✦</span>
                        @endif
                    </a>
                </li>
            </ul>
            {{-- <div class="mt-auto rounded-box border border-secondary/20 bg-secondary/10 p-4">
                <span class="text-xl" aria-hidden="true">🌷</span>
                <p class="mt-2 text-sm font-bold">Make yourself at home</p>
                <p class="mt-1 text-xs leading-relaxed text-base-content/60">A cozy corner for all the things you’re
                    working on.</p>
            </div> --}}

            <div class="mt-auto rounded-box border border-base-300 bg-base-200/60 p-3">
                <div class="flex items-center gap-3 px-1 py-2">
                    <div @class([
                        'avatar',
                        'avatar-placeholder' => !$user->profile_picture_path,
                    ])>
                        <div class="size-10 rounded-full bg-accent text-accent-content">
                            @if ($user->profile_picture_path)
                                <img src="{{ route('profile.images.show', ['type' => 'profile']) }}"
                                    alt="{{ __('Your profile picture') }}">
                            @else
                                <span class="text-sm font-bold">{{ str($user->name)->substr(0, 1)->upper() }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold">{{ $user->name }}</p>
                        <p class="truncate text-xs text-base-content/55">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <a href="{{ route('profile.edit') }}"
                        class="btn btn-sm btn-ghost justify-start">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-sm btn-ghost w-full justify-start">{{ __('Log Out') }}</button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
</div>
