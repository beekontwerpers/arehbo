<?php

$space       = get_spacing_class(get_field('space'));
$full_id     = get_full_id(get_field('id'));
$background  = get_field('cta_background');
$image_id    = get_field('image');
$image_url   = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
$image_alt   = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '') : '';
$image_label = get_field('image_label');
$title       = get_field('title');
$description = get_field('description');
$buttons_raw = (array) get_field('buttons');
$buttons     = array_values(array_filter($buttons_raw, function ($row) {
    $link = is_array($row) ? ($row['link'] ?? null) : null;
    return is_array($link) && !empty($link['url']) && !empty($link['title']);
}));

// Fallbacks from "Blokken" options page (blocks-settings).
if (empty($image_id))    $image_id    = (int) (get_field('blocks_cta_image', 'option') ?: get_field('blocks_cta_image', 'options'));
if (empty($image_label)) $image_label = get_field('blocks_cta_image_label', 'option') ?: get_field('blocks_cta_image_label', 'options');
if (empty($title))       $title       = get_field('blocks_cta_title', 'option') ?: get_field('blocks_cta_title', 'options');
if (empty($description)) $description = get_field('blocks_cta_description', 'option') ?: get_field('blocks_cta_description', 'options');
if (empty($buttons)) {
    $fallback_buttons_raw = (array) (get_field('blocks_cta_buttons', 'option') ?: get_field('blocks_cta_buttons', 'options'));
    $buttons = array_values(array_filter($fallback_buttons_raw, function ($row) {
        $link = is_array($row) ? ($row['link'] ?? null) : null;
        return is_array($link) && !empty($link['url']) && !empty($link['title']);
    }));
}

$background = $background ?: 'light';

$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
$image_alt = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '') : '';
$buttons   = array_slice($buttons, 0, 2);

if ($background === 'white') {
    $background = 'light';
}

// CTA block requires a title; fallback title can be provided via Blokken settings.
if (empty($title)) {
    return;
}

?>

<section <?= $full_id; ?> class="cta-section cta-section--<?= esc_attr($background); ?>">
    <div class="<?= esc_attr($space); ?> container">
        <div class="cta">
            <div class="cta__grid">

                <?php if ($image_url) : ?>
                    <div class="cta__media">
                        <img
                            class="cta__image"
                            src="<?= esc_url($image_url); ?>"
                            alt="<?= esc_attr($image_alt); ?>"
                            width="440"
                            height="362"
                            loading="lazy"
                        >
                        <?php if ($image_label) : ?>
                            <span class="cta__badge"><?= esc_html($image_label); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="cta__content">

                    <div class="cta__text">
                        <h2 class="cta__title"><?= esc_html($title); ?></h2>
                        <?php if ($description) : ?>
                            <p class="cta__description"><?= esc_html($description); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($buttons)) : ?>
                        <div class="cta__buttons">
                            <?php foreach ($buttons as $i => $btn) :
                                $link = $btn['link'] ?? null;
                                $label = $link['title'] ?? '';
                                $url   = $link['url']   ?? '';
                                $target = $link['target'] ?? '_self';
                                $variant = $btn['variant'] ?? ($i === 0 ? 'accent' : 'outline');

                                if (empty($label) || empty($url)) continue;
                            ?>
                                <?php get_template_part('components/button', '', [
                                    'label'   => $label,
                                    'url'     => $url,
                                    'target'  => $target,
                                    'variant' => $variant,
                                    'icon'    => $variant !== 'outline',
                                ]); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
                
            </div>
        </div>
    </div>
</section>
