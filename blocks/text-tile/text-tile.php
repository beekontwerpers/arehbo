<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space       = get_spacing_class(get_field('space'));
$full_id     = get_full_id(get_field('id'));
$background  = get_field('background') ?: 'light';
$description = get_field('description');
$buttons     = get_field('buttons') ?: [];

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

if (empty($description) && empty($buttons)) {
    return;
}

?>

<section <?= $full_id; ?> class="text-tile <?= esc_attr($bg_class); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">
            <div class="text-tile__content">

                <?php if ($description) : ?>
                    <div class="text-tile__description">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($buttons)) : ?>
                    <div class="text-tile__buttons">
                        <?php foreach ($buttons as $i => $btn) :
                            $link   = $btn['link'] ?? null;
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
        </div>
    </div>
</section>
