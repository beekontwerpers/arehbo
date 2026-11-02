<?php

/**
 * Zet een prijs uit een ACF-tekstveld ("€413,- p.p.", "€172,50 p.p.") om naar een float.
 */
function arehbo_parse_price_to_float($raw) {
    $clean = preg_replace('/[^\d.,]/', '', (string) $raw);

    if ($clean === '') {
        return 0.0;
    }

    // "1.234,50" -> punt is duizendtalscheiding, komma is decimaalteken.
    if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
        $clean = str_replace('.', '', $clean);
    }

    $clean = rtrim(str_replace(',', '.', $clean), '.');

    return $clean === '' ? 0.0 : (float) $clean;
}

/**
 * Haal de Visual Systems-appointment op waarop is ingeschreven.
 *
 * @param int $appointment_id
 * @return array|null  course_id (VS), component_id, name
 */
function arehbo_get_vs_appointment($appointment_id) {
    global $wpdb;

    $appointment_id = (int) $appointment_id;
    if (!$appointment_id) {
        return null;
    }

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT appointment_id, course_id, component_id, parent_id, name
             FROM {$wpdb->prefix}custom_visualsystems_appointments
             WHERE appointment_id = %d
             LIMIT 1",
            $appointment_id
        ),
        ARRAY_A
    );

    return $row ?: null;
}

/**
 * Zoek de cursus-post bij een Visual Systems-id. Losse dagdelen staan als eigen
 * cursus-post met id "{course_id}_{component_id}", de hele reeks als "{course_id}".
 *
 * @param string $vs_id
 * @return WP_Post|null
 */
function arehbo_get_cursus_post_by_vs_id($vs_id) {
    $vs_id = (string) $vs_id;
    if ($vs_id === '') {
        return null;
    }

    $posts = get_posts([
        'post_type'        => 'cursussen',
        'post_status'      => 'publish',
        'posts_per_page'   => 1,
        'suppress_filters' => false,
        'meta_query'       => [
            [
                'key'     => 'id_visual_systems',
                'value'   => $vs_id,
                'compare' => '=',
            ],
        ],
    ]);

    return $posts ? $posts[0] : null;
}

/**
 * Bouw de GA4 purchase-payload voor de bedankpagina.
 *
 * De cursus wordt bepaald via het appointment waarop is ingeschreven (veld 1016,
 * met de URL-parameter `appointment` en de source_url van de entry als backup).
 * Dat appointment hoort bij één component_id, oftewel één dagdeel van een cursus,
 * en daarmee bij de juiste prijs.
 *
 * @return array|null
 */
