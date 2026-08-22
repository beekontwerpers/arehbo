<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space    = get_spacing_class(get_field('space'));
$full_id  = get_full_id(get_field('id'));

$title   = get_field('loc_title');
$desc    = get_field('loc_description');
$buttons = get_field('loc_buttons') ?: [];

$pins    = get_field('loc_pins') ?: [];
$map_url = get_template_directory_uri() . '/blocks/locations/map-nl.svg';

?>

<section <?= $full_id; ?> class="locations-block">
    <div class="<?= esc_attr($space); ?>">
        <div class="locations-block__layout container">

            <div class="locations-block__left">

                <?php if ($desc) : ?>
                    <div class="locations-block__description">
                        <?= $desc ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($buttons)) : ?>
                    <div class="locations-block__btn-wrap">
                        <?php foreach ($buttons as $btn) :
                            $link = $btn['link'] ?? [];
                            $label = $link['title'] ?? '';
                            if (empty($label) || empty($link['url'])) continue;

                            $variant = $btn['variant'] ?? 'accent';
                        ?>
                            <?php get_template_part('components/button', '', [
                                'label'   => $label,
                                'url'     => $link['url'],
                                'target'  => $link['target'] ?? '_self',
                                'variant' => $variant,
                                'icon'    => $variant !== 'outline',
                            ]); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="locations-block__right" aria-hidden="true">
                <div class="locations-block__map-wrap">
                    <img src="<?= esc_url($map_url); ?>" alt="" aria-hidden="true" class="locations-block__map-img">
                    <?php foreach ($pins as $pin) :
                        $name = $pin['pin_name'] ?? '';
                        $x    = floatval($pin['pin_x'] ?? 50);
                        $y    = floatval($pin['pin_y'] ?? 50);
                    ?>
                        <span class="locations-pin" style="left:<?= esc_attr($x); ?>%;top:<?= esc_attr($y); ?>%;">
                            <svg class="locations-pin__icon" xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36" fill="none">
                                <path d="M14 0C6.268 0 0 6.268 0 14c0 9.8 14 22 14 22s14-12.2 14-22C28 6.268 21.732 0 14 0z" fill="#013674" stroke="rgba(255,255,255,0.35)" stroke-width="2"/>
                                <circle cx="14" cy="13" r="5" fill="white"/>
                            </svg>
                            <?php if ($name) : ?>
                                <span class="locations-pin__label"><?= esc_html($name); ?></span>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>
