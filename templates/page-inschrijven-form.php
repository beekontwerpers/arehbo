<?php
/*
 * Template Name: Cursuskalender – Formulier
 */

get_header('form');

$page_title  = get_the_title();
$background  = get_field('inschr_form_background') ?: 'light-blue';
$title       = get_field('inschr_form_title');
$description = get_field('inschr_form_description');
$form_id     = get_field('inschr_form_id');

$bg_class = ($background === 'dark-blue') ? ' bg-dark' : ' bg-light';
$is_dark  = $background === 'dark-blue';

$course_naam = isset($_GET['course']) ? sanitize_text_field($_GET['course']) : '';
$course_id   = isset($_GET['course_id']) ? absint($_GET['course_id']) : 0;
$locatie     = isset($_GET['locatie']) ? sanitize_text_field($_GET['locatie']) : '';
$datum_raw   = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
$beschikbaar = isset($_GET['beschikbaar']) ? intval($_GET['beschikbaar']) : 0;
$prijs       = isset($_GET['prijs']) ? sanitize_text_field($_GET['prijs']) : '';

$cursus_datum = '';
if ($datum_raw !== '') {
    $date_obj = DateTime::createFromFormat('Y-m-d', $datum_raw) ?: DateTime::createFromFormat('d-m-Y', $datum_raw);
    if ($date_obj instanceof DateTime) {
        $cursus_datum = $date_obj->format('d-m-Y');
    } else {
        $cursus_datum = $datum_raw;
    }
}

$dagdelen = [];
if (!empty($_GET['dagdelen'])) {
    $json = base64_decode(sanitize_text_field($_GET['dagdelen']));
    if ($json) {
        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            $dagdelen = $decoded;
        }
    }
}

// Inschrijving voor één los dagdeel: toon alleen dát dagdeel, niet de hele cursus.
$is_single       = !empty($_GET['single']) && $_GET['single'] !== 'false' && $_GET['single'] !== '0';
$appointment_id  = isset($_GET['appointment']) ? absint($_GET['appointment']) : 0;
$dagdeel_naam    = '';

if ($is_single && $appointment_id) {
    $selected = array_values(array_filter(
        $dagdelen,
        fn($d) => (int) ($d['appointment_id'] ?? 0) === $appointment_id
    ));

    if (!empty($selected)) {
        $dagdelen = $selected;
        $dagdeel  = $selected[0];

        // Naam van het dagdeel ("Cursus X: Ochtend" -> "Ochtend"), met fallback op het label.
        $raw_name = trim((string) ($dagdeel['name'] ?? ''));
        if ($raw_name !== '') {
            $dagdeel_naam = strpos($raw_name, ':') !== false
                ? trim(substr($raw_name, strpos($raw_name, ':') + 1))
                : $raw_name;
        } elseif (!empty($dagdeel['label'])) {
            $dagdeel_naam = (string) $dagdeel['label'];
        }

        // Datum en beschikbaarheid van dit dagdeel laten voorgaan op die van de hele cursus.
        if (!empty($dagdeel['date'])) {
            $cursus_datum = $dagdeel['date'];
        }
        if (isset($dagdeel['places_left'])) {
            $beschikbaar  = (int) $dagdeel['places_left'];
            $_GET['beschikbaar'] = $beschikbaar;
        }
    }
}

$cursuskalender_page = function_exists('arehbo_option_page_link_post') ? arehbo_option_page_link_post('cursuskalender_page') : null;

if ($cursuskalender_page) {
    $inschrijven_url = get_permalink($cursuskalender_page);
    $inschrijven_name = get_the_title($cursuskalender_page);
} else {
    $inschrijven_page = get_page_by_path('inschrijven');
    $inschrijven_url  = $inschrijven_page ? get_permalink($inschrijven_page) : home_url('/');
    $inschrijven_name = $inschrijven_page ? get_the_title($inschrijven_page) : 'Inschrijven';
}

$phone = get_field('footer_contact_phone', 'option');
$email = get_field('footer_contact_email', 'option');

$icon_back     = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true"><path d="M19 12H5M5 12L11 18M5 12L11 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$icon_euro     = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true"><path d="M15 5a7 7 0 1 0 0 14A7 7 0 0 0 15 5ZM4 10h8M4 14h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
$icon_user     = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>';
$icon_location = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>';
$icon_calendar = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>';

?>