function arehbo_get_purchase_datalayer() {
    $entry_id     = isset($_GET['entry_id']) ? absint($_GET['entry_id']) : 0;
    $participants = isset($_GET['participants']) ? absint($_GET['participants']) : 0;
    $course_id    = isset($_GET['cursusID']) ? absint($_GET['cursusID']) : 0;

    if (!$entry_id) {
        return null;
    }

    $appointment_id = isset($_GET['appointment']) ? absint($_GET['appointment']) : 0;
    $is_single      = !empty($_GET['single']) && $_GET['single'] !== 'false' && $_GET['single'] !== '0';
    $source_url     = '';

    // De entry is de betrouwbaarste bron: URL-parameters zijn door de bezoeker aan te passen.
    if (class_exists('GFAPI')) {
        $entry = GFAPI::get_entry($entry_id);
        if (!is_wp_error($entry)) {
            if (!empty($entry[1016])) {
                $appointment_id = absint($entry[1016]);
            }
            if (!empty($entry[25])) {
                $participants = absint($entry[25]);
            }
            if (!empty($entry[26])) {
                $course_id = absint($entry[26]);
            }
            $source_url = (string) ($entry['source_url'] ?? '');
        }
    }

    // Laatste redmiddel voor entries zonder appointment-veld: de URL van het formulier.
    // Gravity Forms kapt source_url af op 200 tekens, maar `appointment` en `single`
    // staan vooraan in de querystring.
    if ($source_url !== '') {
        $query = parse_url($source_url, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $source_args);
            if (!$appointment_id && !empty($source_args['appointment'])) {
                $appointment_id = absint($source_args['appointment']);
            }
            if (!empty($source_args['single']) && $source_args['single'] !== 'false' && $source_args['single'] !== '0') {
                $is_single = true;
            }
        }
    }

    if ($participants < 1) {
        $participants = 1;
    }

    $appointment  = arehbo_get_vs_appointment($appointment_id);
    $course_post  = null;
    $dagdeel_post = null;
    $price        = 0.0;
    $item_name    = '';

    if ($appointment) {
        $vs_course    = (string) $appointment['course_id'];
        $component_id = (int) $appointment['component_id'];

        $course_post  = arehbo_get_cursus_post_by_vs_id($vs_course);
        $dagdeel_post = arehbo_get_cursus_post_by_vs_id($vs_course . '_' . $component_id);

        if ($is_single) {
            // Inschrijving op één dagdeel: de losse dagdeel-cursus heeft zijn eigen prijs,
            // anders de prijs van de dagdelen-rij met dit component_id.
            if ($dagdeel_post) {
                $course_post = $dagdeel_post;
                $price       = arehbo_parse_price_to_float(get_field('kosten', $dagdeel_post->ID));
            } elseif ($course_post) {
                $prices = arehbo_get_course_component_prices($course_post->ID);
                $price  = arehbo_parse_price_to_float($prices[$component_id] ?? '');
                if (!empty($appointment['name'])) {
                    $item_name = (string) $appointment['name'];
                }
            }
        } elseif ($course_post) {
            // Inschrijving op de hele reeks: de cursusprijs, niet die van één dagdeel.
            $price = arehbo_parse_price_to_float(get_field('kosten', $course_post->ID));
        }
    }

    // Terugvallen op de cursus uit het formulier als het appointment niets oplevert,
    // zodat de conversie hoe dan ook gemeten wordt.
    if (!$course_post && $course_id) {
        $fallback = get_post($course_id);
        if ($fallback && $fallback->post_type === 'cursussen') {
            $course_post = $fallback;
        }
    }

    if (!$course_post) {
        return null;
    }

    if ($price <= 0) {
        $price = arehbo_parse_price_to_float(get_field('kosten', $course_post->ID));
    }

    $item = [
        'item_id'   => (string) $course_post->ID,
        'item_name' => $item_name ?: $course_post->post_title,
        'price'     => round($price, 2),
        'quantity'  => $participants,
    ];

    $terms = wp_get_object_terms($course_post->ID, 'cursus_categorie', ['fields' => 'names']);
    if (!is_wp_error($terms) && !empty($terms)) {
        $item['item_category'] = $terms[0];
    }

    $code = get_field('code_visual_systems', $course_post->ID);
    if ($code) {
        $item['item_variant'] = (string) $code;
    }

    return [
        'event'     => 'purchase',
        'ecommerce' => [
            'transaction_id' => (string) $entry_id,
            'value'          => round($price * $participants, 2),
            'currency'       => 'EUR',
            'items'          => [$item],
        ],
    ];
}

/**
 * Push het purchase-event naar de dataLayer op de bedankpagina van de inschrijving.
 */
add_action('wp_head', function () {
    if (!is_page('bedankt-inschrijving')) {
        return;
    }

    $payload = arehbo_get_purchase_datalayer();
    if (!$payload) {
        return;
    }

    $json     = wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $entry_id = $payload['ecommerce']['transaction_id'];
    ?>
    <script>
    (function () {
        var key = 'arehbo_purchase_<?= esc_js($entry_id); ?>';

        // Voorkom dubbele conversies als de bezoeker de bedankpagina herlaadt.
        try {
            if (window.sessionStorage && sessionStorage.getItem(key)) return;
            if (window.sessionStorage) sessionStorage.setItem(key, '1');
        } catch (e) {}

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push(<?= $json; ?>);
    })();
    </script>
    <?php
}, 5);
