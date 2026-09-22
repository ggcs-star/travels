<section class="journey-cta-section">

    <div class="journey-cta-overlay"></div>

    <div class="container journey-cta-container">

        {{-- Trust Pills --}}
        <div class="journey-cta-pills">

            <span class="journey-cta-pill">
                75+ Years of Trust
            </span>

            <span class="journey-cta-pill">
                <span class="journey-pill-star">★</span>
                10000+ Happy Travellers
            </span>

            <span class="journey-cta-pill">
                <span class="journey-pill-icon">◉</span>
                Free Consultation
            </span>

            <span class="journey-cta-pill">
                <span class="journey-pill-lock">▣</span>
                No Commitment
            </span>

        </div>


        {{-- Logo --}}
        <div class="journey-cta-logo">

            <img
                src="{{ asset('images/logo.jpeg') }}"
                alt="{{ config('travels.brand.name') }}"
                loading="lazy"
            >

        </div>


        {{-- Heading --}}
        <h2 class="journey-cta-title">
            Ready to Begin Your Journey?
        </h2>


        {{-- Description --}}
        <p class="journey-cta-description">
            Tell us your destination, dates and budget – we will craft
            the perfect itinerary within 24 hours. Free, no commitment.
        </p>


        {{-- Main CTA Buttons --}}
        <div class="journey-cta-actions">

            {{-- Plan --}}
            <a
                href="{{ route('contact') }}"
                class="journey-cta-button journey-cta-plan"
            >
                <span class="journey-cta-button-icon">
                    ➤
                </span>

                <span class="journey-cta-button-text">
                    <small>It's free!</small>
                    <strong>Plan My Trip Now</strong>
                </span>
            </a>


            {{-- Call --}}
            <a
                href="tel:{{ preg_replace('/[^0-9+]/', '', config('travels.contact.phone_primary')) }}"
                class="journey-cta-button journey-cta-call"
            >
                <span class="journey-cta-button-icon">
                    ☎
                </span>

                <span class="journey-cta-button-text">
                    <small>Mon-Sat · 9am-7pm</small>
                    <strong>Call Us Now</strong>
                </span>
            </a>


            {{-- WhatsApp --}}
            <a
                href="#"
                class="journey-cta-button journey-cta-whatsapp"
            >
                <span class="journey-cta-button-icon">
                    ◉
                </span>

                <span class="journey-cta-button-text">
                    <small>Quick Reply!</small>
                    <strong>WhatsApp Us</strong>
                </span>
            </a>

        </div>


        {{-- Contact Strip --}}
        <div class="journey-contact-strip">

            <span>
                <b>✉</b>
                bookings@ssbtravelz.com
            </span>

            <span>
                <b>☎</b>
                +91-9182498843, +91-9014534878
            </span>

            <span>
                <b>●</b>
                Hyderabad · Vijayawada · Visakhapatnam
            </span>

        </div>

    </div>

</section>


@include('components.home.stats')