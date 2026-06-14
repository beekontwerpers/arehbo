<?php

$full_id   = get_full_id(get_field('id'));
$layout    = get_field('layout') ?: 'hero';
$bg_id     = get_field('background_image');
$video_id  = get_field('background_video');
$title     = get_field('title');
$cards     = get_field('cards') ?: [];
$scroll    = get_field('scroll_target');
$hero_title = $title ?: get_the_title();

$bg_url    = $bg_id    ? wp_get_attachment_image_url($bg_id, 'full') : '';
$video_url = $video_id ? wp_get_attachment_url($video_id)            : '';
$cards     = array_slice($cards, 0, 3);
$is_next_section_scroll = ($scroll === 'next-section');

$form_description = get_field('formulier_description');
$form_buttons     = get_field('formulier_buttons') ?: [];
$form_img_id      = get_field('formulier_image');
$form_id          = get_field('formulier_form_id');

$form_img_url = $form_img_id ? wp_get_attachment_image_url($form_img_id, 'full') : '';
$form_img_alt = $form_img_id
    ? (get_post_meta($form_img_id, '_wp_attachment_image_alt', true) ?: $title)
    : $title;

$form_bg_class = 'bg-light';

$default_background  = 'light-blue';
$default_description = get_field('default_description');
$default_buttons     = get_field('default_buttons') ?: [];
$default_img_id      = get_field('default_image');
$default_show_usps   = (bool) get_field('default_show_usps');
$default_usps        = get_field('default_usp_items') ?: [];

$default_img_url = $default_img_id ? wp_get_attachment_image_url($default_img_id, 'full') : '';
$default_img_alt = $default_img_id
    ? (get_post_meta($default_img_id, '_wp_attachment_image_alt', true) ?: $title)
    : $title;

$default_bg_variant = 'light';
$default_bg_class   = ($default_background === 'dark-blue') ? 'bg-dark' : 'bg-light';

?>

