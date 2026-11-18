// Hide/Show header on scroll
(function() {
    var lastScrollTop = 0;
    var header = document.querySelector('.header-samsung');
    var delta = 5;
    var navbarHeight = header ? header.offsetHeight : 0;
    var didScroll = false;

    // Check if header exists
    if (!header) return;

    // Set scroll flag
    window.addEventListener('scroll', function() {
        didScroll = true;
    });

    // Check scroll direction every 250ms
    setInterval(function() {
        if (didScroll) {
            hasScrolled();
            didScroll = false;
        }
    }, 250);

    function hasScrolled() {
        var st = window.pageYOffset || document.documentElement.scrollTop;

        // Make sure they scroll more than delta
        if (Math.abs(lastScrollTop - st) <= delta) return;

        // If scrolled down and past the navbar, add class .nav-up
        if (st > lastScrollTop && st > navbarHeight) {
            // Scroll Down - Hide header
            header.classList.remove('nav-down');
            header.classList.add('nav-up');
        } else {
            // Scroll Up - Show header
            if (st + window.innerHeight < document.documentElement.scrollHeight) {
                header.classList.remove('nav-up');
                header.classList.add('nav-down');
            }
        }

        lastScrollTop = st;
    }
})();
