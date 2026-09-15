document.addEventListener('DOMContentLoaded', function () {

    const body = document.body;

    if (!body) {
        return;
    }


    /* =========================================================
       READ VISUAL SETTINGS
    ========================================================= */

    const primary = body.dataset.sitePrimary;
    const secondary = body.dataset.siteSecondary;
    const accent = body.dataset.siteAccent;
    const text = body.dataset.siteText;
    const background = body.dataset.siteBackground;

    const containerWidth = body.dataset.siteContainerWidth;
    const borderRadius = body.dataset.siteBorderRadius;

    const theme = body.dataset.theme || 'light';
    const animations = body.dataset.animations;
    const pageLoaderEnabled = body.dataset.pageLoader;
    const backToTopEnabled = body.dataset.backToTop;


    /* =========================================================
       ROOT
    ========================================================= */

    const root = document.documentElement;


    /* =========================================================
       APPLY COLORS
    ========================================================= */

    if (primary) {

        root.style.setProperty(
            '--color-primary',
            primary
        );

        root.style.setProperty(
            '--site-primary',
            primary
        );

    }


    if (secondary) {

        root.style.setProperty(
            '--color-secondary',
            secondary
        );

        root.style.setProperty(
            '--site-secondary',
            secondary
        );

    }


    if (accent) {

        root.style.setProperty(
            '--color-accent',
            accent
        );

        root.style.setProperty(
            '--site-accent',
            accent
        );

    }


    if (text) {

        root.style.setProperty(
            '--color-text',
            text
        );

        root.style.setProperty(
            '--site-text',
            text
        );

    }


    if (background) {

        root.style.setProperty(
            '--color-background',
            background
        );

        root.style.setProperty(
            '--site-background',
            background
        );

    }


    /* =========================================================
       CONTAINER WIDTH
    ========================================================= */

    if (containerWidth) {

        const width = parseInt(
            containerWidth,
            10
        );

        if (!Number.isNaN(width)) {

            const widthValue = width + 'px';

            root.style.setProperty(
                '--container-width',
                widthValue
            );

            root.style.setProperty(
                '--site-container-width',
                widthValue
            );

        }

    }


    /* =========================================================
       BORDER RADIUS
    ========================================================= */

    if (borderRadius) {

        const radius = parseInt(
            borderRadius,
            10
        );

        if (!Number.isNaN(radius)) {

            const radiusValue = radius + 'px';

            root.style.setProperty(
                '--radius-md',
                radiusValue
            );

            root.style.setProperty(
                '--site-border-radius',
                radiusValue
            );

        }

    }


    /* =========================================================
       THEME
    ========================================================= */

    let activeTheme = theme;

    if (theme === 'system') {

        const prefersDark = window.matchMedia(
            '(prefers-color-scheme: dark)'
        ).matches;

        activeTheme = prefersDark
            ? 'dark'
            : 'light';

    }


    body.setAttribute(
        'data-theme',
        activeTheme
    );

    body.setAttribute(
        'data-active-theme',
        activeTheme
    );


    /* =========================================================
       ANIMATIONS
    ========================================================= */

    if (
        animations === 'false' ||
        animations === '0' ||
        animations === 'off'
    ) {

        body.setAttribute(
            'data-animations',
            'false'
        );

    } else {

        body.setAttribute(
            'data-animations',
            'true'
        );

    }


    /* =========================================================
       PAGE LOADER
    ========================================================= */

    const pageLoader = document.getElementById(
        'site-page-loader'
    );


    if (pageLoader) {

        const hideLoader = function () {

            pageLoader.classList.add(
                'is-hidden'
            );

        };


        if (
            pageLoaderEnabled === 'true' ||
            pageLoaderEnabled === '1' ||
            pageLoaderEnabled === 'on'
        ) {

            window.addEventListener(
                'load',
                hideLoader
            );


            setTimeout(
                hideLoader,
                3000
            );

        } else {

            hideLoader();

        }

    }


    /* =========================================================
       BACK TO TOP
    ========================================================= */

    const backToTop = document.getElementById(
        'site-back-to-top'
    );


    if (backToTop) {

        const enabled =
            backToTopEnabled === 'true' ||
            backToTopEnabled === '1' ||
            backToTopEnabled === 'on';


        if (!enabled) {

            backToTop.remove();

        } else {

            const toggleBackToTop = function () {

                if (window.scrollY > 400) {

                    backToTop.classList.add(
                        'is-visible'
                    );

                } else {

                    backToTop.classList.remove(
                        'is-visible'
                    );

                }

            };


            window.addEventListener(
                'scroll',
                toggleBackToTop,
                {
                    passive: true
                }
            );


            backToTop.addEventListener(
                'click',
                function () {

                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                }
            );


            toggleBackToTop();

        }

    }

});