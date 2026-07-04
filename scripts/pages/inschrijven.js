(function () {
    'use strict';

    var cat = document.getElementById('inschr-filter-cat');
    var cursus = document.getElementById('inschr-filter-cursus');
    var cardsContainer = document.querySelector('[data-inschr-cards]');
    var placeholder = document.querySelector('[data-inschr-placeholder]');
    var emptyState = document.querySelector('[data-inschr-empty-state]');
    var ajaxConfig = window.arehboInschrijven || {};
    var loadedCategory = '';
    var cardsLoaded = false;
    var isLoading = false;
    var initialCategory = '';

    if (!cat || !cursus || !cardsContainer) return;

    try {
        initialCategory = new URL(window.location.href).searchParams.get('categorie') || '';
    } catch (e) {
        initialCategory = '';
    }

    function setHidden(element, hidden) {
        if (!element) return;
        element.hidden = hidden;
    }

    function resetCourseSelect() {
        cursus.innerHTML = '<option value="">Selecteer eerst een categorie</option>';
        cursus.setAttribute('disabled', 'disabled');
    }

    function renderCourseOptions(options) {
        var fragment = document.createDocumentFragment();
        var firstOption = document.createElement('option');
        firstOption.value = '';
        firstOption.textContent = 'Alle cursussen';
        fragment.appendChild(firstOption);

        options.forEach(function (option) {
            var courseOption = document.createElement('option');
            courseOption.value = String(option.id);
            courseOption.textContent = String(option.label);
            fragment.appendChild(courseOption);
        });

        cursus.innerHTML = '';
        cursus.appendChild(fragment);
        cursus.disabled = options.length === 0;
    }

    function applyFilter() {
        if (!cardsLoaded) return;

        var catVal = cat.value;
        var courseVal = cursus.value;
        var visibleCount = 0;
        var cards = cardsContainer.querySelectorAll('.course-card');

        cards.forEach(function (card) {
            var cardCat = card.dataset.categorie || '';
            var cardCourse = card.dataset.course || '';
            var catMatch = !catVal || cardCat === catVal;
            var courseMatch = !courseVal || cardCourse === courseVal;
            var isVisible = catMatch && courseMatch;

            card.style.display = isVisible ? '' : 'none';

            if (isVisible) visibleCount += 1;
        });

        setHidden(emptyState, !(catVal && visibleCount === 0));
    }

    function renderCards(html) {
        cardsContainer.innerHTML = html || '';
        cardsLoaded = !!cardsContainer.querySelector('.course-card');
    }

    function loadCards(category) {
        if (!ajaxConfig.ajaxUrl || isLoading) {
            return;
        }

        isLoading = true;
        cardsLoaded = false;
        cat.disabled = true;
        setHidden(placeholder, true);
        setHidden(emptyState, true);
        cardsContainer.innerHTML = '';

        var formData = new FormData();
        formData.append('action', 'arehbo_inschrijven_courses');
        formData.append('nonce', ajaxConfig.nonce || '');
        formData.append('category', category);

        fetch(ajaxConfig.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.json();
            })
            .then(function (payload) {
                if (!payload || !payload.success) {
                    throw new Error('Invalid response');
                }

                var data = payload.data || {};
                var options = Array.isArray(data.course_options) ? data.course_options : [];

                renderCards(data.cards_html || '');
                renderCourseOptions(options);
                cardsLoaded = !!data.has_cards;
                loadedCategory = category;

                if (!cardsLoaded) {
                    setHidden(emptyState, false);
                    return;
                }

                applyFilter();
            })
            .catch(function () {
                setHidden(placeholder, false);
                var placeholderText = placeholder.querySelector('p');
                if (placeholderText) {
                    placeholderText.textContent = 'Cursussen laden is tijdelijk niet gelukt.';
                }
            })
            .finally(function () {
                isLoading = false;
                cat.disabled = false;
            });
    }

    cat.addEventListener('change', function () {
        var category = cat.value;

        cursus.value = '';

        if (!category) {
            loadedCategory = '';
            cardsLoaded = false;
            resetCourseSelect();
            setHidden(placeholder, false);
            setHidden(emptyState, true);
            cardsContainer.innerHTML = '';
            return;
        }

        if (category !== loadedCategory || !cardsLoaded) {
            loadCards(category);
            return;
        }

        applyFilter();
    });

    cursus.addEventListener('change', applyFilter);

    resetCourseSelect();
    setHidden(placeholder, false);
    setHidden(emptyState, true);

    if (initialCategory) {
        cat.value = initialCategory;

        if (cat.value === initialCategory) {
            cat.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
}());
