<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space      = get_spacing_class(get_field('space'));
$full_id    = get_full_id(get_field('id'));
$background = get_field('background') ?: 'light';
$title      = get_field('title');
$display_mode = get_field('display_mode') ?: 'manual';
$posts        = [];
$cta_button = get_field('cta_button');

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

if ($display_mode === 'latest2') {
    $posts = get_posts([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 2,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
} else {
    $posts = get_field('posts');
}

if (empty($posts) || !is_array($posts)) {
    return;
}

$arrow_white = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4 10H16M16 10L11 5M16 10L11 15" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

?>

<section <?= $full_id; ?> class="blogs<?= $bg_class ? ' ' . esc_attr($bg_class) : ''; ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">
            <div class="blogs__content">

                <?php if ($title) : ?>
                    <h2 class="blogs__title"><?= esc_html($title); ?></h2>
                <?php endif; ?>

                <div class="blogs__grid">

                <?php foreach ($posts as $post) :
                    $post_id  = $post->ID;
                    $post_url = get_permalink($post);
                    $post_title  = get_the_title($post);
                    $post_excerpt = wp_trim_words(
                        get_the_excerpt($post_id) ?: strip_tags(get_the_content(null, false, $post)),
                        30,
                        '...'
                    );

                    $image_id  = get_post_thumbnail_id($post_id);
                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
                    $image_alt = $image_id ? (get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: esc_attr($post_title)) : '';

                    $word_count   = str_word_count(strip_tags(get_post_field('post_content', $post_id)));
                    $reading_mins = max(1, (int) ceil($word_count / 200));
                ?>

                    <article class="blog-card">

                        <a
                            class="blog-card__link"
                            href="<?= esc_url($post_url); ?>"
                            aria-label="<?= esc_attr($post_title); ?>"
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
                                <div class="blog-card__reading-time" aria-label="<?php esc_attr_e('Reading time', 'arehbo-theme'); ?>">
                                    <span>Leestijd: <?= $reading_mins; ?> minuten</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="blog-card__body">

                            <h3 class="blog-card__title"><?= esc_html($post_title); ?></h3>

                            <?php if ($post_excerpt) : ?>
                                <p class="blog-card__excerpt"><?= esc_html($post_excerpt); ?></p>
                            <?php endif; ?>

                            <div class="blog-card__btn-wrap">
                                <span class="blog-card__btn-label" aria-hidden="true">LEES VERDER</span>
                                <span class="blog-card__btn" aria-hidden="true"><?= $arrow_white; ?></span>
                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <?php if ($cta_button && !empty($cta_button['url'])) : ?>
                <div class="blogs__footer">
                    <?php get_template_part('components/button', null, [
                        'label'   => $cta_button['title'] ?: 'BEKIJK ALLE BLOGS',
                        'url'     => $cta_button['url'],
                        'target'  => $cta_button['target'] ?: '_self',
                        'variant' => 'accent',
                    ]); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
