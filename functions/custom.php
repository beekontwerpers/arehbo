<?php

function arehbo_reading_time($post_id = null) {
    $content    = get_post_field('post_content', $post_id ?: get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $minutes    = max(1, (int) round($word_count / 200));
    return $minutes . ' ' . ($minutes === 1 ? 'minuut' : 'minuten');
}

function get_spacing_class($space) {
    if (empty($space)) return '';

    $classes = [];

    if (!empty($space['top']) && $space['top'] !== 'none') {
        $classes[] = 'space-top-' . $space['top'];
    }

    if (!empty($space['bottom']) && $space['bottom'] !== 'none') {
        $classes[] = 'space-bottom-' . $space['bottom'];
    }

    return implode(' ', $classes);
}

function get_full_id($id) {
    if (empty($id)) return '';
    return 'id="' . esc_attr($id) . '"';
}

function arehbo_today_hours($schedule) {
    if (empty($schedule) || !is_array($schedule)) return null;
    $now_day = (int) current_time('N');
    foreach ($schedule as $row) {
        if ((int) ($row['day'] ?? 0) === $now_day) {
            return $row;
        }
    }
    return null;
}

function arehbo_is_open_now($schedule) {
    if (empty($schedule) || !is_array($schedule)) {
        return false;
    }

    $now_day  = (int) current_time('N');
    $now_time = current_time('H:i');

    foreach ($schedule as $row) {
        $day = (int) ($row['day'] ?? 0);
        if ($day !== $now_day) continue;

        if (!empty($row['closed'])) return false;

        $open  = $row['open']  ?? '';
        $close = $row['close'] ?? '';
        if (!$open || !$close) return false;

        return ($now_time >= $open && $now_time < $close);
    }

    return false;
}

function arehbo_next_open_hours($schedule) {
    if (empty($schedule) || !is_array($schedule)) return null;

    try {
        $now = new DateTime('now', new DateTimeZone('Europe/Amsterdam'));
    } catch (Exception $e) {
        return null;
    }

    $now_day  = (int) $now->format('N');
    $now_time = $now->format('H:i');

    $by_day = [];
    foreach ($schedule as $row) {
        $by_day[(int) ($row['day'] ?? 0)] = $row;
    }

    for ($offset = 0; $offset <= 7; $offset++) {
        $day = (($now_day - 1 + $offset) % 7) + 1;
        $row = $by_day[$day] ?? null;
        if (!$row || !empty($row['closed'])) continue;

        $open  = $row['open']  ?? '';
        $close = $row['close'] ?? '';
        if (!$open || !$close) continue;

        if ($offset === 0 && $now_time >= $open) continue;

        return ['offset' => $offset, 'day' => $day, 'open' => $open, 'close' => $close];
    }

    return null;
}

function arehbo_format_nl_date($timestamp) {
    if (empty($timestamp)) return '';

    $days_short = [
        'Monday'    => 'Ma.',
        'Tuesday'   => 'Di.',
        'Wednesday' => 'Wo.',
        'Thursday'  => 'Do.',
        'Friday'    => 'Vr.',
        'Saturday'  => 'Za.',
        'Sunday'    => 'Zo.',
    ];

    $months = [
        1  => 'januari', 2  => 'februari', 3  => 'maart',     4  => 'april',
        5  => 'mei',     6  => 'juni',     7  => 'juli',      8  => 'augustus',
        9  => 'september', 10 => 'oktober', 11 => 'november', 12 => 'december',
    ];

    $dow   = $days_short[date('l', $timestamp)] ?? '';
    $day   = (int) date('j', $timestamp);
    $month = $months[(int) date('n', $timestamp)] ?? '';
    $year  = date('Y', $timestamp);

    return trim("{$dow} {$day} {$month} {$year}");
}

function arehbo_get_inschrijven_form_page_url() {
    $selected_page = arehbo_option_page_link_post('inschrijven_form_page');

    if ($selected_page) {
        return get_permalink($selected_page);
    }

    $form_page_posts = get_posts([
        'post_type'      => 'page',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'templates/page-inschrijven-form.php',
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ]);

    return !empty($form_page_posts) ? get_permalink($form_page_posts[0]) : '';
}

function arehbo_get_inschrijven_courses($category_slug = '') {
    $args = [
        'post_type'      => 'cursussen',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => 'visible_visual_systems',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
    ];

    if (!empty($category_slug)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'cursus_categorie',
                'field'    => 'slug',
                'terms'    => [$category_slug],
            ],
        ];
    }

    $courses = [];

    foreach (get_posts($args) as $course_post) {
        $course_id = (int) $course_post->ID;
        $courses[] = [
            'id'    => $course_id,
            'label' => $course_post->post_title,
        ];
    }

    return $courses;
}

