<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space          = get_spacing_class(get_field('space'));
$full_id        = get_full_id(get_field('id'));
$layout         = get_field('layout') ?: 'photo';
$background     = get_field('background') ?: 'dark';
$title          = get_field('title');
$photo_slides   = get_field('photo_slides');
$process_slides = get_field('process_slides');

if (empty($title) && empty($photo_slides) && empty($process_slides)) {
    return;
}

$slides_html = [];

if ($layout === 'photo' && !empty($photo_slides)) {

    foreach ($photo_slides as $slide) {
        $image_id = $slide['image'] ?? null;
        if (!$image_id) continue;

        $image_url = wp_get_attachment_image_url($image_id, 'full');
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '';

        ob_start();
        ?>
        <img class="slider-photo__image" src="<?= esc_url($image_url); ?>" alt="<?= esc_attr($image_alt); ?>" loading="lazy">
        <?php
        $slides_html[] = ob_get_clean();
    }

} elseif ($layout === 'process' && !empty($process_slides)) {

    foreach ($process_slides as $slide) {
        $image_id  = $slide['image']            ?? null;
        $date      = $slide['date']             ?? '';
        $card_title = $slide['card_title']      ?? '';
        $card_desc = $slide['card_description'] ?? '';

        if (!$image_id && !$card_title) continue;

        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
        $image_alt = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '') : '';

        ob_start();
        ?>
        <div class="process-card">

            <?php if ($image_url) : ?>
                <div class="process-card__media">
                    <img class="process-card__image" src="<?= esc_url($image_url); ?>" alt="<?= esc_attr($image_alt); ?>" loading="lazy">
                </div>
            <?php endif; ?>

            <div class="process-card__body">
                <?php if ($date) : ?>
                    <span class="process-card__date"><?= esc_html($date); ?></span>
                <?php endif; ?>
                <div class="process-card__text">
                    <?php if ($card_title) : ?>
                        <h3 class="process-card__title"><?= esc_html($card_title); ?></h3>
                    <?php endif; ?>
                    <?php if ($card_desc) : ?>
                        <p class="process-card__description"><?= esc_html($card_desc); ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php
        $slides_html[] = ob_get_clean();
    }

}

?>

<?php
$legacy_map = [
    'primary'     => 'dark',
    'dark'        => 'dark',
    'light'       => 'light',
    'white'       => 'light',
    'transparent' => 'light',
];
$background = $legacy_map[$background] ?? 'dark';
?>

<section <?= $full_id; ?> class="slider-block slider-block--<?= esc_attr($layout); ?> <?= $background === 'dark' ? 'bg-dark' : 'bg-light'; ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">

        <?php if ($title) : ?>
            <h2 class="slider-block__title"><?= esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if (!empty($slides_html)) : ?>
            <?php get_template_part('components/slider', '', [
                'slides' => $slides_html,
                'layout' => $layout,
            ]); ?>
        <?php endif; ?>

        </div>
    </div>
</section>
