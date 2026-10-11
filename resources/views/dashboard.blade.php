<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-base-content">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <section x-data="elapsedTime" aria-labelledby="elapsed-time-title" class="card overflow-hidden border border-secondary/20 bg-base-100 shadow-xl shadow-secondary/10">
        <div class="card-body gap-8 p-6 sm:p-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-3">
                    <span class="badge badge-outline">Our timeline</span>
                    <h2 id="elapsed-time-title" class="text-3xl font-extrabold tracking-tight text-base-content sm:text-4xl">Look how far we've come <span aria-hidden="true">&#9825;</span></h2>
                    <p class="text-base-content/65">Every second is another adventure and absurdity in the making 😋</p>
                </div>
                <span class="badge badge-soft badge-info shrink-0">&#9679; Counting every second</span>
            </div>

            <div class="flex flex-col gap-4 rounded-box bg-secondary/10 px-5 py-4 text-base-content sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-base-content/60">It all started on</p>
                    <p class="mt-1 text-lg font-bold"><time datetime="2025-10-12T00:00:00+08:00" :datetime="timezone === 'WIB' ? '2025-10-12T00:00:00+07:00' : '2025-10-12T00:00:00+08:00'">12 October 2025</time> <span aria-hidden="true">&#127799;</span></p>
                    <p class="mt-1 text-xs text-base-content/60" x-text="timezone === 'WIB' ? 'Midnight in WIB (UTC+7)' : 'Midnight in WITA (UTC+8)'">Midnight in WITA (UTC+8)</p>
                </div>
                <div class="space-y-2">
                    <label for="counter-timezone" class="block text-xs font-bold text-base-content/70">Timezone</label>
                    <select id="counter-timezone" x-model="timezone" class="select w-full sm:w-48">
                        <option value="WIB">WIB (UTC+7)</option>
                        <option value="WITA">WITA (UTC+8)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6" role="group" aria-label="Elapsed time since 12 October 2025">
                <template x-for="unit in ['years', 'months', 'days', 'hours', 'minutes', 'seconds']" :key="unit">
                    <div class="stats min-w-0 rounded-2xl border border-base-300 bg-base-200/50 shadow-sm">
                        <div class="stat min-w-0 px-3 py-6 text-center">
                            <div class="stat-value text-4xl tabular-nums" :class="{ 'text-secondary': unit === 'years' || unit === 'hours', 'text-accent': unit === 'months' || unit === 'minutes', 'text-base-content': unit === 'days' || unit === 'seconds' }" x-text="String(elapsed[unit]).padStart(2, '0')"></div>
                            <div class="stat-title mt-2 text-xs font-bold uppercase tracking-widest" x-text="unit"></div>
                        </div>
                    </div>
                </template>
            </div>

            <p x-show="anniversary" style="display: none;" role="status" class="rounded-box bg-secondary/10 p-6 text-center text-2xl font-extrabold text-secondary sm:text-3xl">Happy anniversary!</p>

            <noscript><p class="text-base-content/70">Enable JavaScript to see the live elapsed-time counter.</p></noscript>
            <p class="text-center text-sm text-base-content/60"><span aria-hidden="true">&#10023;</span> And the story keeps growing.</p>
        </div>
    </section>
</x-app-layout>