function arehbo_get_inschrijven_cards($category_slug = '') {
    global $wpdb;

    $args = [
        'post_type'      => 'cursussen',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => 'visible_visual_systems',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
    ];

    if (!empty($category_slug)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'cursus_categorie',
                'field'    => 'slug',
                'terms'    => [$category_slug],
            ],
        ];
    }

    $cursussen_posts = get_posts($args);
    $cards = [];
    $today_start = strtotime('tomorrow', current_time('timestamp'));

    foreach ($cursussen_posts as $cursus_post) {
        $cursus_id_vs = get_field('id_visual_systems', $cursus_post->ID);
        if (!$cursus_id_vs) {
            continue;
        }

        $cursus_name   = $cursus_post->post_title;
        $cursus_loc    = get_field('locatie', $cursus_post->ID) ?: '';
        $cursus_kosten = get_field('kosten', $cursus_post->ID) ?: '';
        $cursus_code   = get_field('code_visual_systems', $cursus_post->ID) ?: '';
        $cursus_terms  = wp_get_object_terms($cursus_post->ID, 'cursus_categorie', ['fields' => 'slugs']);
        $cursus_cat    = (!is_wp_error($cursus_terms) && !empty($cursus_terms)) ? implode(',', $cursus_terms) : '';

        $appointments = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT appointment_id, start_date, end_date, room, location, places_left, name
                FROM {$wpdb->prefix}custom_visualsystems_appointments
                WHERE course_id = %s
                AND parent_id = 0
                ORDER BY start_date ASC",
                $cursus_id_vs
            ),
            ARRAY_A
        );

        $appointments = array_values(array_filter($appointments, function ($appointment) use ($today_start) {
            return ((int) ($appointment['start_date'] ?? 0)) >= $today_start;
        }));

        foreach ($appointments as $appointment) {
            $appointment_id = (int) $appointment['appointment_id'];
            $places_left    = max(0, (int) $appointment['places_left']);

            $sibling_dates = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT start_date, end_date, appointment_id, places_left, name
                    FROM {$wpdb->prefix}custom_visualsystems_appointments
                    WHERE parent_id = %d
                    ORDER BY start_date ASC",
                    $appointment_id
                ),
                ARRAY_A
            );

            $all_dates = array_merge(
                [['start_date' => $appointment['start_date'], 'end_date' => $appointment['end_date'], 'appointment_id' => $appointment_id, 'places_left' => $places_left, 'name' => $appointment['name']]],
                $sibling_dates
            );

            $unique_dates = [];
            foreach ($all_dates as $date_row) {
                $start = (int) ($date_row['start_date'] ?? 0);
                $end   = (int) ($date_row['end_date'] ?? 0);
                if (!$start) {
                    continue;
                }
                if (!isset($unique_dates[$start]) || ($end && $end < ($unique_dates[$start]['end_date'] ?? PHP_INT_MAX))) {
                    $unique_dates[$start] = ['start_date' => $start, 'end_date' => $end, 'appointment_id' => $date_row['appointment_id'] ?? null, 'places_left' => $date_row['places_left'] ?? 0, 'name' => $date_row['name'] ?? ''];
                }
            }

            $all_dates = array_values($unique_dates);
            usort($all_dates, fn($a, $b) => ($a['start_date'] ?? 0) <=> ($b['start_date'] ?? 0));

            $merged_days = [];
            foreach ($all_dates as $date_row) {
                $start_ts = (int) ($date_row['start_date'] ?? 0);
                $end_ts   = (int) ($date_row['end_date'] ?? 0);
                if (!$start_ts) {
                    continue;
                }

                $date_key = date_i18n('Y-m-d', $start_ts);

                if (!isset($merged_days[$date_key])) {
                    $merged_days[$date_key] = [
                        'start_date' => $start_ts,
                        'end_date'   => $end_ts,
						'appointment_id' => $date_row['appointment_id'] ?? null,
						'places_left' => $date_row['places_left'] ?? 0,
                    	'name' => $date_row['name'] ?? ''
                    ];
                    continue;
                }

                $merged_days[$date_key]['start_date'] = min($merged_days[$date_key]['start_date'], $start_ts);
                if ($end_ts) {
                    $merged_days[$date_key]['end_date'] = max($merged_days[$date_key]['end_date'] ?? 0, $end_ts);
                }
            }

            $merged_days = array_values($merged_days);
            usort($merged_days, fn($a, $b) => ($a['start_date'] ?? 0) <=> ($b['start_date'] ?? 0));

            $days = [];
            foreach ($merged_days as $i => $date_row) {
                $days[] = [
                    'label' => 'Lesdag ' . ($i + 1) . ' (' . date_i18n('l', $date_row['start_date']) . ')',
                    'date'  => date_i18n('d-m-Y', $date_row['start_date']),
                    'time'  => date_i18n('H:i', $date_row['start_date']) . ' tot ' . date_i18n('H:i', $date_row['end_date']),
					'appointment_id' => $date_row['appointment_id'] ?? null,
					'places_left' => $date_row['places_left'] ?? 0,
                	'name' => $date_row['name'] ?? ''
                ];
                if($date_row['places_left'] ?? 0 < $places_left) {
                    $places_left = $date_row['places_left'] ?? 0;
                }
            }

            if ($places_left === 0) {
                $status = 'vol';
            } elseif ($places_left < 4) {
                $status = 'bijna-vol';
            } else {
                $status = 'beschikbaar';
            }

            $cards[] = [
                'status'      => $status,
                'name'        => $cursus_name,
                'available'   => $places_left,
                'days_count'  => count($merged_days),
                'location'    => $cursus_loc,
                'prijs'       => $cursus_kosten,
                'days'        => $days,
                'dates_ts'    => array_map(fn($d) => $d['start_date'], $merged_days),
                'category'    => $cursus_cat,
                'course_id'   => $cursus_post->ID,
            	'code'        => $cursus_code,
                'appointment' => $appointment_id,
            ];
        }
    }

    usort($cards, function ($a, $b) {
        return ($a['dates_ts'][0] ?? 0) <=> ($b['dates_ts'][0] ?? 0);
    });

    return $cards;
}

