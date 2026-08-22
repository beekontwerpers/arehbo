<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space      = get_spacing_class(get_field('space'));
$full_id    = get_full_id(get_field('id'));
$background = get_field('tc_background') ?: 'dark';
$tile_type  = get_field('tc_tile_type') ?: 'standard';
$tile_title_tag = get_field('tc_tile_title_tag') ?: 'h3';

$allowed_tile_title_tags = ['h2', 'h3', 'h4'];
if (!in_array($tile_title_tag, $allowed_tile_title_tags, true)) {
    $tile_title_tag = 'h3';
}

$description = get_field('tc_description');
$button_raw  = get_field('tc_button') ?: [];
$cards       = get_field('tc_cards') ?: [];

$btn_url    = $button_raw['url']    ?? '';
$btn_label  = $button_raw['title']  ?? '';
$btn_target = $button_raw['target'] ?? '_self';

?>

<section <?= $full_id; ?> class="text-cards-block text-cards-block--<?= esc_attr($background); ?> text-cards-block--<?= esc_attr($tile_type); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="text-cards-block__layout container">

            <div class="text-cards-block__left">

                <?php if ($description) : ?>
                    <div class="text-cards-block__description">
                        <?= wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if ($btn_url && $btn_label) : ?>
                    <div class="text-cards-block__btn-wrap">
                        <?php get_template_part('components/button', '', [
                            'label'  => $btn_label,
                            'url'    => $btn_url,
                            'target' => $btn_target,
                            'variant' => 'accent',
                            'icon'   => true,
                        ]); ?>
                    </div>
                <?php endif; ?>

            </div>

            <?php if ($cards) : ?>
                <div class="text-cards-block__cards">
                    <?php foreach ($cards as $card) :
                        $card_title = $card['card_title'] ?? '';
                        $card_desc  = $card['card_description'] ?? '';
                    ?>
                        <div class="text-cards-block__card">

                            <div class="text-cards-block__card-header">
                                <?php if ($tile_type !== 'checklist') : ?>
                                    <span class="text-cards-block__card-icon" aria-hidden="true"></span>
                                <?php endif; ?>
                                <?php if ($card_title) : ?>
                                    <<?= $tile_title_tag; ?> class="text-cards-block__card-title"><?= esc_html($card_title); ?></<?= $tile_title_tag; ?>>
                                <?php endif; ?>
                            </div>

                            <?php if ($card_desc) : ?>
                                <div class="text-cards-block__card-description">
                                    <?= wp_kses_post($card_desc); ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
