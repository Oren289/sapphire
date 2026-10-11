<section aria-labelledby="profile-picture-heading">
    <h2 id="profile-picture-heading" class="card-title">{{ __('Profile picture') }}</h2>
    <p class="mt-2 text-sm text-base-content/60">{{ __('Pick your best pic, or the silliest!') }}</p>
    @include('profile.partials.picture-upload-form', ['type' => 'profile', 'label' => __('Choose a profile picture'), 'hint' => __('A square photo works best.')])
</section>
