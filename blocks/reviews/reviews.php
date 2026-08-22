<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space      = get_spacing_class(get_field('space'));
$full_id    = get_full_id(get_field('id'));
$background = get_field('rv_background') ?: 'dark';
$layout     = get_field('rv_layout') ?: 'slider';

$title      = get_field('rv_title');
$btn_raw    = get_field('rv_button') ?: [];
$reviews    = get_field('rv_reviews') ?: [];
if (empty($reviews)) {
    $reviews = get_field('blocks_reviews_reviews', 'option') ?: [];
}

$btn_url    = $btn_raw['url']    ?? '';
$btn_label  = $btn_raw['title']  ?? '';
$btn_target = $btn_raw['target'] ?? '_self';

$google_variant = 'bare';

$arrow_left  = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M16 10H4M4 10L9 5M4 10L9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$arrow_right = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

?>

<?php
$layout = in_array($layout, ['grid', 'slider'], true) ? $layout : 'slider';
?>

<section <?= $full_id; ?> class="reviews-block reviews-block--<?= esc_attr($background); ?> reviews-block--<?= esc_attr($layout); ?>">
    <div class="<?= esc_attr($space); ?>">

        <div class="reviews-block__header container">

            <div class="reviews-block__header-left">

                <?php if ($title) : ?>
                    <h2 class="reviews-block__title"><?= esc_html($title); ?></h2>
                <?php endif; ?>

                <?php get_template_part('components/google-reviews', '', ['variant' => $google_variant]); ?>

            </div>

            <?php if ($btn_url && $btn_label) : ?>
                <div class="reviews-block__header-right">
                    <?php get_template_part('components/button', '', [
                        'label'   => $btn_label,
                        'url'     => $btn_url,
                        'target'  => $btn_target,
                        'variant' => 'accent',
                        'icon'    => true,
                    ]); ?>
                </div>
            <?php endif; ?>

        </div>

        <?php if ($reviews) : ?>
            <?php if ($layout === 'grid') : ?>
                <div class="reviews-block__grid-wrap">
                    <div class="container">
                        <div class="reviews-block__grid">
                            <?php foreach ($reviews as $index => $review) :
                                $stars       = intval($review['rv_stars'] ?? 5);
                                $text        = $review['rv_text'] ?? '';
                                $name        = $review['rv_name'] ?? '';
                                $time        = $review['rv_time'] ?? '';
                                $avatar      = $review['rv_avatar'] ?? null;
                                $avatar_url  = is_array($avatar) ? ($avatar['url'] ?? '') : '';
                                $avatar_alt  = is_array($avatar) ? ($avatar['alt'] ?? $name) : $name;
                                $modal_id    = 'review-modal-' . $index;

                                $full_text_plain = trim(wp_strip_all_tags((string) $text));
                                $excerpt_plain   = wp_trim_words($full_text_plain, 32, '…');
                                $is_truncated    = ($full_text_plain !== '' && $excerpt_plain !== $full_text_plain);
                            ?>
                                <div class="reviews-block__card">

                                    <div class="reviews-block__card-stars" aria-label="<?= esc_attr($stars); ?> out of 5 stars">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <svg class="reviews-block__star reviews-block__star--<?= $i <= $stars ? 'filled' : 'empty'; ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                            </svg>
                                        <?php endfor; ?>
                                    </div>

                                    <?php if ($full_text_plain) : ?>
                                        <p class="reviews-block__card-text">
                                            “<?= esc_html($excerpt_plain); ?>”
                                            <?php if ($is_truncated) : ?>
                                                <button class="reviews-block__read-more is-visible" data-modal="<?= esc_attr($modal_id); ?>" aria-expanded="false" type="button">Meer lezen</button>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="reviews-block__card-reviewer">
                                        <?php if ($avatar_url) : ?>
                                            <img class="reviews-block__card-avatar" src="<?= esc_url($avatar_url); ?>" alt="<?= esc_attr($avatar_alt); ?>" width="50" height="50" loading="lazy">
                                        <?php else : ?>
                                            <span class="reviews-block__card-avatar reviews-block__card-avatar--fallback" aria-hidden="true"><?= esc_html(mb_substr($name, 0, 1)); ?></span>
                                        <?php endif; ?>
                                        <div class="reviews-block__card-reviewer-info">
                                            <?php if ($name) : ?>
                                                <span class="reviews-block__card-name"><?= esc_html($name); ?></span>
                                            <?php endif; ?>
                                            <?php if ($time) : ?>
                                                <span class="reviews-block__card-time"><?= esc_html($time); ?> geleden</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="reviews-block__slider-wrap">
                    <div class="reviews-block__slider-inner container">

                        <div class="slider slider--reviews">

                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    <?php foreach ($reviews as $index => $review) :
                                        $stars       = intval($review['rv_stars'] ?? 5);
                                        $text        = $review['rv_text'] ?? '';
                                        $name        = $review['rv_name'] ?? '';
                                        $time        = $review['rv_time'] ?? '';
                                        $avatar      = $review['rv_avatar'] ?? null;
                                        $avatar_url  = is_array($avatar) ? ($avatar['url'] ?? '') : '';
                                        $avatar_alt  = is_array($avatar) ? ($avatar['alt'] ?? $name) : $name;
                                        $modal_id    = 'review-modal-' . $index;

                                        $full_text_plain = trim(wp_strip_all_tags((string) $text));
                                        $excerpt_plain   = wp_trim_words($full_text_plain, 32, '…');
                                        $is_truncated    = ($full_text_plain !== '' && $excerpt_plain !== $full_text_plain);
                                    ?>
                                        <div class="swiper-slide">
                                            <div class="reviews-block__card">

                                                <div class="reviews-block__card-stars" aria-label="<?= esc_attr($stars); ?> out of 5 stars">
                                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                        <svg class="reviews-block__star reviews-block__star--<?= $i <= $stars ? 'filled' : 'empty'; ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                                        </svg>
                                                    <?php endfor; ?>
                                                </div>

                                                <?php if ($full_text_plain) : ?>
                                                    <p class="reviews-block__card-text">
                                                        “<?= esc_html($excerpt_plain); ?>”
                                                        <?php if ($is_truncated) : ?>
                                                            <button class="reviews-block__read-more is-visible" data-modal="<?= esc_attr($modal_id); ?>" aria-expanded="false" type="button">Meer lezen</button>
                                                        <?php endif; ?>
                                                    </p>
                                                <?php endif; ?>

                                                <div class="reviews-block__card-reviewer">
                                                    <?php if ($avatar_url) : ?>
                                                        <img class="reviews-block__card-avatar" src="<?= esc_url($avatar_url); ?>" alt="<?= esc_attr($avatar_alt); ?>" width="50" height="50" loading="lazy">
                                                    <?php else : ?>
                                                        <span class="reviews-block__card-avatar reviews-block__card-avatar--fallback" aria-hidden="true"><?= esc_html(mb_substr($name, 0, 1)); ?></span>
                                                    <?php endif; ?>
                                                    <div class="reviews-block__card-reviewer-info">
                                                        <?php if ($name) : ?>
                                                            <span class="reviews-block__card-name"><?= esc_html($name); ?></span>
                                                        <?php endif; ?>
                                                        <?php if ($time) : ?>
                                                            <span class="reviews-block__card-time"><?= esc_html($time); ?> geleden</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="slider-nav">
                                <div class="slider-nav__arrows">
                                    <button class="slider-nav__btn slider-nav__btn--prev" aria-label="<?php esc_attr_e('Previous slide', 'arehbo-theme'); ?>">
                                        <?= $arrow_left; ?>
                                    </button>
                                    <button class="slider-nav__btn slider-nav__btn--next" aria-label="<?php esc_attr_e('Next slide', 'arehbo-theme'); ?>">
                                        <?= $arrow_right; ?>
                                    </button>
                                </div>
                                <div class="slider-nav__progress swiper-pagination"></div>
                            </div>

                        </div>

                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($reviews as $index => $review) :
                $text     = $review['rv_text'] ?? '';
                $name     = $review['rv_name'] ?? '';
                $stars    = intval($review['rv_stars'] ?? 5);
                $modal_id = 'review-modal-' . $index;
                if (!$text) continue;
            ?>
                <div class="reviews-block__modal" id="<?= esc_attr($modal_id); ?>" role="dialog" aria-modal="true" aria-label="<?= esc_attr($name); ?>" hidden>
                    <div class="reviews-block__modal-overlay"></div>
                    <div class="reviews-block__modal-box">
                        <button class="reviews-block__modal-close" aria-label="<?php esc_attr_e('Close', 'arehbo-theme'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                                <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                        <div class="reviews-block__modal-stars" aria-label="<?= esc_attr($stars); ?> out of 5 stars">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <svg class="reviews-block__star reviews-block__star--<?= $i <= $stars ? 'filled' : 'empty'; ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <p class="reviews-block__modal-text"><?= nl2br(esc_html($text)); ?></p>
                        <?php if ($name) : ?>
                            <p class="reviews-block__modal-name"><?= esc_html($name); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</section>
