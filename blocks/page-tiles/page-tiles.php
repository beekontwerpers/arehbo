<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space      = get_spacing_class(get_field('space'));
$full_id    = get_full_id(get_field('id'));
$background = get_field('background') ?: 'light';
$layout     = get_field('layout')     ?: 'grid';
$description = get_field('description');
$cards      = get_field('cards');

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

if (empty($cards)) {
    return;
}

$arrow_icon = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$arrow_left = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M16 10H4M4 10L9 5M4 10L9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$arrow_right = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

?>

<?php if ($layout === 'grid') : ?>

<section <?= $full_id; ?> class="page-tiles page-tiles--grid <?= esc_attr($bg_class); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">
            <div class="page-tiles__grid">

                <div class="page-tiles__header">
                    <?php if ($description) : ?>
                        <div class="page-tiles__description"><?= $description ?></div>
                    <?php endif; ?>
                </div>

                <?php foreach ($cards as $card) :
                    $image_id   = $card['image']  ?? null;
                    $card_title = $card['title']  ?? '';
                    $button     = $card['button'] ?? null;

                    if (!$card_title && !$image_id) continue;

                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
                    $image_alt = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '') : '';
                ?>
                    <div class="tile-card">

                        <?php if ($button && !empty($button['url'])) : ?>
                            <a
                                href="<?= esc_url($button['url']); ?>"
                                class="tile-card__link"
                                target="<?= esc_attr($button['target'] ?: '_self'); ?>"
                                aria-label="<?= esc_attr($button['title'] ?: $card_title); ?>"
                            ></a>
                        <?php endif; ?>

                        <?php if ($image_url) : ?>
                            <img
                                class="tile-card__image"
                                src="<?= esc_url($image_url); ?>"
                                alt="<?= esc_attr($image_alt); ?>"
                                width="305"
                                height="215"
                                loading="lazy"
                            >
                        <?php endif; ?>

                        <?php if ($card_title) : ?>
                            <h3 class="tile-card__title"><?= esc_html($card_title); ?></h3>
                        <?php endif; ?>

                        <?php if ($button && !empty($button['url'])) : ?>
                            <div class="tile-card__btn-wrap">
                                <span class="tile-card__btn-label" aria-hidden="true">MEER INFORMATIE</span>
                                <span class="tile-card__btn" aria-hidden="true"><?= $arrow_icon; ?></span>
                            </div>
                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>

<?php elseif ($layout === 'slider') : ?>

<section <?= $full_id; ?> class="page-tiles page-tiles--slider <?= esc_attr($bg_class); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">

            <?php if ($description) : ?>
                <div class="page-tiles__header page-tiles__header--centered">
                    <?php if ($description) : ?>
                        <div class="page-tiles__description"><?= $description ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="page-tiles__swiper-wrap">
                <div class="swiper">
                    <div class="swiper-wrapper">

                        <?php foreach ($cards as $card) :
                            $image_id   = $card['image']  ?? null;
                            $card_title = $card['title']  ?? '';
                            $button     = $card['button'] ?? null;

                            if (!$image_id && !$card_title) continue;

                            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
                            $image_alt = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '') : '';

                            $has_link     = $button && !empty($button['url']);
                            $overlay_tag  = $has_link ? 'a' : 'div';
                            $overlay_attrs = $has_link
                                ? sprintf(
                                    'href="%s" target="%s" aria-label="%s"',
                                    esc_url($button['url']),
                                    esc_attr($button['target'] ?: '_self'),
                                    esc_attr($button['title'] ?: $card_title)
                                )
                                : '';
                        ?>
                            <div class="swiper-slide">
                                <div
                                    class="tile-slide"
                                    <?php if ($image_url) : ?>style="background-image: url('<?= esc_url($image_url); ?>')"<?php endif; ?>
                                >
                                    <<?= $overlay_tag; ?> class="tile-slide__overlay" <?= $overlay_attrs; ?>>

                                        <?php if ($card_title) : ?>
                                            <h3 class="tile-slide__title"><?= esc_html($card_title); ?></h3>
                                        <?php endif; ?>

                                        <?php if ($has_link) : ?>
                                            <div class="tile-card__btn-wrap tile-slide__btn-wrap">
                                                <span class="tile-card__btn-label" aria-hidden="true">MEER INFORMATIE</span>
                                                <span class="tile-card__btn" aria-hidden="true"><?= $arrow_icon; ?></span>
                                            </div>
                                        <?php endif; ?>

                                    </<?= $overlay_tag; ?>>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>

            <div class="page-tiles__nav slider-nav">
                <div class="slider-nav__arrows">
                    <button class="slider-nav__btn slider-nav__btn--prev" type="button" aria-label="Vorige pagina">
                        <?= $arrow_left; ?>
                    </button>
                    <button class="slider-nav__btn slider-nav__btn--next" type="button" aria-label="Volgende pagina">
                        <?= $arrow_right; ?>
                    </button>
                </div>
                <div class="slider-nav__progress page-tiles__progress swiper-pagination"></div>
            </div>

        </div>
    </div>
</section>

<?php endif; ?>
