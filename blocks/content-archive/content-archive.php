<?php

$space        = get_spacing_class(get_field('space'));
$full_id      = get_full_id(get_field('id'));
$background   = get_field('background')    ?: 'light';
$layout       = get_field('layout')        ?: 'slider';
$post_type    = get_field('post_type')     ?: 'cursussen';
$display_mode_slider = get_field('display_mode_slider') ?: get_field('display_mode') ?: 'latest';
$display_mode_slider_vacatures = get_field('display_mode_slider_vacatures') ?: get_field('display_mode') ?: 'latest';
$display_mode_grid   = get_field('display_mode_grid') ?: get_field('display_mode') ?: 'all';
$display_mode = $layout === 'grid'
    ? $display_mode_grid
    : ($post_type === 'vacatures' ? $display_mode_slider_vacatures : $display_mode_slider);
$manual_posts = get_field('posts')         ?: [];
$show_filters = get_field('show_filters');
$title        = get_field('title');
$show_google_reviews = get_field('show_google_reviews');
$show_google_reviews = ($show_google_reviews === '' || $show_google_reviews === null) ? true : (bool) $show_google_reviews;

$taxonomy = arehbo_taxonomy_for_post_type($post_type);
$term     = $taxonomy ? get_field('term_' . $post_type) : null;
$grid_default_terms = [];
$grid_active_term   = 'all';

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

$btn_label = 'MEER INFORMATIE';

$query_args = [
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
];

if ($layout === 'grid') {
    $query_args['posts_per_page'] = -1;

    if ($display_mode === 'category' && $taxonomy) {
        if (is_object($term) && !empty($term->slug)) {
            $grid_active_term = $term->slug;
            $grid_default_terms[] = $term->slug;
        }
    }

} elseif ($display_mode === 'manual' && !empty($manual_posts)) {
    $ids = array_map(fn($p) => is_object($p) ? $p->ID : $p, (array) $manual_posts);
    $query_args['post__in'] = $ids;
    $query_args['orderby']  = 'post__in';

} elseif ($display_mode === 'category' && !empty($term) && $taxonomy) {
    $term_id = is_object($term) ? $term->term_id : (int) $term;
    $query_args['tax_query'] = [[
        'taxonomy' => $taxonomy,
        'field'    => 'term_id',
        'terms'    => $term_id,
    ]];

} else {
    $query_args['posts_per_page'] = 6;
}

if ($post_type === 'cursussen' && function_exists('arehbo_visible_coursussen_query_args')) {
    $query_args = arehbo_visible_coursussen_query_args($query_args);
}

$archive_query = new WP_Query($query_args);
$posts         = $archive_query->posts;
wp_reset_postdata();

if (empty($posts)) {
    return;
}

$terms_map = [];
if ($layout === 'grid' && $show_filters && $taxonomy) {
    foreach ($posts as $post) {
        $post_terms = get_the_terms($post->ID, $taxonomy);
        if ($post_terms && ! is_wp_error($post_terms)) {
            foreach ($post_terms as $t) {
                $terms_map[$t->slug] = $t->name;
            }
        }
    }
}

$arrow_right = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$arrow_left  = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M16 10H4M4 10L9 5M4 10L9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$default_terms_attr = !empty($grid_default_terms)
    ? ' data-default-terms="' . esc_attr(implode(',', $grid_default_terms)) . '"'
    : '';

if (!function_exists('build_archive_card')) :
function build_archive_card($post, $post_type, $btn_label, $arrow_right, $terms_map_active = false) {
    $post_id   = $post->ID;
    $permalink = get_permalink($post_id);
    $post_title = get_the_title($post_id);

    $image_id  = get_post_thumbnail_id($post_id);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
    $image_alt = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: esc_html($post_title)) : esc_html($post_title);

    $kosten  = ($post_type === 'cursussen') ? get_field('kosten', $post_id)  : '';
    $duur    = ($post_type === 'cursussen') ? get_field('duur', $post_id)    : '';
    $uren    = ($post_type === 'vacatures') ? get_field('uren', $post_id)    : '';
    $locatie = ($post_type === 'vacatures') ? get_field('locatie', $post_id) : '';

    $data_terms = '';
    if ($terms_map_active) {
        $taxonomy = arehbo_taxonomy_for_post_type($post_type);
        if ($taxonomy) {
            $post_terms = get_the_terms($post_id, $taxonomy);
            if ($post_terms && ! is_wp_error($post_terms)) {
                $slugs      = array_map(fn($t) => $t->slug, $post_terms);
                $data_terms = 'data-terms="' . esc_attr(implode(',', $slugs)) . '"';
            }
        }
    }

    ob_start();
    ?>
    <div class="archive-card" <?= $data_terms; ?>>

        <a
            href="<?= esc_url($permalink); ?>"
            class="archive-card__link"
            aria-label="<?= esc_attr($post_title); ?>"
        ></a>

        <?php if ($image_url) : ?>
            <div class="archive-card__media">
                <img
                    class="archive-card__image"
                    src="<?= esc_url($image_url); ?>"
                    alt="<?= esc_attr($image_alt); ?>"
                    width="305"
                    height="215"
                    loading="lazy"
                >
            </div>
        <?php endif; ?>

        <div class="archive-card__body">

            <h3 class="archive-card__title"><?= esc_html($post_title); ?></h3>

            <?php if ($kosten || $duur || $uren || $locatie) : ?>
                <div class="archive-card__meta">

                    <?php if ($kosten) : ?>
                        <div class="archive-card__meta-row">
                            <span class="archive-card__meta-label">Kosten:</span>
                            <span class="archive-card__meta-value"><?= esc_html($kosten); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($duur) : ?>
                        <div class="archive-card__meta-row">
                            <span class="archive-card__meta-label">Duur:</span>
                            <span class="archive-card__meta-value"><?= esc_html($duur); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($uren) : ?>
                        <div class="archive-card__meta-row">
                            <span class="archive-card__meta-label">Uren:</span>
                            <span class="archive-card__meta-value"><?= esc_html($uren); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($locatie) : ?>
                        <div class="archive-card__meta-row">
                            <span class="archive-card__meta-label">Locatie:</span>
                            <span class="archive-card__meta-value"><?= esc_html($locatie); ?></span>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

            <div class="archive-card__btn-wrap">
                <span class="archive-card__btn-label" aria-hidden="true"><?= esc_html($btn_label); ?></span>
                <span class="archive-card__btn" aria-hidden="true"><?= $arrow_right; ?></span>
            </div>

        </div>

    </div>
    <?php
    return ob_get_clean();
}
endif;

