<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space       = get_spacing_class(get_field('space'));
$full_id     = get_full_id(get_field('id'));
$background  = get_field('background') ?: 'light';
$reverse     = get_field('reverse');
$image_id    = get_field('image');
$description = get_field('description');
$button      = get_field('button');

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

if (empty($image_id) && empty($description)) {
    return;
}

$section_class = 'image-text ' . $bg_class;
$is_reverse = ($reverse === 'reverse' || $reverse === 1 || $reverse === true || $reverse === '1');
if ($is_reverse) {
    $section_class .= ' image-text--reverse';
}

$image_alt   = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
$image_title = $image_id ? get_the_title($image_id) : '';
$image_url   = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';

?>

<section <?= $full_id; ?> class="<?php echo esc_attr($section_class); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">
        <div class="image-text__grid">

            <?php if ($image_url) : ?>
                <div class="image-text__media">
                    <img
                        src="<?php echo esc_url($image_url); ?>"
                        alt="<?php echo esc_attr($image_alt); ?>"
                        title="<?php echo esc_attr($image_title); ?>"
                        class="image-text__image"
                    >
                </div>
            <?php endif; ?>

            <div class="image-text__content">
                <?php get_template_part('components/text-content', '', [
                    'description'    => $description,
                    'button'         => $button,
                    'button_variant' => 'accent',
                ]); ?>
            </div>

        </div>
        </div>
    </div>
</section>
