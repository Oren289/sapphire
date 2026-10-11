<!-- DEBUG-123 -->
<x-guest-layout>
    <div class="card relative isolate overflow-hidden rounded-3xl bg-linear-to-br from-base-300 via-secondary/20 to-accent/20 p-1">
        <div class="absolute -right-8 -top-10 h-32 w-32 rounded-full bg-primary/20 blur-2xl" aria-hidden="true"></div>
        <div class="absolute -bottom-12 -left-10 h-36 w-36 rounded-full bg-primary/20 blur-2xl" aria-hidden="true"></div>

        <div class="card-body relative rounded-[1.35rem] bg-base-100/90 p-6 backdrop-blur sm:p-8">
            <div class="mb-7 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-base-content">{{ __('Hey, welcome back!') }}</h1>
                    <p class="mt-2 text-sm leading-6 text-base-content/60">{{ __('Login to continue!') }}</p>
                </div>
                {{-- <div class="grid h-14 w-14 shrink-0 -rotate-6 place-items-center rounded-2xl bg-primary/20 text-3xl shadow-sm" aria-hidden="true">&#10022;</div> --}}
                <img src="{{ asset('images/sapphire_db.png') }}" alt="Sapphire logo"
                    class="h-15 w-auto shrink-0 object-contain -rotate-6">
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-base-content">{{ __('Email') }}</label>
                    <input id="email" class="input w-full rounded-xl border-base-300 bg-base-100 px-4 py-3 text-base-content shadow-sm outline-none transition placeholder:text-base-content/60 focus:border-primary focus:ring-4 focus:ring-primary/20" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="password" class="block text-sm font-semibold text-base-content">{{ __('Password') }}</label>
                        @if (Route::has('password.request'))
                            <a class="rounded-md text-xs font-semibold text-primary transition hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" class="input w-full rounded-xl border-base-300 bg-base-100 px-4 py-3 text-base-content shadow-sm outline-none transition placeholder:text-base-content/60 focus:border-primary focus:ring-4 focus:ring-primary/20" type="password" name="password" placeholder="********" required autocomplete="current-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <label for="remember_me" class="flex cursor-pointer items-center gap-3 rounded-xl bg-primary/20 px-4 py-3 text-sm text-base-content/60 transition hover:bg-primary/20">
                    <input id="remember_me" type="checkbox" class="checkbox checkbox-primary h-5 w-5 rounded-md border-primary/20 text-primary focus:ring-primary" name="remember">
                    <span>{{ __('Remember me') }}</span>
                    <span class="ml-auto text-base" aria-hidden="true">&#9729;</span>
                </label>

                <button class="btn btn-primary group w-full rounded-xl py-3 text-base font-bold shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:shadow-primary/20 focus:outline-none focus:ring-4 focus:ring-primary/20" type="submit">
                    {{ __('Log in') }}
                    <span class="ml-1 inline-block transition-transform group-hover:translate-x-1" aria-hidden="true">&#8594;</span>
                </button>
            </form>

            {{-- <p class="mt-6 text-center text-xs text-base-content/60">{{ __('A little progress still counts. You have got this!') }} <span aria-hidden="true">&#10047;</span></p> --}}
        </div>
    </div>
</x-guest-layout>