function arehbo_render_inschrijven_cards($cards, $form_page_url) {
    ob_start();

    $icon_location = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>';
    $icon_calendar = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>';
    $icon_clock    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>';
    $icon_arrow    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    foreach ($cards as $card) :
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
            	'code'        => $card['code'],
                'locatie'     => $card['location'],
                'date'        => $first_ts ? date_i18n('Y-m-d', $first_ts) : '',
                'beschikbaar' => $card['available'],
                'prijs'       => $card['prijs'] ?? '',
                'dagdelen'    => base64_encode(wp_json_encode($card['days'])),
            ], $form_page_url);
        }
        $badge_tag   = $badge_url ? 'a' : 'div';
        $badge_attrs = $badge_url ? ' href="' . esc_url($badge_url) . '"' : '';
    ?>

    <article class="course-card course-card--<?= esc_attr($status); ?>"
             data-categorie="<?= esc_attr($card['category'] ?? ''); ?>"
             data-course="<?= esc_attr($card['course_id'] ?? ''); ?>">

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
                	?>
                	<li class="course-card__lesdag">
                        <a href="<?php echo $form_page_url."?appointment={$dag['appointment_id']}&single=true&param-cursus-code={$card['code']}&course={$card['name']}&course_id={$card['course_id']}&locatie={$card['location']}&date={$dag['date']}&beschikbaar={$card['available']}&prijs={$course_kosten}&dagdelen=".base64_encode(wp_json_encode($card['days']));  ?>"><span class="course-card__lesdag-label"><?php echo $name; ?></span></a>
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

    <?php endforeach;

    return ob_get_clean();
}

function arehbo_handle_inschrijven_courses_ajax() {
    check_ajax_referer('arehbo_inschrijven_courses', 'nonce');

    $category_slug = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : '';
    $cards         = arehbo_get_inschrijven_cards($category_slug);
    $courses       = arehbo_get_inschrijven_courses($category_slug);
    $form_page_url = arehbo_get_inschrijven_form_page_url();

    wp_send_json_success([
        'cards_html'     => arehbo_render_inschrijven_cards($cards, $form_page_url),
        'course_options' => $courses,
        'has_cards'      => !empty($cards),
    ]);
}

add_action('wp_ajax_arehbo_inschrijven_courses', 'arehbo_handle_inschrijven_courses_ajax');
add_action('wp_ajax_nopriv_arehbo_inschrijven_courses', 'arehbo_handle_inschrijven_courses_ajax');
