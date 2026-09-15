@php
    $settings = app(\App\Services\SettingsService::class);

    /*
    |--------------------------------------------------------------------------
    | Topbar Settings
    |--------------------------------------------------------------------------
    */

    $topbarEnabled = $settings->get(
        'topbar.enabled',
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Global Contact Settings
    |--------------------------------------------------------------------------
    */

    $phone = $settings->get(
        'site.phone',
        config(
            'travels.contact.phone_primary',
            '+1 (202) 555-0147'
        )
    );

    $email = $settings->get(
        'site.email',
        config(
            'travels.contact.email',
            'support@example.com'
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Topbar Labels
    |--------------------------------------------------------------------------
    */

    $phoneLabel = $settings->get(
        'topbar.phone_label',
        'Call us'
    );

    $emailLabel = $settings->get(
        'topbar.email_label',
        ''
    );

    $phoneHref = preg_replace(
        '/[^0-9+]/',
        '',
        $phone
    );
@endphp


@if($topbarEnabled)

    <div class="kanila-topbar">

        <div class="kanila-topbar-inner container">

            {{-- LEFT --}}
            <div class="kanila-topbar-left">

                @if($phone)

                    <a
                        href="tel:{{ $phoneHref }}"
                        class="kanila-topbar-contact"
                    >

                        <span class="kanila-topbar-icon">
                            ☎
                        </span>

                        <span>
                            @if($phoneLabel)
                                {{ $phoneLabel }}:
                            @endif

                            {{ $phone }}
                        </span>

                    </a>

                @endif


                @if($email)

                    <a
                        href="mailto:{{ $email }}"
                        class="kanila-topbar-contact"
                    >

                        <span class="kanila-topbar-icon">
                            ✉
                        </span>

                        <span>
                            @if($emailLabel)
                                {{ $emailLabel }}:
                            @endif

                            {{ $email }}
                        </span>

                    </a>

                @endif

            </div>


            {{-- RIGHT --}}
            <div class="kanila-topbar-right">

                @auth

                    <a
                        href="{{ route('profile') }}"
                        class="kanila-user-account"
                    >

                        <span class="kanila-user-icon">
                            ♙
                        </span>

                        <span class="kanila-user-text">

                            <small>Welcome</small>

                            <strong>
                                {{ auth()->user()->name
                                    ?: auth()->user()->username
                                    ?: 'My Account' }}
                            </strong>

                        </span>

                    </a>


                    <span class="kanila-topbar-divider"></span>


                    <a
                        href="{{ route('bookings.index') }}"
                        class="kanila-account-link"
                    >
                        My Bookings
                    </a>


                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="kanila-logout-form"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="kanila-account-link kanila-account-link--button"
                        >
                            Logout
                        </button>

                    </form>


                @else

                    <a
                        href="{{ route('login') }}"
                        class="kanila-auth-link"
                    >
                        Login
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="kanila-register-button"
                    >
                        Register
                    </a>

                @endauth

            </div>

        </div>

    </div>

@endif