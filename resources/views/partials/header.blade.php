<header class="site-header" id="top">
    <div class="container header-main">
        <a class="header-brand" href="{{ request()->routeIs('home') ? '#top' : route('home') }}" aria-label="HSB Careers home">
            <img class="header-logo" src="{{ asset('images/header/logo-hsb-v2.webp') }}" alt="HSB" width="220"
                height="79" fetchpriority="high">
            <span class="header-divider" aria-hidden="true"></span>
            <span class="header-wordmark">CAREERS</span>
        </a>

        <nav class="header-actions" aria-label="Main navigation">
            <a href="https://www.hsb.co.id/about" target="_blank" rel="noopener noreferrer">About Us</a>
            <a href="https://www.hsb.co.id/contact" target="_blank" rel="noopener noreferrer">Contact us</a>
            <a href="https://www.hsb.co.id/newsroom" target="_blank" rel="noopener noreferrer">Media Info</a>
            <a href="https://www.hsb.co.id/newsroom/awards" target="_blank" rel="noopener noreferrer">Awards</a>
        </nav>

        <button class="header-menu-toggle" type="button" aria-label="Open navigation menu"
            aria-controls="mobile-navigation" aria-expanded="false" data-mobile-nav-open>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </button>
    </div>

    <dialog class="mobile-nav-drawer" id="mobile-navigation" aria-label="Mobile navigation" data-mobile-nav>
        <div class="mobile-nav-heading">
            <span>Menu</span>
            <button class="mobile-nav-close" type="button" aria-label="Close navigation menu" data-mobile-nav-close>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 5 19 19M19 5 5 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>
        <nav class="mobile-nav-links" aria-label="Mobile navigation links">
            <a href="https://www.hsb.co.id/about" target="_blank" rel="noopener noreferrer">About Us</a>
            <a href="https://www.hsb.co.id/contact" target="_blank" rel="noopener noreferrer">Contact us</a>
            <a href="https://www.hsb.co.id/newsroom" target="_blank" rel="noopener noreferrer">Media Info</a>
            <a href="https://www.hsb.co.id/newsroom/awards" target="_blank" rel="noopener noreferrer">Awards</a>
        </nav>
    </dialog>
</header>