<main id="main-content" class="inschr-form-page inschr-hero-bg<?= $bg_class; ?>">

    <div class="inschr-form-page__inner container">

        <div class="inschr-form-nav">
            <nav class="inschr-form-breadcrumbs" aria-label="Breadcrumb">
                <a class="inschr-form-breadcrumbs__link" href="<?= esc_url(home_url('/')); ?>">Home</a>
                <span class="inschr-form-breadcrumbs__sep" aria-hidden="true">/</span>
                <a class="inschr-form-breadcrumbs__link" href="<?= esc_url($inschrijven_url); ?>"><?= esc_html($inschrijven_name); ?></a>
                <span class="inschr-form-breadcrumbs__sep" aria-hidden="true">/</span>
                <span class="inschr-form-breadcrumbs__current" aria-current="page"><?= esc_html($page_title); ?></span>
            </nav>
        </div>

        <hr class="inschr-form-divider">

        <div class="inschr-form-hero">
            <?php if ($title) : ?>
                <h1 class="inschr-form-hero__title"><?= esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if ($description) : ?>
                <div class="inschr-form-hero__description"><?= wp_kses_post($description); ?></div>
            <?php endif; ?>
        </div>

    </div>

    <div class="inschr-form-grid container">

        <div class="inschr-form-main">

            <?php if ($course_naam || $cursus_datum || $prijs || isset($_GET['beschikbaar']) || $locatie) : ?>
                <div class="inschr-form-info-box">
                    <?php if ($course_naam) : ?>
                        <h2 class="inschr-form-info-box__title"><?= esc_html($dagdeel_naam ? $course_naam . ' - ' . $dagdeel_naam : $course_naam); ?></h2>
                    <?php endif; ?>
                    <div class="inschr-form-info-box__meta">
                        <?php if ($cursus_datum) : ?>
                            <div class="inschr-form-info-box__meta-row">
                                <?= $icon_calendar; ?>
                                <span><?= esc_html($cursus_datum); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($prijs) : ?>
                            <div class="inschr-form-info-box__meta-row">
                                <span class="inschr-form-info-box__meta-icon">€</span>
                                <span><?= esc_html($prijs); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_GET['beschikbaar'])) : ?>
                            <div class="inschr-form-info-box__meta-row">
                                <?= $icon_user; ?>
                                <span><?= esc_html($beschikbaar); ?> plekken beschikbaar</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($locatie) : ?>
                            <div class="inschr-form-info-box__meta-row">
                                <?= $icon_location; ?>
                                <span><?= esc_html($locatie); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="inschr-form-card <?= $is_dark ? 'inschr-form-card--dark' : ''; ?>">
                <?php if ($form_id) : ?>
                    <?= do_shortcode('[gravityforms id="' . intval($form_id) . '" ajax="true"]'); ?>
                <?php else : ?>
                    <p class="inschr-form-card__empty">Geen formulier geselecteerd.</p>
                <?php endif; ?>
            </div>

        </div>

        <div class="inschr-form-sidebar">

            <?php if (!empty($dagdelen)) : ?>
                <div class="inschr-form-days-box">
                    <h2 class="inschr-form-days-box__title"><?= count($dagdelen) === 1 ? 'Lesdag:' : 'Lesdagen:'; ?></h2>
                    <ul class="inschr-form-days-box__list">
                        <?php foreach ($dagdelen as $dag) :
                            $dt = !empty($dag['date']) ? DateTime::createFromFormat('d-m-Y', $dag['date']) : false;
                            $ts = $dt ? $dt->getTimestamp() : 0;
                            $formatted_date = $ts ? arehbo_format_nl_date($ts) : ($dag['label'] ?? '');
                            $time_display   = str_replace(' tot ', ' - ', $dag['time'] ?? '');
                        ?>
                            <li class="inschr-form-days-box__item">
                                <span class="inschr-form-days-box__date"><?= esc_html($formatted_date); ?></span>
                                <span class="inschr-form-days-box__time"><?= esc_html($time_display); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($phone || $email) : ?>
                <div class="inschr-form-contact-box">
                    <h2 class="inschr-form-contact-box__title">Komt u er niet uit?</h2>
                    <?php if ($phone) : ?>
                        <div class="inschr-form-contact-box__row">
                            <span class="inschr-form-contact-box__label">Bel ons</span>
                            <a class="inschr-form-contact-box__value" href="tel:<?= esc_attr(preg_replace('/\s+/', '', $phone)); ?>"><?= esc_html($phone); ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if ($email) : ?>
                        <div class="inschr-form-contact-box__row">
                            <span class="inschr-form-contact-box__label">Mail ons</span>
                            <a class="inschr-form-contact-box__value" href="mailto:<?= esc_attr($email); ?>"><?= esc_html($email); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

</main>

<?php get_footer(); ?>
