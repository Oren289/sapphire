const timezoneOffsets = { WIB: 7, WITA: 8 };
const startedAt = Date.UTC(2025, 9, 12);

export function isAnniversary(now = Date.now(), timezone = 'WITA') {
    const date = new Date(now + timezoneOffsets[timezone] * 60 * 60 * 1000);

    return date.getUTCMonth() === 9 && date.getUTCDate() === 12;
}

export function elapsedSinceStart(now = Date.now(), timezone = 'WITA') {
    const timezoneOffset = timezoneOffsets[timezone] * 60 * 60 * 1000;
    const currentTime = Math.max(startedAt, now + timezoneOffset);
    const currentDate = new Date(currentTime);
    let years = currentDate.getUTCFullYear() - 2025;
    let cursor = Date.UTC(2025 + years, 9, 12);

    if (cursor > currentTime) {
        years--;
        cursor = Date.UTC(2025 + years, 9, 12);
    }

    let months = 0;

    while (months < 11) {
        const nextMonth = Date.UTC(2025 + years, 9 + months + 1, 12);

        if (nextMonth > currentTime) {
            break;
        }

        months++;
        cursor = nextMonth;
    }

    const remainingSeconds = Math.floor((currentTime - cursor) / 1000);

    return {
        years,
        months,
        days: Math.floor(remainingSeconds / 86400),
        hours: Math.floor(remainingSeconds / 3600) % 24,
        minutes: Math.floor(remainingSeconds / 60) % 60,
        seconds: remainingSeconds % 60,
    };
}

export default () => ({
    timezone: 'WITA',
    elapsed: elapsedSinceStart(),
    anniversary: isAnniversary(),
    interval: null,

    init() {
        this.$watch('timezone', () => this.updateElapsed());
        this.interval = setInterval(() => {
            this.updateElapsed();
        }, 1000);
    },

    updateElapsed() {
        const now = Date.now();
        this.elapsed = elapsedSinceStart(now, this.timezone);
        this.anniversary = isAnniversary(now, this.timezone);
    },

    destroy() {
        clearInterval(this.interval);
    },
});
