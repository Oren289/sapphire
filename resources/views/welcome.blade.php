<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="sapphire">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Sapphire') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/sapphire_db.png') }}">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-base-200 font-sans text-base-content antialiased">
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-5 sm:px-8 lg:px-12">
            <header class="flex flex-wrap items-center justify-between gap-4 py-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 font-extrabold tracking-tight">
                    {{-- <span class="grid size-11 place-items-center rounded-2xl bg-secondary text-2xl text-secondary-content shadow-sm" aria-hidden="true">&#9825;</span> --}}
                    <img src="{{ asset('images/sapphire_db.png') }}" alt="Sapphire logo"
                        class="h-12 w-auto shrink-0 object-contain">
                    <span class="text-xl">{{ config('app.name', 'Sapphire') }}<span class="block text-xs font-medium tracking-normal text-base-content/60">A little space for us</span></span>
                </a>
                <nav aria-label="Account navigation" class="flex flex-wrap items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-soft">Open dashboard <span aria-hidden="true">&#8594;</span></a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                        @if (Route::has('register'))
                            {{-- <a href="{{ route('register') }}" class="btn btn-soft">Create account</a> --}}
                        @endif
                    @endauth
                </nav>
            </header>

            <main class="flex-1 space-y-12 py-8 sm:py-12">
                <section class="hero overflow-hidden rounded-box border border-base-300 bg-linear-to-br from-base-100 via-base-100 to-secondary/10 shadow-sm">
                    <div class="hero-content w-full max-w-none flex-col gap-10 p-6 sm:p-10 lg:flex-row lg:gap-16 lg:p-14">
                        <div class="min-w-0 flex-1">
                            <span class="badge badge-outline">Our very own safe place</span>
                            <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">A home for our<br><span class="text-secondary">everyday adventures.</span></h1>
                            <p class="mt-5 max-w-lg text-base leading-relaxed text-base-content/65 sm:text-lg">Keep the memories, dream up what comes next, and make room for the things that matter. All together, in one cozy corner.</p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to our space <span aria-hidden="true">&#8594;</span></a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Step inside <span aria-hidden="true">&#8594;</span></a>
                                @endauth
                                <a href="#our-space" class="btn btn-ghost">Take a little look</a>
                            </div>
                        </div>
                        <div class="w-full max-w-md flex-1" aria-label="Our story began on 12 October 2025">
                            <div class="card rotate-2 border border-base-300 bg-base-100 shadow-xl shadow-secondary/10">
                                <div class="card-body items-center gap-5 py-10 text-center">
                                    <span class="grid size-20 place-items-center rounded-full bg-secondary/10 text-5xl text-secondary" aria-hidden="true">&#9825;</span>
                                    <div><p class="text-xs font-bold uppercase tracking-widest text-base-content/50">It all started on</p><p class="mt-2 text-2xl font-extrabold"><time datetime="2025-10-12">12 October 2025</time></p></div>
                                    <p class="text-sm text-base-content/60">And the story keeps growing.</p>
                                    <div class="flex flex-wrap justify-center gap-2"><span class="badge badge-soft">Memories</span><span class="badge badge-soft badge-info">Bucket list</span></div>
                                </div>
                            </div>
                            <p class="mt-6 text-center text-sm text-base-content/50"><span aria-hidden="true">&#10024;</span> Every ordinary day has a little magic.</p>
                        </div>
                    </div>
                </section>

                <section id="our-space" aria-labelledby="our-space-heading" class="scroll-mt-6 space-y-6">
                    <div class="text-center"><h2 id="our-space-heading" class="text-2xl font-extrabold sm:text-3xl">All the little things, together.</h2><p class="mt-2 text-base-content/60">A place for what we've done, and everything still to come.</p></div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <article class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body"><span class="grid size-12 place-items-center rounded-2xl bg-secondary/10 text-2xl text-secondary" aria-hidden="true">&#9201;</span><h3 class="card-title mt-2">Our timeline</h3><p class="text-sm leading-relaxed text-base-content/60">Count every year, day, and second of the story we're making.</p></div></article>
                        <article class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body"><span class="grid size-12 place-items-center rounded-2xl bg-accent/10 text-2xl text-accent" aria-hidden="true">&#10003;</span><h3 class="card-title mt-2">Shared dreams</h3><p class="text-sm leading-relaxed text-base-content/60">Build a bucket list of adventures, then celebrate each one fulfilled.</p></div></article>
                        <article class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body"><span class="grid size-12 place-items-center rounded-2xl bg-secondary/10 text-2xl text-secondary" aria-hidden="true">&#9635;</span><h3 class="card-title mt-2">Favorite moments</h3><p class="text-sm leading-relaxed text-base-content/60">Give our pictures a home, with the places and stories behind them.</p></div></article>
                        <article class="card border border-base-300 bg-base-100 shadow-sm"><div class="card-body"><span class="grid size-12 place-items-center rounded-2xl bg-accent/10 text-2xl text-accent" aria-hidden="true">&#8599;</span><h3 class="card-title mt-2">Future plans</h3><p class="text-sm leading-relaxed text-base-content/60">Track income, spending, and shared savings for our next big dream.</p></div></article>
                    </div>
                </section>
            </main>
            <footer class="border-t border-base-300 py-6 text-center text-sm text-base-content/50">{{ config('app.name', 'Sapphire') }} — Made with <span aria-hidden="true">&#9825;</span> by Ilhan</footer>
        </div>
    </body>
</html>