?>

<?php if ($layout === 'slider') : ?>

<section <?= $full_id; ?> class="content-archive content-archive--slider <?= esc_attr($bg_class); ?>">
    <div class="<?= esc_attr($space); ?>">

        <?php if ($title) : ?>
            <div class="container">
                <div class="content-archive__head">
                    <h2 class="content-archive__title"><?= esc_html($title); ?></h2>
                    <?php if ($show_google_reviews) : ?>
                        <?php get_template_part('components/google-reviews', '', ['variant' => $background === 'dark' ? 'dark' : 'light']); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="content-archive__swiper-wrap container">
            <div class="swiper">
                <div class="swiper-wrapper">

                    <?php foreach ($posts as $post) : ?>
                        <div class="swiper-slide">
                            <?= build_archive_card($post, $post_type, $btn_label, $arrow_right); ?>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

        <div class="container">
            <div class="content-archive__nav slider-nav">
                <div class="slider-nav__arrows">
                    <button class="slider-nav__btn slider-nav__btn--prev" aria-label="Vorige pagina">
                        <?= $arrow_left; ?>
                    </button>
                    <button class="slider-nav__btn slider-nav__btn--next" aria-label="Volgende pagina">
                        <?= $arrow_right; ?>
                    </button>
                </div>
                <div class="slider-nav__progress content-archive__progress swiper-pagination"></div>
            </div>
        </div>

    </div>
</section>

<?php elseif ($layout === 'grid') : ?>

<section <?= $full_id; ?> class="content-archive content-archive--grid <?= esc_attr($bg_class); ?>"<?= $default_terms_attr; ?>>
    <div class="<?= esc_attr($space); ?>">
        <div class="container">

            <?php if ($title) : ?>
                <h2 class="content-archive__title"><?= esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($show_filters && ! empty($terms_map)) :
                $all_labels = ['cursussen' => 'Alle cursussen', 'diensten' => 'Alle diensten', 'vacatures' => 'Alle vacatures'];
                $all_label  = $all_labels[$post_type] ?? 'Alles';
            ?>
                <div class="archive-filters">
                    <button class="archive-filter<?= $grid_active_term === 'all' ? ' is-active' : ''; ?>" data-term="all" type="button">
                        <span class="archive-filter__check" aria-hidden="true"></span>
                        <span class="archive-filter__label"><?= esc_html($all_label); ?></span>
                    </button>
                    <?php foreach ($terms_map as $slug => $name) : ?>
                        <button class="archive-filter<?= $grid_active_term === $slug ? ' is-active' : ''; ?>" data-term="<?= esc_attr($slug); ?>" type="button">
                            <span class="archive-filter__check" aria-hidden="true"></span>
                            <span class="archive-filter__label"><?= esc_html($name); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="archive-grid">
                <?php foreach ($posts as $post) : ?>
                    <?= build_archive_card($post, $post_type, $btn_label, $arrow_right, true); ?>
                <?php endforeach; ?>
            </div>

            <div class="archive-pagination" aria-label="Paginering">
                <button class="archive-page-btn archive-page-btn--prev" aria-label="Vorige pagina" type="button">
                    <?= $arrow_left; ?>
                </button>
                <div class="archive-page-numbers" role="list"></div>
                <button class="archive-page-btn archive-page-btn--next" aria-label="Volgende pagina" type="button">
                    <?= $arrow_right; ?>
                </button>
            </div>

        </div>
    </div>
</section>

<?php endif; ?>
