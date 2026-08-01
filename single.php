<?php get_header(); ?>

<?php

$post_id     = get_the_ID();
$blog_title  = get_the_title();
$img_url     = get_the_post_thumbnail_url($post_id, 'full');
$post_date   = date_i18n('j F Y', get_the_time('U'));
$read_time   = arehbo_reading_time($post_id);

$crumb_title = mb_strlen($blog_title) > 40
    ? mb_substr($blog_title, 0, 40) . '...'
    : $blog_title;

$background = 'light';

$author_raw  = get_field('blog_author', $post_id);
if ($author_raw instanceof WP_Post) {
    $author_post = $author_raw;
} elseif (is_numeric($author_raw) && $author_raw > 0) {
    $author_post = get_post((int) $author_raw);
} else {
    $raw_id      = get_post_meta($post_id, 'blog_author', true);
    $author_post = $raw_id ? get_post((int) $raw_id) : null;
}

$author_name    = $author_post ? get_the_title($author_post) : '';
$author_img_id  = ($author_post && $author_post->ID)
    ? (get_field('foto', $author_post->ID) ?: get_post_thumbnail_id($author_post->ID))
    : 0;
$author_img_url = $author_img_id
    ? wp_get_attachment_image_url($author_img_id, [62, 62])
    : '';
$author_img_alt = $author_img_id
    ? (get_post_meta($author_img_id, '_wp_attachment_image_alt', true) ?: $author_name)
    : $author_name;

$arrow_left = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false" fill="none"><path d="M19 12H5M11 18L5 12L11 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$arrow_white = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$blogs_url = ($page_for_posts = get_option('page_for_posts'))
    ? get_permalink($page_for_posts)
    : home_url('/');
?>

