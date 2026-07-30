(function () {
    'use strict';

    var hamburger  = document.querySelector('.hamburger');
    var mobileMenu = document.getElementById('mobile-menu');
    var track      = document.getElementById('mobile-track');
    var viewport   = mobileMenu ? mobileMenu.querySelector('.mobile-menu__viewport') : null;
    var headerBar  = document.querySelector('.site-header__bar');

    if (!hamburger || !mobileMenu) return;

    function updateBarVars() {
        if (!headerBar) return;
        var rect = headerBar.getBoundingClientRect();
        document.documentElement.style.setProperty('--bar-height', Math.round(rect.height) + 'px');
        document.documentElement.style.setProperty('--header-bottom', Math.round(rect.bottom) + 'px');
    }
    updateBarVars();
    window.addEventListener('resize', updateBarVars);
    window.addEventListener('load', updateBarVars);
    if (document.fonts && document.fonts.ready && typeof document.fonts.ready.then === 'function') {
        document.fonts.ready.then(updateBarVars);
    }
    if (typeof ResizeObserver !== 'undefined' && headerBar) {
        try {
            new ResizeObserver(updateBarVars).observe(headerBar);
        } catch (e) {}
    }

    var overlay = document.createElement('div');
    overlay.className = 'mobile-overlay';
    document.body.appendChild(overlay);
    overlay.addEventListener('click', closeMenu);

    function setMenuHeight(index) {
        if (!viewport || !track) return;
        var panels = track.querySelectorAll('.mobile-menu__panel');
        if (panels[index]) {
            var content = panels[index].querySelector('.mobile-menu__content');
            if (content) {
                var barBottom = headerBar ? headerBar.getBoundingClientRect().bottom : 80;
                var maxAvailable = window.innerHeight - barBottom - 20;
                var targetHeight = Math.min(content.offsetHeight, maxAvailable);
                viewport.style.height = targetHeight + 'px';
                panels.forEach(function (p, i) {
                    p.style.height = i === index ? targetHeight + 'px' : '';
                });
            }
        }
    }

    function positionMenu() {
        if (headerBar) {
            var barBottom = headerBar.getBoundingClientRect().bottom;
            mobileMenu.style.paddingTop = Math.round(barBottom + 15) + 'px';
        }
    }

    positionMenu();

    function openMenu() {
        positionMenu();
        hamburger.classList.add('is-active');
        hamburger.setAttribute('aria-expanded', 'true');
        mobileMenu.classList.add('is-open');
        mobileMenu.setAttribute('aria-hidden', 'false');
        overlay.classList.add('is-active');
        document.body.style.overflow = 'hidden';
        if (track) track.style.transition = 'none';
        goToPanel(0);
        if (track) requestAnimationFrame(function () {
            requestAnimationFrame(function () { track.style.transition = ''; });
        });
    }

    function closeMenu() {
        hamburger.classList.remove('is-active');
        hamburger.setAttribute('aria-expanded', 'false');
        mobileMenu.classList.remove('is-open');
        mobileMenu.setAttribute('aria-hidden', 'true');
        overlay.classList.remove('is-active');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', function () {
        if (hamburger.classList.contains('is-active')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
            closeMenu();
            hamburger.focus();
        }
    });

    function goToPanel(index) {
        if (!track) return;
        track.style.transform = 'translateX(-' + (index * 100) + '%)';
        setMenuHeight(index);
    }

    document.querySelectorAll('.mobile-nav__panel-open').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var panelIndex = parseInt(btn.getAttribute('data-panel'), 10);
            goToPanel(panelIndex);
        });
    });

    document.querySelectorAll('[data-go-main]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            goToPanel(0);
        });
    });


    var headerEl = document.querySelector('.site-header');
    if (headerEl && headerBar) {
        var megaPanelsContainer = document.createElement('div');
        megaPanelsContainer.className = 'site-mega-panels';
        headerEl.insertBefore(megaPanelsContainer, headerBar.nextSibling);
        document.querySelectorAll('.site-nav__item--has-mega').forEach(function (item) {
            var mega = item.querySelector('.mega-menu');
            if (mega) {
                item._megaPanel = mega;
                megaPanelsContainer.appendChild(mega);
            }
        });
    }

    document.querySelectorAll('.site-nav__item--has-mega').forEach(function (item) {
        var closeTimer = null;
        var mega = item._megaPanel || null;
        var megaToggle = item.querySelector('.site-nav__mega-toggle');

        function openMega() {
            clearTimeout(closeTimer);
            document.querySelectorAll('.site-nav__item--has-mega').forEach(function (other) {
                if (other !== item) {
                    other.classList.remove('is-mega-open');
                    if (other._megaPanel) other._megaPanel.classList.remove('is-mega-open');
                    var otherToggle = other.querySelector('.site-nav__mega-toggle');
                    if (otherToggle) otherToggle.setAttribute('aria-expanded', 'false');
                }
            });
            item.classList.add('is-mega-open');
            if (mega) mega.classList.add('is-mega-open');
            if (megaToggle) megaToggle.setAttribute('aria-expanded', 'true');
        }

        function scheduledClose() {
            closeTimer = setTimeout(function () {
                item.classList.remove('is-mega-open');
                if (mega) mega.classList.remove('is-mega-open');
                if (megaToggle) megaToggle.setAttribute('aria-expanded', 'false');
            }, 200);
        }

        item.addEventListener('mouseenter', openMega);
        item.addEventListener('mouseleave', scheduledClose);
        item.addEventListener('focusin', openMega);
        item.addEventListener('focusout', function (e) {
            if (item.contains(e.relatedTarget)) return;
            scheduledClose();
        });

        if (mega) {
            mega.addEventListener('mouseenter', function () { clearTimeout(closeTimer); });
            mega.addEventListener('mouseleave', scheduledClose);
        }

        if (megaToggle) {
            megaToggle.addEventListener('click', function () {
                var isOpen = item.classList.contains('is-mega-open');
                if (isOpen) {
                    item.classList.remove('is-mega-open');
                    if (mega) mega.classList.remove('is-mega-open');
                    megaToggle.setAttribute('aria-expanded', 'false');
                } else {
                    openMega();
                }
            });
        }
    });

    document.querySelectorAll('.site-nav__item--has-dropdown').forEach(function (item) {
        var closeTimer = null;
        var dropdown = item.querySelector('.site-nav__dropdown');
        var dropdownToggle = item.querySelector('.site-nav__dropdown-toggle');

        function openDropdown() {
            clearTimeout(closeTimer);
            document.querySelectorAll('.site-nav__item--has-dropdown').forEach(function (other) {
                if (other !== item) {
                    other.classList.remove('is-dropdown-open');
                    var otherToggle = other.querySelector('.site-nav__dropdown-toggle');
                    if (otherToggle) otherToggle.setAttribute('aria-expanded', 'false');
                }
            });
            item.classList.add('is-dropdown-open');
            if (dropdownToggle) dropdownToggle.setAttribute('aria-expanded', 'true');
        }

        function scheduledClose() {
            closeTimer = setTimeout(function () {
                item.classList.remove('is-dropdown-open');
                if (dropdownToggle) dropdownToggle.setAttribute('aria-expanded', 'false');
            }, 200);
        }

        item.addEventListener('mouseenter', openDropdown);
        item.addEventListener('mouseleave', scheduledClose);
        item.addEventListener('focusin', openDropdown);
        item.addEventListener('focusout', function (e) {
            if (item.contains(e.relatedTarget)) return;
            scheduledClose();
        });

        if (dropdown) {
            dropdown.addEventListener('mouseenter', function () { clearTimeout(closeTimer); });
            dropdown.addEventListener('mouseleave', scheduledClose);
        }

        if (dropdownToggle) {
            dropdownToggle.addEventListener('click', function () {
                var isOpen = item.classList.contains('is-dropdown-open');
                if (isOpen) {
                    item.classList.remove('is-dropdown-open');
                    if (dropdownToggle) dropdownToggle.setAttribute('aria-expanded', 'false');
                } else {
                    openDropdown();
                }
            });
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.site-nav__item--has-dropdown') &&
            !e.target.closest('.site-nav__item--has-mega') &&
            !e.target.closest('.mega-menu')) {
            document.querySelectorAll('.site-nav__item--has-mega').forEach(function (item) {
                item.classList.remove('is-mega-open');
                if (item._megaPanel) item._megaPanel.classList.remove('is-mega-open');
                var toggle = item.querySelector('.site-nav__mega-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
            document.querySelectorAll('.site-nav__item--has-dropdown').forEach(function (item) {
                item.classList.remove('is-dropdown-open');
                var toggle = item.querySelector('.site-nav__dropdown-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            });
        }
    });

}());
