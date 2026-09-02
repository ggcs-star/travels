/*
|--------------------------------------------------------------------------
| Application Initialization
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', () => {
    initializeTravelHeroSlider();
    initializeMobileNavigation();
    initializeNavigationDropdowns();
    initializeBookingForm();
});


/*
|--------------------------------------------------------------------------
| Travel Hero Background Slider
|--------------------------------------------------------------------------
*/

function initializeTravelHeroSlider() {

    const sliders = document.querySelectorAll(
        '[data-travel-hero-slider]'
    );

    if (!sliders.length) {
        return;
    }

    sliders.forEach((slider) => {

        const slides = slider.querySelectorAll(
            '.travel-hero-bg-slide'
        );

        if (slides.length <= 1) {
            return;
        }

        let currentIndex = 0;

        const slideDuration = 5000;

        /*
        |--------------------------------------------------------------------------
        | Prepare Next Image
        |--------------------------------------------------------------------------
        */

        const prepareImage = (index) => {

            const slide = slides[index];

            if (!slide) {
                return;
            }

            const image = slide.querySelector('img');

            if (!image) {
                return;
            }

            if (image.loading === 'lazy') {
                image.loading = 'eager';
            }
        };


        /*
        |--------------------------------------------------------------------------
        | Change Slide
        |--------------------------------------------------------------------------
        */

        const changeSlide = () => {

            const currentSlide =
                slides[currentIndex];

            if (currentSlide) {
                currentSlide.classList.remove(
                    'is-active'
                );
            }

            currentIndex =
                (currentIndex + 1) % slides.length;

            const nextSlide =
                slides[currentIndex];

            if (nextSlide) {
                nextSlide.classList.add(
                    'is-active'
                );
            }

            updateSliderDots(
                slider,
                currentIndex
            );

            const nextIndex =
                (currentIndex + 1) % slides.length;

            prepareImage(nextIndex);
        };


        /*
        |--------------------------------------------------------------------------
        | Slider Dots
        |--------------------------------------------------------------------------
        */

        const dotsContainer =
            slider.querySelector('.kanila-slider');

        const goToSlide = (index) => {

            const currentSlide =
                slides[currentIndex];

            if (currentSlide) {
                currentSlide.classList.remove(
                    'is-active'
                );
            }

            currentIndex = index;

            const nextSlide =
                slides[currentIndex];

            if (nextSlide) {
                nextSlide.classList.add(
                    'is-active'
                );
            }

            updateSliderDots(
                slider,
                currentIndex
            );
        };


        if (dotsContainer) {

            const dots =
                dotsContainer.querySelectorAll('button');

            dots.forEach((dot, index) => {

                dot.addEventListener(
                    'click',
                    () => {
                        goToSlide(index);
                    }
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Start Slider
        |--------------------------------------------------------------------------
        */

        window.setInterval(
            changeSlide,
            slideDuration
        );

    });
}


/*
|--------------------------------------------------------------------------
| Update Slider Dots
|--------------------------------------------------------------------------
*/

function updateSliderDots(
    slider,
    activeIndex
) {

    const dotsContainer =
        slider.querySelector('.kanila-slider');

    if (!dotsContainer) {
        return;
    }

    const dots =
        dotsContainer.querySelectorAll('button');

    dots.forEach((dot, index) => {

        const isActive =
            index === activeIndex;

        dot.classList.toggle(
            'is-active',
            isActive
        );

        dot.setAttribute(
            'aria-selected',
            String(isActive)
        );

    });
}


/*
|--------------------------------------------------------------------------
| Mobile Navigation
|--------------------------------------------------------------------------
*/

function initializeMobileNavigation() {

    const toggle =
        document.querySelector(
            '.mobile-menu-toggle'
        );

    const navigation =
        document.querySelector(
            '.kanila-mobile-navigation'
        );

    if (!toggle || !navigation) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Menu
    |--------------------------------------------------------------------------
    */

    toggle.addEventListener(
        'click',
        () => {

            const isOpen =
                navigation.classList.toggle(
                    'is-open'
                );

            toggle.setAttribute(
                'aria-expanded',
                String(isOpen)
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Close After Navigation
    |--------------------------------------------------------------------------
    */

    const links =
        navigation.querySelectorAll('a');

    links.forEach((link) => {

        link.addEventListener(
            'click',
            () => {

                navigation.classList.remove(
                    'is-open'
                );

                toggle.setAttribute(
                    'aria-expanded',
                    'false'
                );

            }
        );

    });
}


/*
|--------------------------------------------------------------------------
| Navigation Dropdowns
|--------------------------------------------------------------------------
*/

function initializeNavigationDropdowns() {

    const dropdowns =
        document.querySelectorAll(
            '.kanila-nav-dropdown'
        );

    if (!dropdowns.length) {
        return;
    }


    dropdowns.forEach((dropdown) => {

        const trigger =
            dropdown.querySelector(
                '.nav-dropdown-trigger'
            );

        if (!trigger) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Toggle Dropdown
        |--------------------------------------------------------------------------
        */

        trigger.addEventListener(
            'click',
            (event) => {

                event.preventDefault();

                const isOpen =
                    trigger.getAttribute(
                        'aria-expanded'
                    ) === 'true';


                /*
                |--------------------------------------------------------------------------
                | Close Other Dropdowns
                |--------------------------------------------------------------------------
                */

                dropdowns.forEach((item) => {

                    const itemTrigger =
                        item.querySelector(
                            '.nav-dropdown-trigger'
                        );

                    if (itemTrigger) {

                        itemTrigger.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }

                });


                /*
                |--------------------------------------------------------------------------
                | Toggle Current Dropdown
                |--------------------------------------------------------------------------
                */

                trigger.setAttribute(
                    'aria-expanded',
                    String(!isOpen)
                );

            }
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Close Dropdown On Outside Click
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        (event) => {

            if (
                event.target.closest(
                    '.kanila-nav-dropdown'
                )
            ) {
                return;
            }

            dropdowns.forEach((dropdown) => {

                const trigger =
                    dropdown.querySelector(
                        '.nav-dropdown-trigger'
                    );

                if (trigger) {

                    trigger.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Close Dropdown With Escape
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        (event) => {

            if (event.key !== 'Escape') {
                return;
            }

            dropdowns.forEach((dropdown) => {

                const trigger =
                    dropdown.querySelector(
                        '.nav-dropdown-trigger'
                    );

                if (trigger) {

                    trigger.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }

            });

        }
    );
}


/*
|--------------------------------------------------------------------------
| Booking Form
|--------------------------------------------------------------------------
|
| Handles:
|
| - Traveller add/remove
| - Available seat limit
| - Traveller count
| - Departure change
| - Price calculation
| - Booking summary
| - Preventing more travellers than available seats
|
*/

function initializeBookingForm() {

    const form =
        document.querySelector(
            '[data-booking-form]'
        );

    if (!form) {
        return;
    }


    const travellerList =
        form.querySelector(
            '[data-traveller-list]'
        );

    const addTravellerButton =
        form.querySelector(
            '[data-add-traveller]'
        );

    const travellerTemplate =
        document.getElementById(
            'traveller-template'
        );

    const departureSelect =
        form.querySelector(
            '[data-departure-select]'
        );

    const total =
        form.querySelector(
            '[data-booking-total]'
        );

    const availabilityMessage =
        form.querySelector(
            '[data-availability-message]'
        );

    const travellerCountMessage =
        form.querySelector(
            '[data-traveller-count]'
        );

    const summarySeats =
        form.querySelector(
            '[data-summary-seats]'
        );

    const summaryTravellers =
        form.querySelector(
            '[data-summary-travellers]'
        );

    const summaryDeparture =
        form.querySelector(
            '[data-summary-departure]'
        );


    /*
    |--------------------------------------------------------------------------
    | Safety Checks
    |--------------------------------------------------------------------------
    */

    if (
        !travellerList ||
        !addTravellerButton ||
        !travellerTemplate ||
        !departureSelect
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    const getTravellerCards = () => {

        return travellerList.querySelectorAll(
            '[data-traveller-card]'
        );

    };


    const getTravellerCount = () => {

        return getTravellerCards().length;

    };


    const getSelectedDeparture = () => {

        return departureSelect.selectedOptions?.[0]
            ?? null;

    };


    const getAvailableSeats = () => {

        const option =
            getSelectedDeparture();

        if (!option) {
            return 0;
        }

        const seats =
            Number(
                option.dataset.availableSeats ?? 0
            );

        return Number.isFinite(seats)
            ? Math.max(0, seats)
            : 0;

    };


    const getPrice = () => {

        const option =
            getSelectedDeparture();

        if (!option) {
            return 0;
        }

        const price =
            Number(
                option.dataset.price ?? 0
            );

        return Number.isFinite(price)
            ? Math.max(0, price)
            : 0;

    };


    /*
    |--------------------------------------------------------------------------
    | Update Traveller Numbers
    |--------------------------------------------------------------------------
    */

    const updateTravellerNumbers = () => {

        const cards =
            getTravellerCards();

        cards.forEach((card, index) => {

            const number =
                card.querySelector(
                    '[data-traveller-number]'
                );

            const remove =
                card.querySelector(
                    '[data-remove-traveller]'
                );

            if (number) {

                number.textContent =
                    String(index + 1);

            }

            /*
            |--------------------------------------------------------------------------
            | First Traveller Cannot Be Removed
            |--------------------------------------------------------------------------
            */

            if (remove) {

                remove.hidden =
                    cards.length === 1;

            }

        });

    };


    /*
    |--------------------------------------------------------------------------
    | Update Traveller Count
    |--------------------------------------------------------------------------
    */

    const updateTravellerCount = () => {

        const count =
            getTravellerCount();

        if (travellerCountMessage) {

            travellerCountMessage.textContent =
                `${count} ${
                    count === 1
                        ? 'traveller'
                        : 'travellers'
                } selected`;

        }

        if (summaryTravellers) {

            summaryTravellers.textContent =
                String(count);

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Update Available Seats
    |--------------------------------------------------------------------------
    */

    const updateAvailability = () => {

        const availableSeats =
            getAvailableSeats();

        const count =
            getTravellerCount();

        if (availabilityMessage) {

            if (availableSeats <= 0) {

                availabilityMessage.textContent =
                    'No seats available for this departure.';

            } else {

                const remainingAfterSelection =
                    Math.max(
                        0,
                        availableSeats - count
                    );

                availabilityMessage.textContent =
                    `${availableSeats} ${
                        availableSeats === 1
                            ? 'seat'
                            : 'seats'
                    } available · ${
                        remainingAfterSelection
                    } left after current selection`;

            }

        }

        if (summarySeats) {

            summarySeats.textContent =
                String(
                    Math.max(
                        0,
                        availableSeats - count
                    )
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Disable Add Button When Capacity Reached
        |--------------------------------------------------------------------------
        */

        addTravellerButton.disabled =
            count >= availableSeats
            || availableSeats <= 0;


        if (count >= availableSeats) {

            addTravellerButton.title =
                availableSeats > 0
                    ? 'All available seats have been selected.'
                    : 'No seats are available.';

        } else {

            addTravellerButton.title =
                '';

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Update Estimated Total
    |--------------------------------------------------------------------------
    */

    const updateEstimatedTotal = () => {

        const price =
            getPrice();

        const count =
            getTravellerCount();

        const calculatedTotal =
            price * count;

        if (total) {

            total.textContent =
                `₹${Math.round(
                    calculatedTotal
                ).toLocaleString('en-IN')}`;

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Update Departure Summary
    |--------------------------------------------------------------------------
    */

    const updateDepartureSummary = () => {

        const option =
            getSelectedDeparture();

        if (!option) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Departure Date
        |--------------------------------------------------------------------------
        |
        | We read the text from the selected option instead of
        | duplicating departure data into JavaScript.
        |
        */

        if (summaryDeparture) {

            const text =
                option.textContent
                    .trim()
                    .split('·')[0]
                    .trim();

            summaryDeparture.textContent =
                text;

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Remove Existing Extra Travellers
    |--------------------------------------------------------------------------
    |
    | If user changes from a departure with many seats to a departure
    | with fewer seats, remove extra traveller cards automatically.
    |
    */

    const enforceSeatLimit = () => {

        const availableSeats =
            getAvailableSeats();

        const cards =
            Array.from(
                getTravellerCards()
            );

        /*
        |--------------------------------------------------------------------------
        | At Least One Traveller
        |--------------------------------------------------------------------------
        */

        const maximumCards =
            Math.max(
                1,
                availableSeats
            );

        if (availableSeats <= 0) {

            cards.slice(1).forEach((card) => {
                card.remove();
            });

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Remove Cards Beyond Available Capacity
        |--------------------------------------------------------------------------
        */

        if (cards.length > maximumCards) {

            cards
                .slice(maximumCards)
                .forEach((card) => {
                    card.remove();
                });

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Add Traveller
    |--------------------------------------------------------------------------
    */

    addTravellerButton.addEventListener(
        'click',
        () => {

            const currentCount =
                getTravellerCount();

            const availableSeats =
                getAvailableSeats();


            /*
            |--------------------------------------------------------------------------
            | No Seats
            |--------------------------------------------------------------------------
            */

            if (availableSeats <= 0) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Capacity Reached
            |--------------------------------------------------------------------------
            */

            if (
                currentCount >=
                availableSeats
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Generate New Traveller
            |--------------------------------------------------------------------------
            */

            const index =
                currentCount;

            const markup =
                travellerTemplate.innerHTML
                    .replaceAll(
                        '__INDEX__',
                        String(index)
                    );


            travellerList.insertAdjacentHTML(
                'beforeend',
                markup
            );


            updateTravellerNumbers();
            updateTravellerCount();
            updateAvailability();
            updateEstimatedTotal();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Remove Traveller
    |--------------------------------------------------------------------------
    */

    travellerList.addEventListener(
        'click',
        (event) => {

            const button =
                event.target.closest(
                    '[data-remove-traveller]'
                );

            if (!button) {
                return;
            }


            const cards =
                getTravellerCards();


            /*
            |--------------------------------------------------------------------------
            | Never Remove Last Traveller
            |--------------------------------------------------------------------------
            */

            if (cards.length <= 1) {

                return;

            }


            const card =
                button.closest(
                    '[data-traveller-card]'
                );


            if (card) {

                card.remove();

            }


            updateTravellerNumbers();
            updateTravellerCount();
            updateAvailability();
            updateEstimatedTotal();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Departure Changed
    |--------------------------------------------------------------------------
    */

    departureSelect.addEventListener(
        'change',
        () => {

            enforceSeatLimit();

            updateTravellerNumbers();
            updateTravellerCount();
            updateAvailability();
            updateEstimatedTotal();
            updateDepartureSummary();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    enforceSeatLimit();

    updateTravellerNumbers();
    updateTravellerCount();
    updateAvailability();
    updateEstimatedTotal();
    updateDepartureSummary();

}