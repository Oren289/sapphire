<section aria-labelledby="background-picture-heading">
    <h2 id="background-picture-heading" class="card-title">{{ __('Background picture') }}</h2>
    <p class="mt-2 text-sm text-base-content/60">{{ __('Put whatever you like!') }}</p>
    @include('profile.partials.picture-upload-form', ['type' => 'background', 'label' => __('Choose a background picture'), 'hint' => __('A wide landscape photo works best.')])
</section>
