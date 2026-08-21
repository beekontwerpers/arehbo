<?php get_header(); ?>

<?php

$post_id    = get_the_ID();
$page_title = get_the_title();
$img_url    = get_the_post_thumbnail_url($post_id, 'full');
$post_date  = date_i18n('j F Y', get_the_time('U'));

$uren    = get_field('uren', $post_id);
$locatie = get_field('locatie', $post_id);
$salary  = get_field('salary', $post_id);

$author_raw = get_field('vacature_author', $post_id);
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

$overview_link = arehbo_option_page_link_post('vacatures_overview_page');
$overview_url  = $overview_link ? get_permalink($overview_link) : '';
$overview_label = $overview_link ? get_the_title($overview_link) : '';

$background = 'light';

$arrow_left = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none"><path d="M19 12H5M11 18L5 12L11 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

?>

<main class="single-post" id="main-content">

    <section class="single-hero"
        <?php if ($img_url) : ?>
            style="background-image: url('<?= esc_url($img_url); ?>');"
        <?php endif; ?>>

        <div class="single-hero__inner container">

            <div class="single-hero__nav">

                <?php if ($overview_url) : ?>
                    <a class="single-hero__back" href="<?= esc_url($overview_url); ?>">
                        <?= $arrow_left; ?>
                        Terug naar overzicht
                    </a>
                <?php endif; ?>

                <nav class="single-hero__breadcrumbs" aria-label="Breadcrumb">
                    <a class="single-hero__crumb-link" href="<?= esc_url(home_url('/')); ?>">Home</a>
                    <?php if ($overview_url && $overview_label) : ?>
                        <span class="single-hero__crumb-sep" aria-hidden="true">/</span>
                        <a class="single-hero__crumb-link" href="<?= esc_url($overview_url); ?>"><?= esc_html($overview_label); ?></a>
                    <?php endif; ?>
                    <span class="single-hero__crumb-sep" aria-hidden="true">/</span>
                    <span class="single-hero__crumb-current" aria-current="page"><?= esc_html($page_title); ?></span>
                </nav>

            </div>

            <hr class="single-hero__sep">

            <div class="blogs__content">
                <h1 class="single-hero__title"><?= esc_html($page_title); ?></h1>
            </div>

        </div>
    </section>

    <div class="single-body bg-<?= esc_attr($background); ?>">
        <div class="container">
            <div class="blogs__content">
                <div class="single-content">

                <?php if ($author_name || $uren || $locatie || $salary || $post_date) : ?>
                    <div class="single-author">
                        <?php if ($author_name) : ?>
                            <div class="single-author__left">
                                <?php if ($author_img_url) : ?>
                                    <img
                                        class="single-author__avatar"
                                        src="<?= esc_url($author_img_url); ?>"
                                        alt="<?= esc_attr($author_img_alt); ?>"
                                        width="62"
                                        height="62"
                                        loading="lazy"
                                    >
                                <?php endif; ?>
                                <div class="single-author__info">
                                    <span class="single-author__label">Stel je vragen aan:</span>
                                    <span class="single-author__name"><?= esc_html($author_name); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="single-author__right">
                            <?php if ($uren) : ?>
                                <div class="single-author__meta-item">
                                    <span class="single-author__meta-label">Uren:</span>
                                    <span class="single-author__meta-value"><?= esc_html($uren); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($locatie) : ?>
                                <div class="single-author__meta-item">
                                    <span class="single-author__meta-label">Locatie:</span>
                                    <span class="single-author__meta-value"><?= esc_html($locatie); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($salary) : ?>
                                <div class="single-author__meta-item">
                                    <span class="single-author__meta-label">Salarisindicatie:</span>
                                    <span class="single-author__meta-value"><?= esc_html($salary); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="single-entry">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php the_content(); ?>
                    <?php endwhile; ?>
                </div>

                </div>
            </div>
        </div>
    </div>

</main>

<?php get_footer(); ?>
