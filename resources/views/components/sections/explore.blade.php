<section class="explore-section">
    <div class="container">

        <div class="explore-card">

            <div class="explore-heading">
                <span class="section-eyebrow">
                    Find your escape
                </span>

                <h2>
                    Where do you want to go?
                </h2>

                <p>
                    Tell us what you're looking for and discover
                    destinations made for your next adventure.
                </p>
            </div>

            <form
                action="{{ route('tours.index') }}"
                method="GET"
                class="explore-form"
            >

                {{-- Destination --}}
                <div class="explore-field">

                    <span class="explore-field-icon" aria-hidden="true">
                        ◎
                    </span>

                    <div class="explore-field-content">

                        <label for="destination">
                            Destination
                        </label>

                        <select
                            name="destination"
                            id="destination"
                        >
                            <option value="">
                                Where are you going?
                            </option>

                            <option value="kashmir">
                                Kashmir
                            </option>

                            <option value="goa">
                                Goa
                            </option>

                            <option value="manali">
                                Manali
                            </option>

                            <option value="dubai">
                                Dubai
                            </option>

                            <option value="rajasthan">
                                Rajasthan
                            </option>
                        </select>

                    </div>

                </div>


                {{-- Travel Date --}}
                <div class="explore-field">

                    <span class="explore-field-icon" aria-hidden="true">
                        ◷
                    </span>

                    <div class="explore-field-content">

                        <label for="travel_date">
                            Travel Date
                        </label>

                        <input
                            type="date"
                            name="travel_date"
                            id="travel_date"
                        >

                    </div>

                </div>


                {{-- Travelers --}}
                <div class="explore-field">

                    <span class="explore-field-icon" aria-hidden="true">
                        ♧
                    </span>

                    <div class="explore-field-content">

                        <label for="travelers">
                            Travelers
                        </label>

                        <select
                            name="travelers"
                            id="travelers"
                        >
                            <option value="">
                                Select travelers
                            </option>

                            <option value="1">
                                1 Traveler
                            </option>

                            <option value="2">
                                2 Travelers
                            </option>

                            <option value="3-5">
                                3–5 Travelers
                            </option>

                            <option value="6-10">
                                6–10 Travelers
                            </option>

                            <option value="10+">
                                10+ Travelers
                            </option>
                        </select>

                    </div>

                </div>


                {{-- Search Button --}}
                <button
                    type="submit"
                    class="explore-submit"
                >
                    <span>
                        Explore Tours
                    </span>

                    <span aria-hidden="true">
                        →
                    </span>
                </button>

            </form>

            {{-- Popular Searches --}}
            <div class="explore-popular">

                <span>
                    Popular:
                </span>

                <a href="{{ url('/tours?destination=kashmir') }}">
                    Kashmir
                </a>

                <a href="{{ url('/tours?destination=goa') }}">
                    Goa
                </a>

                <a href="{{ url('/tours?destination=dubai') }}">
                    Dubai
                </a>

                <a href="{{ url('/tours?destination=manali') }}">
                    Manali
                </a>

            </div>

        </div>

    </div>
</section>