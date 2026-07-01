(function () {
    if (typeof Swiper === 'undefined') return;

    document.querySelectorAll('.page-tiles--slider').forEach(function (block) {
        var swiperEl = block.querySelector('.swiper');
        if (!swiperEl) return;

        new Swiper(swiperEl, {
            slidesPerView: 3.25,
            spaceBetween: 40,
            navigation: {
                prevEl: block.querySelector('.slider-nav__btn--prev'),
                nextEl: block.querySelector('.slider-nav__btn--next'),
                disabledClass: 'is-disabled',
            },
            pagination: {
                el: block.querySelector('.page-tiles__progress'),
                type: 'progressbar',
            },
            breakpoints: {
                0: {
                    slidesPerView: 1.2,
                    spaceBetween: 20,
                },
                769: {
                    slidesPerView: 2.25,
                    spaceBetween: 30,
                },
                1200: {
                    slidesPerView: 3.25,
                    spaceBetween: 40,
                },
            },
        });
    });
}());
