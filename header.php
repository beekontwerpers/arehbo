<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Server Side Tracking by Taggrs.io -->
	
<script>
    (function(w,d,s,l,i){
        w[l]=w[l]||[];
        w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s);
        j.async=true;
        j.src='https://flow.arehbo.nl/'+i+'.js';
        f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','55evdvu1h0');
</script>

<!-- End Server Side Tracking by Taggrs.io -->
	
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	
	<!-- Server Side Tracking by Taggrs.io (noscript) -->
	
<noscript>
    <iframe src="https://flow.arehbo.nl/55evdvu1h0.html" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
	
<!-- End Server Side Tracking by Taggrs.io (noscript) -->

<?php wp_body_open(); ?>

<?php

$logo_id       = get_field('logo', 'option');
$logo_url      = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
$logo_alt      = $logo_id ? (get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name')) : get_bloginfo('name');

$phone_number  = get_field('phone_number', 'option');
$phone_tel     = $phone_number ? preg_replace('/[^+\d]/', '', $phone_number) : '';
$phone_img_id  = get_field('phone_image', 'option');
$phone_img_url = $phone_img_id ? wp_get_attachment_image_url($phone_img_id, 'thumbnail') : '';
$phone_img_alt = $phone_img_id ? (get_post_meta($phone_img_id, '_wp_attachment_image_alt', true) ?: '') : '';

$cta_button = null;
if (is_singular()) {
    $cta_button = get_field('cta_button', get_queried_object_id());
}
if (empty($cta_button)) {
    $cta_button = get_field('cta_button', 'option');
}
$nav_items     = get_field('navigation', 'option') ?: [];
$header_bg     = 'bg-light-blue';

$arrow_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$chevron_left_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$chevron_right_svg = '<svg class="site-nav__chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false" fill="none"><path d="M9 4L17 12L9 20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>

<header class="site-header <?= esc_attr($header_bg); ?>" role="banner">

    <div class="site-header__bar">
        <div class="site-header__inner">

            <a class="site-header__logo" href="<?= esc_url(home_url('/')); ?>" aria-label="<?= esc_attr(get_bloginfo('name')); ?>">
                <?php if ($logo_url) : ?>
                    <img src="<?= esc_url($logo_url); ?>" alt="<?= esc_attr($logo_alt); ?>" height="40" loading="eager">
                <?php else : ?>
                    <?= esc_html(get_bloginfo('name')); ?>
                <?php endif; ?>
            </a>

            <?php if (!empty($nav_items)) : ?>
                <nav class="site-nav desktop-only" aria-label="<?php esc_attr_e('Main navigation', 'arehbo-theme'); ?>">
                    <ul class="site-nav__list" role="list">
                        <?php foreach ($nav_items as $item) :
                            $is_mega      = !empty($item['is_mega_menu']);
                            $has_dropdown = !$is_mega && !empty($item['has_dropdown']) && !empty($item['dropdown_items']);
                            $link         = $item['link'] ?? null;
                            $label        = ($link['title'] ?? '') ?: ($item['label'] ?? '');
                            if (!$label) continue;
                        ?>
                            <li class="site-nav__item <?= $is_mega ? 'site-nav__item--has-mega' : ($has_dropdown ? 'site-nav__item--has-dropdown' : ''); ?>">

                                <?php if ($is_mega) : ?>
                                    <?php if ($link) : ?>
                                        <a class="site-nav__link" href="<?= esc_url($link['url']); ?>" <?= $link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                            <?= esc_html($label); ?>
                                        </a>
                                    <?php else : ?>
                                        <span class="site-nav__link"><?= esc_html($label); ?></span>
                                    <?php endif; ?>
                                    <button class="site-nav__mega-toggle" type="button" aria-expanded="false" aria-label="<?= esc_attr($label); ?> menu openen">
                                        <?= $chevron_right_svg; ?>
                                    </button>
                                <?php elseif ($has_dropdown) : ?>
                                    <?php if ($link) : ?>
                                        <a class="site-nav__link" href="<?= esc_url($link['url']); ?>" <?= $link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                            <?= esc_html($label); ?>
                                        </a>
                                    <?php else : ?>
                                        <span class="site-nav__link"><?= esc_html($label); ?></span>
                                    <?php endif; ?>
                                    <button class="site-nav__dropdown-toggle" type="button" aria-expanded="false" aria-label="<?= esc_attr($label); ?> menu openen">
                                        <?= $chevron_right_svg; ?>
                                    </button>
                                <?php elseif ($link) : ?>
                                    <a class="site-nav__link" href="<?= esc_url($link['url']); ?>" <?= $link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                        <?= esc_html($label); ?>
                                    </a>
                                <?php else : ?>
                                    <span class="site-nav__link"><?= esc_html($label); ?></span>
                                <?php endif; ?>

                                <?php if ($is_mega) :
                                    $mega_bg      = $item['mega_bg'] ?? 'light-blue';
                                    $mega_cols    = $item['mega_columns'] ?? [];
                                    $cta_intro    = $item['mega_cta_intro'] ?? '';
                                    $cta_action   = $item['mega_cta_action'] ?? '';
                                    $cta_link     = $item['mega_cta_link'] ?? null;
                                ?>
                                    <div class="mega-menu mega-menu--<?= esc_attr($mega_bg); ?>" role="region">
                                        <div class="mega-menu__inner">

                                            <div class="mega-menu__columns">
                                                <?php foreach ($mega_cols as $col) :
                                                    $col_links = $col['column_links'] ?? [];
                                                    $view_all  = $col['column_view_all'] ?? null;
                                                ?>
                                                    <div class="mega-menu__column">
                                                        <?php if (!empty($col['column_title'])) : ?>
                                                            <p class="mega-menu__col-title"><?= esc_html($col['column_title']); ?>:</p>
                                                        <?php endif; ?>

                                                        <?php if (!empty($col_links)) : ?>
                                                            <ul class="mega-menu__links" role="list">
                                                                <?php foreach ($col_links as $cl) :
                                                                    $cl_link = $cl['link'] ?? null;
                                                                    if (!$cl_link) continue;
                                                                ?>
                                                                    <li>
                                                                        <a class="mega-menu__link"
                                                                           href="<?= esc_url($cl_link['url']); ?>"
                                                                           <?= $cl_link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                                                            <?= esc_html($cl_link['title']); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>

                                                        <?php if ($view_all) : ?>
                                                            <a class="mega-menu__view-all"
                                                               href="<?= esc_url($view_all['url']); ?>"
                                                               <?= $view_all['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                                                <?= esc_html($view_all['title']); ?>
                                                                <?= $arrow_svg; ?>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <?php if ($cta_link) : ?>
                                                <a class="mega-menu__cta"
                                                   href="<?= esc_url($cta_link['url']); ?>"
                                                   <?= $cta_link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                                    <?php if ($cta_intro) : ?>
                                                        <span class="mega-menu__cta-intro"><?= esc_html($cta_intro); ?></span>
                                                    <?php endif; ?>
                                                    <?php if ($cta_action) : ?>
                                                        <span class="mega-menu__cta-action">
                                                            <?= esc_html($cta_action); ?>
                                                            <?= $arrow_svg; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </a>
                                            <?php endif; ?>

                                        </div>
                                    </div>

                                <?php elseif ($has_dropdown) : ?>
                                    <div class="site-nav__dropdown">
                                        <ul class="site-nav__dropdown-list" role="list">
                                            <?php foreach ($item['dropdown_items'] as $dd) :
                                                $dd_link = $dd['link'] ?? null;
                                                if (!$dd_link) continue;
                                            ?>
                                                <li>
                                                    <a class="site-nav__dropdown-link"
                                                       href="<?= esc_url($dd_link['url']); ?>"
                                                       <?= $dd_link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                                        <?= esc_html($dd_link['title']); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="site-header__actions">

                <?php if ($phone_number) : ?>
                    <div class="header-phone">
                        <a class="header-phone__btn" href="tel:<?= esc_attr($phone_tel); ?>" aria-label="<?= esc_attr($phone_number); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21.276" height="21.317" viewBox="0 0 21.276 21.317" aria-hidden="true" focusable="false">
                                <g transform="translate(0.999 1)">
                                    <path d="M513.388,2006.461v2.908a1.948,1.948,0,0,1-.163.785,1.991,1.991,0,0,1-.465.649,1.919,1.919,0,0,1-.689.407,1.853,1.853,0,0,1-.8.1,19.177,19.177,0,0,1-8.364-2.976,18.905,18.905,0,0,1-5.815-5.815,19.189,19.189,0,0,1-2.976-8.4,1.943,1.943,0,0,1,.093-.795,2.018,2.018,0,0,1,.406-.688,1.941,1.941,0,0,1,1.43-.63h2.908a2,2,0,0,1,1.283.465,1.958,1.958,0,0,1,.655,1.2,12.482,12.482,0,0,0,.678,2.724,1.939,1.939,0,0,1-.436,2.045l-1.231,1.231a15.5,15.5,0,0,0,5.815,5.815l1.231-1.231a1.925,1.925,0,0,1,.959-.514,1.977,1.977,0,0,1,1.087.078,12.545,12.545,0,0,0,2.724.678,1.944,1.944,0,0,1,1.667,1.968Z" transform="translate(-494.112 -1992)" fill="none" stroke="#004081" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                </g>
                            </svg>
                        </a>
                        <div class="header-phone__card desktop-only" aria-hidden="true">
                            <?php if ($phone_img_url) : ?>
                                <img class="header-phone__image" src="<?= esc_url($phone_img_url); ?>" alt="<?= esc_attr($phone_img_alt); ?>" width="45" height="45" loading="lazy">
                            <?php endif; ?>
                            <span class="header-phone__number"><?= esc_html($phone_number); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($cta_button) : ?>
                    <a class="btn btn--accent site-header__cta desktop-only"
                       href="<?= esc_url($cta_button['url']); ?>"
                       <?= $cta_button['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                        <?= esc_html($cta_button['title']); ?>
                    </a>
                <?php endif; ?>

                <button class="hamburger mobile-only"
                        aria-label="<?php esc_attr_e('Open menu', 'arehbo-theme'); ?>"
                        aria-expanded="false"
                        aria-controls="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

            </div>

        </div>
    </div>

    <div class="mobile-menu" id="mobile-menu" aria-hidden="true">
        <div class="mobile-menu__viewport">
            <div class="mobile-menu__track" id="mobile-track">

                <div class="mobile-menu__panel">
                    <div class="mobile-menu__content container">

                        <?php if (!empty($nav_items)) :
                            $panel_index = 0;
                        ?>
                            <nav aria-label="<?php esc_attr_e('Mobile navigation', 'arehbo-theme'); ?>">
                                <ul class="mobile-nav__list" role="list">
                                    <?php foreach ($nav_items as $item_idx => $item) :
                                        $is_mega      = !empty($item['is_mega_menu']);
                                        $has_dropdown = !$is_mega && !empty($item['has_dropdown']) && !empty($item['dropdown_items']);
                                        $link         = $item['link'] ?? null;
                                        $label        = ($link['title'] ?? '') ?: ($item['label'] ?? '');
                                        if (!$label) continue;

                                        if ($is_mega || $has_dropdown) $panel_index++;
                                        $this_panel = $panel_index;
                                    ?>
                                        <li class="mobile-nav__item">
                                            <div class="mobile-nav__row">
                                                <?php if ($link) : ?>
                                                    <a class="mobile-nav__link" href="<?= esc_url($link['url']); ?>" <?= $link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                                        <?= esc_html($label); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span class="mobile-nav__link"><?= esc_html($label); ?></span>
                                                <?php endif; ?>

                                                <?php if ($is_mega || $has_dropdown) : ?>
                                                    <button class="mobile-nav__toggle mobile-nav__panel-open"
                                                            type="button"
                                                            data-panel="<?= (int)$this_panel; ?>"
                                                            aria-label="<?= esc_attr($label); ?> menu openen">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none">
                                                            <path d="M9 4L17 12L9 20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>


                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                        <?php if ($cta_button) : ?>
                            <div class="mobile-menu__cta">
                                <a class="btn btn--accent"
                                   href="<?= esc_url($cta_button['url']); ?>"
                                   <?= $cta_button['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                    <?= esc_html($cta_button['title']); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <?php if (!empty($nav_items)) :
                    $mob_panel = 0;
                    foreach ($nav_items as $item) :
                        $is_mega      = !empty($item['is_mega_menu']);
                        $has_dropdown = !$is_mega && !empty($item['has_dropdown']) && !empty($item['dropdown_items']);
                        if (!$is_mega && !$has_dropdown) continue;
                        $mob_panel++;

                        $link  = $item['link'] ?? null;
                        $label = ($link['title'] ?? '') ?: ($item['label'] ?? '');
                ?>
                    <?php if ($is_mega) :
                        $mega_bg    = $item['mega_bg'] ?? 'light-blue';
                        $mega_cols  = $item['mega_columns'] ?? [];
                        $cta_intro  = $item['mega_cta_intro'] ?? '';
                        $cta_action = $item['mega_cta_action'] ?? '';
                        $cta_link   = $item['mega_cta_link'] ?? null;
                    ?>
                    <div class="mobile-menu__panel mobile-mega-panel mobile-mega-panel--<?= esc_attr($mega_bg); ?>"
                         data-panel="<?= (int)$mob_panel; ?>">
                        <div class="mobile-menu__content container">

                            <div class="mobile-nav__back">
                                <button class="mobile-nav__back-btn" type="button" data-go-main>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none">
                                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Ga terug
                                </button>
                            </div>

                            <div class="mobile-mega__groups">
                                <?php foreach ($mega_cols as $col) :
                                    $col_links = $col['column_links'] ?? [];
                                    $view_all  = $col['column_view_all'] ?? null;
                                ?>
                                    <div class="mobile-mega__group">
                                        <?php if (!empty($col['column_title'])) : ?>
                                            <p class="mobile-mega__col-title"><?= esc_html($col['column_title']); ?></p>
                                        <?php endif; ?>

                                        <ul class="mobile-mega__links" role="list">
                                            <?php foreach ($col_links as $cl) :
                                                $cl_link = $cl['link'] ?? null;
                                                if (!$cl_link) continue;
                                            ?>
                                                <li>
                                                    <a class="mobile-mega__link"
                                                       href="<?= esc_url($cl_link['url']); ?>"
                                                       <?= $cl_link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                                        <?= esc_html($cl_link['title']); ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>

                                            <?php if ($view_all) : ?>
                                                <li>
                                                    <a class="mobile-mega__view-all"
                                                       href="<?= esc_url($view_all['url']); ?>"
                                                       <?= $view_all['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                                        <?= esc_html($view_all['title']); ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($cta_link) : ?>
                                <a class="mega-menu__cta mobile-mega__cta"
                                   href="<?= esc_url($cta_link['url']); ?>"
                                   <?= $cta_link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                    <?php if ($cta_intro) : ?>
                                        <span class="mega-menu__cta-intro"><?= esc_html($cta_intro); ?></span>
                                    <?php endif; ?>
                                    <?php if ($cta_action) : ?>
                                        <span class="mega-menu__cta-action">
                                            <?= esc_html($cta_action); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php else : ?>
                    <div class="mobile-menu__panel mobile-dropdown-panel"
                         data-panel="<?= (int)$mob_panel; ?>">
                        <div class="mobile-menu__content container">

                            <div class="mobile-nav__back">
                                <button class="mobile-nav__back-btn" type="button" data-go-main>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none">
                                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Ga terug
                                </button>
                            </div>

                            <ul class="mobile-mega__links" role="list">
                                <?php foreach ($item['dropdown_items'] as $dd) :
                                    $dd_link = $dd['link'] ?? null;
                                    if (!$dd_link) continue;
                                ?>
                                    <li>
                                        <a class="mobile-mega__link"
                                           href="<?= esc_url($dd_link['url']); ?>"
                                           <?= $dd_link['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                            <?= esc_html($dd_link['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                        </div>
                    </div>

                    <?php endif; ?>

                <?php endforeach; endif; ?>

            </div>
        </div>
    </div>

</header>