<section <?= $full_id; ?> class="hero-banner hero-banner--<?= esc_attr($layout); ?>">

    <?php if ($layout === 'hero') : ?>

        <div class="hb-hero"
            <?php if ($bg_url && !$video_url) : ?>
                style="background-image: url('<?= esc_url($bg_url); ?>');"
            <?php endif; ?>>

            <?php if ($video_url) : ?>
                <video class="hb-hero__video" autoplay muted loop playsinline>
                    <source src="<?= esc_url($video_url); ?>" type="video/<?= esc_attr(pathinfo($video_url, PATHINFO_EXTENSION)); ?>">
                </video>
            <?php endif; ?>

            <div class="hb-hero__inner container">

                <h1 class="hb-hero__title"><?= esc_html($hero_title); ?></h1>

                <?php if (!empty($cards)) : ?>
                    <div class="hb-hero__cards">
                        <?php foreach ($cards as $card) :
                            $card_title  = $card['card_title'] ?? '';
                            $card_link   = $card['card_link'] ?? [];
                            $link_url    = $card_link['url'] ?? '#';
                            $link_target = $card_link['target'] ?? '_self';
                            if (empty($card_title)) continue;
                        ?>
                            <a class="hb-card"
                                href="<?= esc_url($link_url); ?>"
                                <?php if ($link_target === '_blank') echo 'target="_blank" rel="noopener noreferrer"'; ?>
                            >
                                <span class="hb-card__title"><?= esc_html($card_title); ?></span>
                                <div class="tile-card__btn-wrap">
                                    <span class="tile-card__btn-label" aria-hidden="true">MEER INFORMATIE</span>
                                    <span class="tile-card__btn" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                            <path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($scroll) : ?>
                    <div class="hb-scroll-row">
                        <div class="hb-scroll">
                            <span class="hb-scroll__label">Scroll verder</span>
                            <a class="hb-scroll__btn"
                                href="<?= $is_next_section_scroll ? '#' : '#' . esc_attr(ltrim($scroll, '#')); ?>"
                                <?php if ($is_next_section_scroll) : ?>
                                    data-scroll-target="next-section"
                                <?php endif; ?>
                                aria-label="Scroll verder naar inhoud"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" aria-hidden="true">
                                    <path d="M12 5v14M5 13l7 7 7-7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <?php get_template_part('components/colorbar'); ?>

    <?php elseif ($layout === 'default') : ?>

        <div class="hb-default hb-default--<?= esc_attr($default_background); ?> <?= esc_attr($default_bg_class); ?><?= ($default_show_usps && !empty($default_usps)) ? ' hb-default--has-usps' : ''; ?>">
            <div class="hb-default__inner container">

                <?php $breadcrumb_title = get_the_title(); ?>
                <nav class="archive-breadcrumbs" aria-label="Breadcrumb">
                    <a class="archive-breadcrumbs__link" href="<?= esc_url(home_url('/')); ?>">Home</a>
                    <span class="archive-breadcrumbs__sep" aria-hidden="true">/</span>
                    <span class="archive-breadcrumbs__current" aria-current="page"><?= esc_html($breadcrumb_title); ?></span>
                </nav>

                <hr class="archive-hero__divider">

                <div class="hb-default__hero">

                    <div class="hb-default__text">

                        <h1 class="hb-default__title"><?= esc_html($hero_title); ?></h1>

                        <?php if ($default_description) : ?>
                            <div class="hb-default__description">
                                <?= wp_kses_post($default_description); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($default_buttons)) : ?>
                            <div class="hb-default__buttons">
                                <?php foreach (array_slice($default_buttons, 0, 3) as $i => $btn) :
                                    $link   = $btn['link'] ?? [];
                                    $label  = $link['title'] ?? '';
                                    $url    = $link['url'] ?? '';
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

                    <?php if ($default_img_url) : ?>
                        <div class="hb-default__image-wrap">
                            <img
                                class="hb-default__image"
                                src="<?= esc_url($default_img_url); ?>"
                                alt="<?= esc_attr($default_img_alt); ?>"
                                width="635"
                                height="357"
                                loading="eager"
                            >
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        </div>

        <?php if ($default_show_usps && !empty($default_usps)) : ?>
            <section class="hb-usps usp-list <?= esc_attr($default_bg_class); ?>">
                <div class="container">
                    <div class="usp-list__wrapper">
                        <div class="container">
                            <div class="usp-list__track">
                                <?php get_template_part('components/usp-items', '', [
                                    'items'     => array_slice($default_usps, 0, 3),
                                    'show_icon' => true,
                                ]); ?>

                                <div class="usp-list__clone" aria-hidden="true">
                                    <?php get_template_part('components/usp-items', '', [
                                        'items'     => array_slice($default_usps, 0, 3),
                                        'show_icon' => true,
                                        'label'     => '',
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php get_template_part('components/colorbar'); ?>

    <?php elseif ($layout === 'formulier') : ?>

        <div class="hb-formulier <?= esc_attr($form_bg_class); ?>">
            <div class="hb-formulier__inner container">

                <?php $breadcrumb_title = get_the_title(); ?>
                <nav class="archive-breadcrumbs" aria-label="Breadcrumb">
                    <a class="archive-breadcrumbs__link" href="<?= esc_url(home_url('/')); ?>">Home</a>
                    <span class="archive-breadcrumbs__sep" aria-hidden="true">/</span>
                    <span class="archive-breadcrumbs__current" aria-current="page"><?= esc_html($breadcrumb_title); ?></span>
                </nav>

                <hr class="archive-hero__divider">

                <div class="hb-formulier__grid">

                    <div class="hb-formulier__left">

                        <h1 class="hb-formulier__title"><?= esc_html($hero_title); ?></h1>

                        <?php if ($form_description) : ?>
                            <div class="hb-formulier__description">
                                <?= wp_kses_post($form_description); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($form_buttons)) : ?>
                            <div class="hb-formulier__buttons">
                                <?php foreach (array_slice($form_buttons, 0, 2) as $i => $btn) :
                                    $link   = $btn['link'] ?? [];
                                    $label  = $link['title'] ?? '';
                                    $url    = $link['url'] ?? '';
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

                        <?php if ($form_img_url) : ?>
                            <div class="hb-formulier__image-wrap">
                                <img
                                    class="hb-formulier__image"
                                    src="<?= esc_url($form_img_url); ?>"
                                    alt="<?= esc_attr($form_img_alt); ?>"
                                    loading="lazy"
                                >
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="hb-formulier__form-card">

                        <?php if ($form_id && class_exists('GFForms')) : ?>
                            <div class="hb-formulier__form-card-body">
                                <?php gravity_form((int) $form_id, false, false, false, null, true); ?>
                            </div>
                        <?php elseif ($form_id) : ?>
                            <div class="hb-formulier__form-card-body">
                                <?= do_shortcode('[gravityforms id="' . intval($form_id) . '" ajax="true"]'); ?>
                            </div>
                        <?php else : ?>
                            <p class="hb-formulier__form-card-empty">No form selected.</p>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
        </div>

        <?php get_template_part('components/colorbar'); ?>

    <?php endif; ?>

</section>
