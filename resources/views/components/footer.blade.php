<footer class="site-footer">

    <div class="container">

        {{-- Main Footer --}}
        <div class="footer-main">

            {{-- Brand --}}
            <div class="footer-brand">

                <a href="{{ url('/') }}" class="footer-logo">
                    <span class="footer-logo-mark">T</span>
                    <span>Travels</span>
                </a>

                <p class="footer-description">
                    Discover unforgettable journeys, explore incredible
                    destinations and create memories that last a lifetime.
                </p>

                {{-- Social Links --}}
                <div class="footer-socials">

                    <a
                        href="#"
                        class="footer-social-link"
                        aria-label="Facebook"
                    >
                        f
                    </a>

                    <a
                        href="#"
                        class="footer-social-link"
                        aria-label="Instagram"
                    >
                        ◎
                    </a>

                    <a
                        href="#"
                        class="footer-social-link"
                        aria-label="YouTube"
                    >
                        ▶
                    </a>

                    <a
                        href="#"
                        class="footer-social-link"
                        aria-label="WhatsApp"
                    >
                        ◉
                    </a>

                </div>

            </div>


            {{-- Quick Links --}}
            <div class="footer-column">

                <h3>Explore</h3>

                <ul>
                    <li>
                        <a href="{{ url('/') }}">Home</a>
                    </li>

                    <li>
                        <a href="{{ route('tours.index') }}">Tour Packages</a>
                    </li>

                    <li>
                        <a href="{{ url('/destinations') }}">Destinations</a>
                    </li>

                    <li>
                        <a href="{{ url('/about') }}">About Us</a>
                    </li>

                    <li>
                        <a href="{{ url('/contact') }}">Contact</a>
                    </li>
                </ul>

            </div>


            {{-- Popular Destinations --}}
            <div class="footer-column">

                <h3>Popular Destinations</h3>

                <ul>
                    <li>
                        <a href="#">Kashmir</a>
                    </li>

                    <li>
                        <a href="#">Dubai</a>
                    </li>

                    <li>
                        <a href="#">Goa</a>
                    </li>

                    <li>
                        <a href="#">Manali</a>
                    </li>

                    <li>
                        <a href="#">Rajasthan</a>
                    </li>
                </ul>

            </div>


            {{-- Contact --}}
            <div class="footer-column footer-contact">

                <h3>Get In Touch</h3>

                <div class="footer-contact-item">

                    <span class="footer-contact-icon" aria-hidden="true">
                        ☎
                    </span>

                    <div>
                        <small>Call Us</small>
                        <a href="tel:+919999999999">
                            +91 99999 99999
                        </a>
                    </div>

                </div>


                <div class="footer-contact-item">

                    <span class="footer-contact-icon" aria-hidden="true">
                        @
                    </span>

                    <div>
                        <small>Email Us</small>
                        <a href="mailto:hello@travels.com">
                            hello@travels.com
                        </a>
                    </div>

                </div>


                <div class="footer-contact-item">

                    <span class="footer-contact-icon" aria-hidden="true">
                        ◉
                    </span>

                    <div>
                        <small>Visit Us</small>
                        <span>
                            New Delhi, India
                        </span>
                    </div>

                </div>

            </div>

        </div>


        {{-- Footer Bottom --}}
        <div class="footer-bottom">

            <p>
                © {{ date('Y') }} Travels. All rights reserved.
            </p>

            <div class="footer-bottom-links">

                <a href="#">
                    Privacy Policy
                </a>

                <a href="#">
                    Terms & Conditions
                </a>

            </div>

        </div>

    </div>

</footer>