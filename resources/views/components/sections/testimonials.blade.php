<section class="testimonials-section">
    <div class="container">

        {{-- Section Header --}}
        <div class="section-header testimonials-header">

            <div>
                <span class="section-eyebrow">
                    Traveler stories
                </span>

                <h2 class="section-title">
                    Loved by travelers
                    <span>around the world.</span>
                </h2>

                <p class="section-description">
                    Real experiences from people who trusted us
                    to make their journeys special.
                </p>
            </div>

            <div class="testimonials-rating">

                <div class="testimonials-rating-score">
                    4.9
                </div>

                <div>

                    <div
                        class="testimonials-rating-stars"
                        aria-label="4.9 out of 5 stars"
                    >
                        ★★★★★
                    </div>

                    <span>
                        Based on 500+ reviews
                    </span>

                </div>

            </div>

        </div>


        {{-- Testimonials --}}
        <div class="testimonials-grid">

            {{-- Testimonial 1 --}}
            <article class="testimonial-card">

                <div class="testimonial-card-top">

                    <div class="testimonial-author">

                        <div class="testimonial-avatar">

                            <img
                                src="{{ asset('images/testimonials/avatar-1.webp') }}"
                                alt="Priya Sharma"
                                loading="lazy"
                            >

                        </div>

                        <div>

                            <h3>
                                Priya Sharma
                            </h3>

                            <span>
                                Mumbai, India
                            </span>

                        </div>

                    </div>

                    <span class="testimonial-quote" aria-hidden="true">
                        “
                    </span>

                </div>


                <div
                    class="testimonial-stars"
                    aria-label="5 out of 5 stars"
                >
                    ★★★★★
                </div>


                <blockquote>
                    “The entire Kashmir trip was beautifully planned.
                    Everything was smooth, from the hotel to the
                    sightseeing. We could simply relax and enjoy.”
                </blockquote>

                <span class="testimonial-trip">
                    Kashmir Escape · 6 Days
                </span>

            </article>


            {{-- Testimonial 2 --}}
            <article class="testimonial-card">

                <div class="testimonial-card-top">

                    <div class="testimonial-author">

                        <div class="testimonial-avatar">

                            <img
                                src="{{ asset('images/testimonials/avatar-2.webp') }}"
                                alt="Rahul Mehta"
                                loading="lazy"
                            >

                        </div>

                        <div>

                            <h3>
                                Rahul Mehta
                            </h3>

                            <span>
                                Delhi, India
                            </span>

                        </div>

                    </div>

                    <span class="testimonial-quote" aria-hidden="true">
                        “
                    </span>

                </div>


                <div
                    class="testimonial-stars"
                    aria-label="5 out of 5 stars"
                >
                    ★★★★★
                </div>


                <blockquote>
                    “From the first conversation to the end of our Goa
                    trip, the team was extremely helpful. The itinerary
                    was exactly what we wanted.”
                </blockquote>

                <span class="testimonial-trip">
                    Goa Beach Escape · 4 Days
                </span>

            </article>


            {{-- Testimonial 3 --}}
            <article class="testimonial-card">

                <div class="testimonial-card-top">

                    <div class="testimonial-author">

                        <div class="testimonial-avatar">

                            <img
                                src="{{ asset('images/testimonials/avatar-3.webp') }}"
                                alt="Ananya Kapoor"
                                loading="lazy"
                            >

                        </div>

                        <div>

                            <h3>
                                Ananya Kapoor
                            </h3>

                            <span>
                                Bengaluru, India
                            </span>

                        </div>

                    </div>

                    <span class="testimonial-quote" aria-hidden="true">
                        “
                    </span>

                </div>


                <div
                    class="testimonial-stars"
                    aria-label="5 out of 5 stars"
                >
                    ★★★★★
                </div>


                <blockquote>
                    “Our Dubai experience was amazing. The team helped
                    us choose the right package and made the whole trip
                    completely stress-free.”
                </blockquote>

                <span class="testimonial-trip">
                    Dubai Discovery · 5 Days
                </span>

            </article>

        </div>


        {{-- Navigation --}}
        <div class="testimonials-navigation">

            <button
                type="button"
                class="testimonial-nav-button"
                aria-label="Previous testimonials"
                disabled
            >
                ←
            </button>

            <div class="testimonial-progress">
                <span class="active"></span>
                <span></span>
                <span></span>
            </div>

            <button
                type="button"
                class="testimonial-nav-button"
                aria-label="Next testimonials"
            >
                →
            </button>

        </div>

    </div>
</section>  