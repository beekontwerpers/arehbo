<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space   = get_spacing_class(get_field('space'));
$full_id = get_full_id(get_field('id'));
$background = get_field('background') ?: 'light';
$heading = get_field('heading');
$faqs    = get_field('faqs');

if (empty($faqs)) {
    return;
}

$block_uid = !empty($block['id']) ? $block['id'] : uniqid('faq-');

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

?>

<section <?= $full_id; ?> class="faq-block <?= esc_attr($bg_class); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">
            <div class="faq-block__inner">

        <?php if ($heading) : ?>
            <h2 class="faq-block__heading"><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>

        <div class="faq-list">
            <?php foreach ($faqs as $index => $faq) :
                $title       = $faq['title']       ?? '';
                $description = $faq['description'] ?? '';
                $number      = $index + 1;
                $panel_id    = esc_attr($block_uid . '-panel-' . $index);

                if (empty($title)) continue;
            ?>
                <div class="faq-item">

                    <button
                        class="faq-item__toggle"
                        aria-expanded="false"
                        aria-controls="<?= $panel_id; ?>"
                    >
                        <span class="faq-item__number"><?= $number; ?>.</span>
                        <span class="faq-item__title"><?php echo esc_html($title); ?></span>
                        <span class="faq-item__icon" aria-hidden="true"></span>
                    </button>

                    <div
                        id="<?= $panel_id; ?>"
                        class="faq-item__body"
                    >
                        <?php if ($description) : ?>
                            <div class="faq-item__description">
                                <div class="faq-item__answer">
                                    <?php echo wp_kses_post($description); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

            </div>
        </div>
    </div>
</section>