<main class="single-post" id="main-content">

    <section class="single-hero"
        <?php if ($img_url) : ?>
            style="background-image: url('<?= esc_url($img_url); ?>');"
        <?php endif; ?>>

        <div class="single-hero__inner container">

            <div class="single-hero__nav">

                <a class="single-hero__back" href="<?= esc_url($blogs_url); ?>">
                    <?= $arrow_left; ?>
                    Terug naar alle blogs
                </a>

                <nav class="single-hero__breadcrumbs" aria-label="Breadcrumb">
                    <a class="single-hero__crumb-link" href="<?= esc_url(home_url('/')); ?>">Home</a>
                    <span class="single-hero__crumb-sep" aria-hidden="true">/</span>
                    <a class="single-hero__crumb-link" href="<?= esc_url($blogs_url); ?>">Blogs</a>
                    <span class="single-hero__crumb-sep" aria-hidden="true">/</span>
                    <span class="single-hero__crumb-current" aria-current="page"><?= esc_html($crumb_title); ?></span>
                </nav>

            </div>

            <hr class="single-hero__sep">

            <div class="blogs__content">
                <h1 class="single-hero__title"><?= esc_html($blog_title); ?></h1>
            </div>

        </div>
    </section>

    <div class="single-body bg-<?= esc_attr($background); ?>">
        <div class="container">
            <div class="blogs__content">
                <div class="single-content">

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
                                    <span class="single-author__label">Geschreven door:</span>
                                    <span class="single-author__name"><?= esc_html($author_name); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="single-author__right">
                            <div class="single-author__meta-item">
                                <span class="single-author__meta-label">Geplaatst op:</span>
                                <span class="single-author__meta-value"><?= esc_html($post_date); ?></span>
                            </div>
                            <div class="single-author__meta-item">
                                <span class="single-author__meta-label">Leestijd:</span>
                                <span class="single-author__meta-value"><?= esc_html($read_time); ?></span>
                            </div>
                        </div>

                    </div>

                    <div class="single-entry">
                        <?php the_content(); ?>
                    </div>

                </div>
            </div>

            <?php
            $related_q = new WP_Query([
                'post_type'           => 'post',
                'posts_per_page'      => 2,
                'post__not_in'        => [$post_id],
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ]);
            ?>

            <?php if ($related_q->have_posts()) : ?>
                <section class="blogs blogs--related">
                    <div class="blogs__content">
                        <h2 class="blogs__title">Gerelateerde blogs</h2>

                        <div class="blogs__grid">
                            <?php while ($related_q->have_posts()) : $related_q->the_post();
                                $rel_id      = get_the_ID();
                                $rel_url     = get_permalink();
                                $rel_title   = get_the_title();
                                $rel_excerpt = wp_trim_words(
                                    get_the_excerpt() ?: strip_tags(get_the_content()),
                                    30,
                                    '...'
                                );

                                $image_id  = get_post_thumbnail_id($rel_id);
                                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
                                $image_alt = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: esc_attr($rel_title)) : '';

                                $word_count   = str_word_count(strip_tags(get_post_field('post_content', $rel_id)));
                                $reading_mins = max(1, (int) ceil($word_count / 200));
                            ?>
                                <article class="blog-card">
                                    <a
                                        class="blog-card__link"
                                        href="<?= esc_url($rel_url); ?>"
                                        aria-label="<?= esc_attr($rel_title); ?>"
                                    ></a>

                                    <?php if ($image_url) : ?>
                                        <div class="blog-card__image-wrap">
                                            <img
                                                class="blog-card__image"
                                                src="<?= esc_url($image_url); ?>"
                                                alt="<?= esc_attr($image_alt); ?>"
                                                width="565"
                                                height="400"
                                                loading="lazy"
                                            >
                                            <div class="blog-card__reading-time" aria-label="Leestijd">
                                                <span>Leestijd: <?= $reading_mins; ?> minuten</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="blog-card__body">
                                        <h3 class="blog-card__title"><?= esc_html($rel_title); ?></h3>

                                        <?php if ($rel_excerpt) : ?>
                                            <p class="blog-card__excerpt"><?= esc_html($rel_excerpt); ?></p>
                                        <?php endif; ?>

                                        <div class="blog-card__btn-wrap">
                                            <span class="blog-card__btn-label" aria-hidden="true">LEES VERDER</span>
                                            <span class="blog-card__btn" aria-hidden="true"><?= $arrow_white; ?></span>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>

                        <div class="blogs__footer">
                            <?php get_template_part('components/button', '', [
                                'label'   => 'Bekijk alle blogs',
                                'url'     => esc_url($blogs_url),
                                'target'  => '_self',
                                'variant' => 'accent',
                                'icon'    => true,
                            ]); ?>
                        </div>
                    </div>
                </section>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

            <?php
            // CTA (options-only; no per-post overrides on blog detail).
            $cta_image_id    = (int) (get_field('blocks_cta_image', 'option') ?: get_field('blocks_cta_image', 'options'));
            $cta_image_url   = $cta_image_id ? wp_get_attachment_image_url($cta_image_id, 'full') : '';
            $cta_image_alt   = $cta_image_id ? (get_post_meta($cta_image_id, '_wp_attachment_image_alt', true) ?: '') : '';
            $cta_image_label = get_field('blocks_cta_image_label', 'option') ?: get_field('blocks_cta_image_label', 'options');
            $cta_title       = get_field('blocks_cta_title', 'option') ?: get_field('blocks_cta_title', 'options');
            $cta_description = get_field('blocks_cta_description', 'option') ?: get_field('blocks_cta_description', 'options');
            $cta_buttons_raw = (array) (get_field('blocks_cta_buttons', 'option') ?: get_field('blocks_cta_buttons', 'options'));
            $cta_buttons     = array_values(array_filter($cta_buttons_raw, function ($row) {
                $link = is_array($row) ? ($row['link'] ?? null) : null;
                return is_array($link) && !empty($link['url']) && !empty($link['title']);
            }));
            $cta_buttons = array_slice($cta_buttons, 0, 2);
            ?>

            <?php if (!empty($cta_title)) : ?>
                <section class="cta-section cta-section--light single-cta">
                    <div class="cta">
                        <div class="cta__grid">

                            <?php if ($cta_image_url) : ?>
                                <div class="cta__media">
                                    <img
                                        class="cta__image"
                                        src="<?= esc_url($cta_image_url); ?>"
                                        alt="<?= esc_attr($cta_image_alt); ?>"
                                        width="440"
                                        height="362"
                                        loading="lazy"
                                    >
                                    <?php if ($cta_image_label) : ?>
                                        <span class="cta__badge"><?= esc_html($cta_image_label); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="cta__content">

                                <div class="cta__text">
                                    <h2 class="cta__title"><?= esc_html($cta_title); ?></h2>
                                    <?php if ($cta_description) : ?>
                                        <p class="cta__description"><?= esc_html($cta_description); ?></p>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($cta_buttons)) : ?>
                                    <div class="cta__buttons">
                                        <?php foreach ($cta_buttons as $i => $btn) :
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
                </section>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php get_footer(); ?>
