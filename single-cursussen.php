<?php

get_header();

while (have_posts()) : the_post();

    $page_title  = get_the_title();
    $show_reviews = (bool) get_field('cursus_show_reviews');
    $hero_title  = get_field('cursus_title') ?: $page_title;
    $description = get_field('cursus_description');
    $img_id      = get_field('cursus_image');
    $overview_link = arehbo_post_overview_link_data(get_the_ID(), 'cursus_categorie', 'cursussen_overview_page');
    $overview_url  = $overview_link['url'] ?? '';
    $overview_label = $overview_link['label'] ?? '';

    $kosten      = get_field('kosten');
    $locatie     = get_field('locatie');
    $duur        = get_field('duur');
    $buttons     = get_field('cursus_buttons') ?: [];

    if (!$img_id) {
        $img_id = get_post_thumbnail_id();
    }

    $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'full') : '';
    $img_alt = $img_id ? (get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: $page_title) : '';

    $arrow_left    = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none"><path d="M19 12H5M11 18L5 12L11 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

?>

<main id="main-content" class="cursus-page">

    <div class="cursus-hero-bg cursus-hero-bg--light-blue">
        <div class="cursus-page__inner container">

            <div class="cursus-nav">

                <?php if ($overview_url) : ?>
                    <a class="cursus-back" href="<?= esc_url($overview_url); ?>">
                        <?= $arrow_left; ?>
                        Terug naar overzicht
                    </a>
                <?php endif; ?>

                <nav class="cursus-breadcrumbs" aria-label="Breadcrumb">
                    <a class="cursus-breadcrumbs__link" href="<?= esc_url(home_url('/')); ?>">Home</a>
                    <?php if ($overview_url && $overview_label) : ?>
                        <span class="cursus-breadcrumbs__sep" aria-hidden="true">/</span>
                        <a class="cursus-breadcrumbs__link" href="<?= esc_url($overview_url); ?>"><?= esc_html($overview_label); ?></a>
                    <?php endif; ?>
                    <span class="cursus-breadcrumbs__sep" aria-hidden="true">/</span>
                    <span class="cursus-breadcrumbs__current" aria-current="page"><?= esc_html($page_title); ?></span>
                </nav>

            </div>

            <hr class="cursus-divider">

            <div class="cursus-hero">

                <div class="cursus-hero__text">

                    <?php if ($show_reviews) : ?>
                        <div class="cursus-hero__reviews">
                            <?php get_template_part('components/google-reviews', '', ['variant' => 'light']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($hero_title) : ?>
                        <h1 class="cursus-hero__title"><?= esc_html($hero_title); ?></h1>
                    <?php endif; ?>

                    <?php if ($description) : ?>
                        <div class="cursus-hero__description"><?= wp_kses_post($description); ?></div>
                    <?php endif; ?>

                </div>

                <?php if ($img_url) : ?>
                    <div class="cursus-hero__image-wrap">
                        <img
                            class="cursus-hero__image"
                            src="<?= esc_url($img_url); ?>"
                            alt="<?= esc_attr($img_alt); ?>"
                            width="635"
                            height="357"
                            loading="eager"
                        >
                    </div>
                <?php endif; ?>

            </div><!-- .cursus-hero -->

    </div><!-- .cursus-page__inner -->
    </div><!-- .cursus-hero-bg -->

    <?php if ($kosten || $locatie || $duur || !empty($buttons)) : ?>
        <div class="cursus-cards cursus-cards--light-blue">
            <div class="container">

                <article class="cursus-card">

                    <div class="cursus-card__left">
                        <div class="cursus-card__meta">
                            <?php if ($kosten) : ?>
                                <div class="cursus-card__meta-item">
                                    <span class="cursus-card__meta-label">Kosten (excl. BTW):</span>
                                    <span class="cursus-card__meta-value"><?= esc_html($kosten); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($locatie) : ?>
                                <div class="cursus-card__meta-item">
                                    <span class="cursus-card__meta-label">Locatie:</span>
                                    <span class="cursus-card__meta-value"><?= esc_html($locatie); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($duur) : ?>
                                <div class="cursus-card__meta-item">
                                    <span class="cursus-card__meta-label">Duur:</span>
                                    <span class="cursus-card__meta-value"><?= esc_html($duur); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="cursus-card__right">
                        <?php if (!empty($buttons)) : ?>
                            <?php foreach (array_slice($buttons, 0, 3) as $i => $btn) :
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
                        <?php endif; ?>
                    </div>

                </article>

            </div>
        </div>
    <?php endif; ?>

    <?php get_template_part('components/colorbar'); ?>

    <?php if (has_blocks(get_the_content())) : ?>
        <div id="page-content" class="page-content">
            <?php the_content(); ?>
        </div>
    <?php endif; ?>

</main>

<?php
endwhile;

get_footer();
