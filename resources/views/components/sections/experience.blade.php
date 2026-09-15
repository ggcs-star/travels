<section class="experience-section">
    <div class="container">

        <div class="experience-grid">

            {{-- Image Side --}}
            <div class="experience-visual">

                <div class="experience-image-main">
                    <img
                        src="{{ asset('images/experience/travel-experience.webp') }}"
                        alt="Travelers enjoying a beautiful destination"
                        loading="lazy"
                    >
                </div>

                <div class="experience-image-small">
                    <img
                        src="{{ asset('images/experience/travel-detail.webp') }}"
                        alt="Beautiful travel experience"
                        loading="lazy"
                    >
                </div>

                {{-- Experience Badge --}}
                <div class="experience-badge">

                    <strong>12+</strong>

                    <span>
                        Years of
                        <br>
                        experience
                    </span>

                </div>

            </div>


            {{-- Content Side --}}
            <div class="experience-content">

                <span class="section-eyebrow">
                    Travel differently
                </span>

                <h2 class="section-title">
                    Journeys designed
                    <span>around you.</span>
                </h2>

                <p class="section-description">
                    Every traveler is different. That's why we focus on
                    creating experiences that fit your interests, your
                    pace and the way you want to explore the world.
                </p>


                {{-- Experience Points --}}
                <div class="experience-points">

                    <div class="experience-point">

                        <span class="experience-point-number">
                            01
                        </span>

                        <div>
                            <h3>
                                Discover
                            </h3>

                            <p>
                                Explore destinations and experiences
                                chosen for their unique character.
                            </p>
                        </div>

                    </div>


                    <div class="experience-point">

                        <span class="experience-point-number">
                            02
                        </span>

                        <div>
                            <h3>
                                Personalize
                            </h3>

                            <p>
                                Shape your itinerary around the things
                                you love most.
                            </p>
                        </div>

                    </div>


                    <div class="experience-point">

                        <span class="experience-point-number">
                            03
                        </span>

                        <div>
                            <h3>
                                Experience
                            </h3>

                            <p>
                                Travel with confidence while we take
                                care of the important details.
                            </p>
                        </div>

                    </div>

                </div>


                <a
                    href="{{ url('/about') }}"
                    class="text-link"
                >
                    Learn more about us
                    <span aria-hidden="true">→</span>
                </a>

            </div>

        </div>

    </div>
</section>