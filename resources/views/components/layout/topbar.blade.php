<div class="kanila-topbar">

    <div class="kanila-topbar-inner container">

        {{-- LEFT --}}
        <div class="kanila-topbar-left">

            <a
                href="tel:{{ preg_replace('/[^0-9+]/', '', config('travels.contact.phone_primary')) }}"
                class="kanila-topbar-contact"
            >
                <span class="kanila-topbar-icon">☎</span>

                <span>
                    Call us:
                    {{ config('travels.contact.phone_primary', '+1 (202) 555-0147') }}
                </span>
            </a>

            <a
                href="mailto:{{ config('travels.contact.email', 'support@example.com') }}"
                class="kanila-topbar-contact"
            >
                <span class="kanila-topbar-icon">✉</span>

                <span>
                    {{ config('travels.contact.email', 'support@example.com') }}
                </span>
            </a>

        </div>


        {{-- RIGHT --}}
        <div class="kanila-topbar-right">

            @auth

                {{-- Logged in user --}}
                <a
                    href="{{ route('profile') }}"
                    class="kanila-user-account"
                >
                    <span class="kanila-user-icon">♙</span>

                    <span class="kanila-user-text">
                        <small>Welcome</small>
                        <strong>
                            {{ auth()->user()->name ?: auth()->user()->username ?: 'My Account' }}
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

                {{-- Guest --}}
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