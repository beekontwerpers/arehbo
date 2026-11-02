<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space   = get_spacing_class(get_field('space'));
$full_id = get_full_id(get_field('id'));
$title   = get_field('title');

global $wpdb, $post;

$course_post_id = $post ? $post->ID : get_the_ID();
$course_id      = $course_post_id ? get_field('id_visual_systems', $course_post_id) : '';
$course_code    = $course_post_id ? get_field('code_visual_systems', $course_post_id) : '';
$course_name    = $course_post_id ? get_the_title($course_post_id) : '';
$course_loc     = $course_post_id ? get_field('locatie', $course_post_id) : '';
$course_kosten  = $course_post_id ? get_field('kosten', $course_post_id) : '';

// start_date is een echte UTC-timestamp, dus de grens ook: morgen 00:00 in de sitetijdzone.
$today_start = (new DateTimeImmutable('tomorrow', wp_timezone()))->getTimestamp();

$component_ids = function_exists('arehbo_get_course_component_ids')
    ? arehbo_get_course_component_ids($course_post_id)
    : [];

$component_prices = function_exists('arehbo_get_course_component_prices')
    ? arehbo_get_course_component_prices($course_post_id)
    : [];

$appointments = [];
if (!empty($component_ids)) {
    $placeholders = implode(',', array_fill(0, count($component_ids), '%d'));
    $appointments = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT appointment_id, parent_id, component_id, start_date, end_date, room, location, places_left, name
            FROM {$wpdb->prefix}custom_visualsystems_appointments
            WHERE component_id IN ($placeholders)
            AND start_date >= %d
            ORDER BY start_date ASC",
            ...array_merge($component_ids, [$today_start])
        ),
        ARRAY_A
    );
}

$phone = get_field('footer_contact_phone', 'option');
$email = get_field('footer_contact_email', 'option');

$form_page_url = function_exists('arehbo_get_inschrijven_form_page_url')
    ? arehbo_get_inschrijven_form_page_url()
    : '';

$icon_user     = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>';
$icon_calendar = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>';
$icon_location = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>';
$icon_clock    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>';
$icon_arrow    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$icon_close    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" height="26" fill="currentColor" aria-hidden="true"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
$icon_chevron  = '<svg class="course-card__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false" fill="none"><path d="M4 9L12 17L20 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$cards = [];
// Groepeer de component-appointments per reeks (parent-keten):
// een sibling hoort bij zijn parent, een parent (parent_id = 0) vormt zelf de reeks.
$runs = [];
foreach ($appointments as $appointment) {
    $run_key = ((int) $appointment['parent_id']) > 0
        ? (int) $appointment['parent_id']
        : (int) $appointment['appointment_id'];
    $runs[$run_key][] = $appointment;
}

foreach ($runs as $run_appointments) {
    usort($run_appointments, fn($a, $b) => ((int) $a['start_date']) <=> ((int) $b['start_date']));

    // Primair appointment (voor inschrijven): de parent als die in de reeks zit, anders de eerste dag.
    $primary = $run_appointments[0];
    foreach ($run_appointments as $ra) {
        if ((int) $ra['parent_id'] === 0) {
            $primary = $ra;
            break;
        }
    }
    $appointment_id = (int) $primary['appointment_id'];

    $places_left = null;
    $days = [];
    foreach ($run_appointments as $i => $date_row) {
        $day_places    = max(0, (int) $date_row['places_left']);
        $day_component = (int) $date_row['component_id'];
        $days[] = [
            'label' => 'Lesdag ' . ($i + 1) . ' (' . wp_date('l', $date_row['start_date']) . ')',
            'date'  => wp_date('d-m-Y', $date_row['start_date']),
            'time'  => wp_date('H:i', $date_row['start_date']) . ' tot ' . wp_date('H:i', $date_row['end_date']),
            'appointment_id' => (int) $date_row['appointment_id'],
            'component_id' => $day_component,
            'places_left' => $day_places,
            'prijs' => $component_prices[$day_component] ?? '',
            'name' => $date_row['name'] ?? ''
        ];
        $places_left = ($places_left === null) ? $day_places : min($places_left, $day_places);
    }
    $places_left = (int) $places_left;

    if ($places_left === 0) {
        $status = 'vol';
    } elseif ($places_left < 4) {
        $status = 'bijna-vol';
    } else {
        $status = 'beschikbaar';
    }

    $cards[] = [
        'status'      => $status,
        'name'        => $course_name,
        'code'        => $course_code,
        'available'   => $places_left,
        'days_count'  => count(array_unique(array_map(
            fn($d) => $d['date'],
            $days
        ))),
        'location'    => $course_loc,
        'days'        => $days,
        'dates_ts'    => array_map(fn($d) => (int) $d['start_date'], $run_appointments),
        'appointment' => $appointment_id,
    ];
}

?>

