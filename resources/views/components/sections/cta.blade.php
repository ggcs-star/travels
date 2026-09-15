<section class="cta-section">
    <div class="container">

        <div class="cta-card">

            {{-- Background Image --}}
            <div class="cta-background">
                <img
                    src="{{ asset('images/cta/travel-cta.webp') }}"
                    alt=""
                    loading="lazy"
                    aria-hidden="true"
                >
            </div>

            <div class="cta-overlay"></div>

            {{-- Content --}}
            <div class="cta-content">

                <span class="cta-eyebrow">
                    Your next adventure awaits
                </span>

                <h2>
                    Ready to make
                    <span>memories?</span>
                </h2>

                <p>
                    Tell us where you want to go and we'll help you
                    create a journey you'll never forget.
                </p>

                <div class="cta-actions">

                    <a
                        href="{{ url('/contact') }}"
                        class="btn btn-light"
                    >
                        Start Planning
                        <span aria-hidden="true">→</span>
                    </a>

                    <a
                        href="tel:+919999999999"
                        class="cta-phone"
                    >
                        <span class="cta-phone-icon" aria-hidden="true">
                            ☎
                        </span>

                        <span>
                            <small>Talk to a travel expert</small>
                            <strong>+91 99999 99999</strong>
                        </span>
                    </a>

                </div>

            </div>

            {{-- Decorative Element --}}
            <div class="cta-decoration" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </div>

        </div>

    </div>
</section>