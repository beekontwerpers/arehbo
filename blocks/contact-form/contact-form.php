<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space      = get_spacing_class(get_field('space'));
$full_id    = get_full_id(get_field('id'));
$background = get_field('cf_background') ?: 'dark';

$description = get_field('cf_description');
$author_raw  = get_field('cf_author');
$form_id     = (int) get_field('cf_form_id');

if ($author_raw instanceof WP_Post) {
    $author_post = $author_raw;
} elseif (is_numeric($author_raw) && $author_raw > 0) {
    $author_post = get_post((int) $author_raw);
} else {
    $author_post = null;
}

$author_name    = $author_post ? get_the_title($author_post) : '';
$author_img_id  = $author_post ? (get_field('foto', $author_post->ID) ?: get_post_thumbnail_id($author_post->ID)) : 0;
$author_img_url = $author_img_id ? wp_get_attachment_image_url($author_img_id, [62, 62]) : '';
$author_img_alt = $author_img_id
    ? (get_post_meta($author_img_id, '_wp_attachment_image_alt', true) ?: $author_name)
    : $author_name;

?>

<section <?= $full_id; ?> class="contact-form-block contact-form-block--<?= esc_attr($background); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="contact-form-block__layout container">

            <div class="contact-form-block__left">
                <?php if ($description) : ?>
                    <div class="contact-form-block__description">
                        <?= wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if ($author_name) : ?>
                    <div class="contact-form-block__author">

                        <?php if ($author_img_url) : ?>
                            <img
                                class="contact-form-block__author-avatar"
                                src="<?= esc_url($author_img_url); ?>"
                                alt="<?= esc_attr($author_img_alt); ?>"
                                width="62"
                                height="62"
                                loading="lazy"
                            >
                        <?php endif; ?>

                        <div class="contact-form-block__author-info">
                            <span class="contact-form-block__author-label">Stel je vragen aan:</span>
                            <span class="contact-form-block__author-name"><?= esc_html($author_name); ?></span>
                        </div>

                    </div>
                <?php endif; ?>

            </div>

            <div class="contact-form-block__right">
                <div class="contact-form-block__form-wrap">
                    <?php
                    if ($form_id && class_exists('GFForms')) {
                        gravity_form($form_id, false, false, false, null, true);
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>