<section <?= $full_id; ?> class="course-calendar bg-light">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">

            <?php if ($title) : ?>
                <h2 class="course-calendar__title"><?= esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if (empty($cards)) : ?>

                <div class="course-calendar__empty">
                    <p>
                        Sorry, momenteel hebben we geen cursus vrij waarop aangemeld kan worden.
                        Toch aanmelden? Stuur een e-mail naar
                        <?php if ($email) : ?>
                            <a href="mailto:<?= esc_attr($email); ?>"><?= esc_html($email); ?></a>
                        <?php else : ?>
                            ons
                        <?php endif; ?>,
                        of bel naar
                        <?php if ($phone) : ?>
                            <a href="tel:<?= esc_attr(preg_replace('/\s+/', '', $phone)); ?>"><?= esc_html($phone); ?></a>
                        <?php else : ?>
                            ons
                        <?php endif; ?>.
                    </p>
                </div>

            <?php else : ?>

                <div class="course-calendar__cards">

                    <?php foreach ($cards as $card) :
                        $status   = $card['status'];
                        $avail    = $card['available'];
                        $days     = $card['days'];
                        $dates_ts = $card['dates_ts'];
                        $first_ts = $dates_ts[0] ?? null;

	                        $badge_url = '';
	                        if ($status !== 'vol' && $form_page_url) {
	                            $badge_url = add_query_arg([
	                                'appointment' => $card['appointment'],
	                                'course'      => $card['name'],
	                                'course_id'   => $card['course_id'] ?? '',
	                            	'param-cursus-code' => $card['code'],
	                                'locatie'     => $card['location'],
	                                'date'        => $first_ts ? date_i18n('Y-m-d', $first_ts) : '',
	                                'beschikbaar' => $card['available'],
	                                'prijs'       => $course_kosten,
	                                'dagdelen'    => base64_encode(wp_json_encode($card['days'])),
	                            ], $form_page_url);
	                        }
                        $badge_tag   = $badge_url ? 'a' : 'div';
                        $badge_attrs = $badge_url ? ' href="' . esc_url($badge_url) . '"' : '';
                    ?>

                    <article class="course-card course-card--<?= esc_attr($status); ?>">

                        <aside class="course-card__dates">
                            <?php if ($first_ts) : ?>
                                <div class="course-card__date-big"><?= esc_html(arehbo_format_nl_date($first_ts)); ?></div>
                                <hr class="course-card__dates-sep">
                            <?php endif; ?>
                            <ul class="course-card__dates-meta">
                                <?php if ($card['location']) : ?>
                                    <li class="course-card__meta-item">
                                        <?= $icon_location; ?>
                                        <span><?= esc_html($card['location']); ?></span>
                                    </li>
                                <?php endif; ?>
                                <li class="course-card__meta-item">
                                    <?= $icon_calendar; ?>
                                    <span><?= esc_html($card['days_count']); ?> <?= $card['days_count'] === 1 ? 'lesdag' : 'lesdagen'; ?></span>
                                </li>
                            </ul>
                        </aside>

                        <div class="course-card__main">
                            <h3 class="course-card__name"><?= esc_html($card['name']); ?></h3>
                            <hr class="course-card__main-sep">
                            <ul class="course-card__lesdagen">
                                <?php foreach ($days as $i => $dag) : 
                                	$name = "Lesdag ".esc_html($i + 1);
				                	if($dag['name'] && $dag['name'] !== '') {
				                		if(strpos($dag['name'], ":") !== false){
				                			$name = esc_html(substr($dag['name'], strpos($dag['name'], ":") + 1));	
				                		}else{
				                			$name = esc_html($dag['name']);
				                		}
				                	}
                                    // Zonder prijs op het component is inschrijven op dit dagdeel niet mogelijk: geen link.
                                	$dag_prijs = trim((string) ($dag['prijs'] ?? ''));
                                ?>
                                    <li class="course-card__lesdag">
                                        <?php if ($dag_prijs !== '') : ?>
                                            <a href="<?php echo $form_page_url."?appointment={$dag['appointment_id']}&single=true&param-cursus-code={$card['code']}&course={$card['name']}&locatie={$card['location']}&beschikbaar={$card['available']}&prijs=".rawurlencode($dag_prijs)."&dagdelen=".base64_encode(wp_json_encode($card['days']));  ?>"><span class="course-card__lesdag-label"><?php echo $name; ?></span></a>
                                        <?php else : ?>
                                            <span class="course-card__lesdag-label"><?php echo $name; ?></span>
                                        <?php endif; ?>
                                        <div class="course-card__lesdag-meta">
                                            <span class="course-card__lesdag-date">
                                                <?= $icon_calendar; ?>
                                                <span><?= esc_html(arehbo_format_nl_date($dates_ts[$i] ?? null)); ?></span>
                                            </span>
                                            <span class="course-card__lesdag-time">
                                                <?= $icon_clock; ?>
                                                <span><?= esc_html($dag['time']); ?></span>
                                            </span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="course-card__action">
                            <<?= $badge_tag; ?> class="course-card__badge"<?= $badge_attrs; ?>>
                                <span class="course-card__badge-text"><?= $status === 'vol' ? 'VOLGEBOEKT' : 'INSCHRIJVEN'; ?></span>
                                <?php if ($status !== 'vol') : ?>
                                    <span class="course-card__badge-icon">
                                        <?= $icon_arrow; ?>
                                    </span>
                                <?php endif; ?>
                            </<?= $badge_tag; ?>>
                            <span class="course-card__plekken">
                                <?= esc_html($avail); ?> <?= $avail === 1 ? 'plek' : 'plekken'; ?> beschikbaar
                            </span>
                        </div>

                    </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>
</section>
