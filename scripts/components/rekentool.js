(function () {
    'use strict';

    function parseTiers(bar) {
        var raw = bar.getAttribute('data-tiers') || '[]';
        try {
            var tiers = JSON.parse(raw);
            return Array.isArray(tiers) ? tiers : [];
        } catch (e) {
            return [];
        }
    }

    function getUnitPrice(tiers, qty) {
        var currentPrice = null;

        for (var i = 0; i < tiers.length; i++) {
            var step = parseInt(tiers[i].step, 10);
            if (step > qty) {
                break;
            }

            currentPrice = tiers[i].price;
        }

        if (currentPrice === null && tiers.length) {
            currentPrice = tiers[tiers.length - 1].price;
        }

        return currentPrice || 0;
    }

    function fmt(n) {
        var s = n.toFixed(2).replace('.', ',');
        return '\u20ac' + s;
    }

    function applyQtyToUrl(templateUrl, qty) {
        if (!templateUrl) {
            return '';
        }

        try {
            var url = new URL(templateUrl, window.location.href);
            var changed = false;

            url.searchParams.forEach(function (value, key) {
                if (value === '0') {
                    url.searchParams.set(key, String(qty));
                    changed = true;
                }
            });

            return changed ? url.toString() : templateUrl;
        } catch (e) {
            return templateUrl;
        }
    }

    document.querySelectorAll('.rekentool__bar[data-tiers]').forEach(function (bar) {
        var tiers = parseTiers(bar);
        if (!tiers.length) return;

        var qty          = 1;
        var countEl      = bar.querySelector('.rekentool__count');
        var currentPrice = bar.querySelector('.rekentool__current-price');
        var btnDec       = bar.querySelector('.rekentool__btn--decrease');
        var btnInc       = bar.querySelector('.rekentool__btn--increase');
        var qtyLinks     = bar.querySelectorAll('.rekentool__btn-wrap a[data-qty-url-template]');

        function render() {
            var price = getUnitPrice(tiers, qty);
            if (countEl) countEl.textContent = qty;
            if (currentPrice) currentPrice.textContent = fmt(price);
            if (btnDec) btnDec.disabled = qty <= 1;

            qtyLinks.forEach(function (link) {
                var templateUrl = link.getAttribute('data-qty-url-template') || link.getAttribute('href');
                var nextUrl = applyQtyToUrl(templateUrl, qty);
                if (nextUrl) {
                    link.setAttribute('href', nextUrl);
                }
            });
        }

        if (btnDec) btnDec.addEventListener('click', function () { if (qty > 1)   { qty--; render(); } });
        if (btnInc) btnInc.addEventListener('click', function () { if (qty < 999) { qty++; render(); } });

        render();
    });
}());
