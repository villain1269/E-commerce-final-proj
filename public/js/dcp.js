/* dcp.js — small shared behaviours for header menu, search bar, toast.
   Originally part of js/cart.js (buildLayout + setupMenu); now only the bits
   still needed on the Laravel server-rendered pages. */

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        // Mobile menu
        var btn = document.getElementById('menu-toggle');
        var nav = document.getElementById('main-nav');
        if (btn && nav) {
            btn.addEventListener('click', function () {
                var open = nav.classList.toggle('open');
                btn.classList.toggle('open', open);
                btn.setAttribute('aria-expanded', open);
            });
            nav.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') {
                    nav.classList.remove('open');
                    btn.classList.remove('open');
                    btn.setAttribute('aria-expanded', false);
                }
            });
        }

        // Search bar
        var sBtn = document.getElementById('search-toggle');
        var sBar = document.getElementById('search-bar');
        if (sBtn && sBar) {
            sBtn.addEventListener('click', function () {
                var show = sBar.classList.toggle('hidden') === false;
                sBtn.setAttribute('aria-expanded', show);
                if (show) {
                    var input = document.getElementById('search-input');
                    if (input) input.focus();
                }
            });
        }
    });
})();
