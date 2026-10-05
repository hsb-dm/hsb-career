<header class="site-header" id="top">
    <div class="container header-main">
        <a class="header-brand" href="{{ request()->routeIs('home') ? '#top' : route('home') }}" aria-label="HSB Careers home">
            <img class="header-logo" src="{{ asset('images/header/logo-hsb-v2.webp') }}" alt="HSB" width="220"
                height="79" fetchpriority="high">
            <span class="header-divider" aria-hidden="true"></span>
            <span class="header-wordmark">CAREERS</span>
        </a>

        <nav class="header-actions" aria-label="Main navigation">
            <a href="{{ request()->routeIs('home') ? '#about' : route('home') . '#about' }}">About Us</a>
            <a href="https://www.hsb.co.id/contact">Contact us</a>
            <a href="https://blog.hsb.co.id/">Media Info</a>
            <a href="{{ request()->routeIs('home') ? '#awards-title' : route('home') . '#awards-title' }}">Awards</a>
        </nav>
    </div>
</header>
