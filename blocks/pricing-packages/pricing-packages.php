<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space    = get_spacing_class(get_field('space'));
$full_id  = get_full_id(get_field('id'));
$background = get_field('background') ?: 'light';
$packages = get_field('packages');

if (empty($packages)) {
    return;
}

$packages = array_slice($packages, 0, 3);
$total    = count($packages);

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

?>

<section <?= $full_id; ?> class="pricing-packages <?= esc_attr($bg_class); ?>">
    <div class="<?= esc_attr($space); ?>">
        <div class="container">
        <div class="pricing-packages__grid">

            <?php foreach ($packages as $index => $package) :

                $is_featured  = ($total === 3 && $index === 1);

                $title        = $package['title']        ?? '';
                $description  = $package['description']  ?? '';
                $price        = $package['price']        ?? '';
                $features       = $package['features']       ?? [];
                $button         = $package['button']         ?? null;
                $accent_button  = $package['accent_button']  ?? false;

            ?>
                <div class="package-card <?php echo $is_featured ? 'package-card--featured' : ''; ?>">

                    <?php if ($title) : ?>
                        <h3 class="package-card__title"><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>

                    <?php if ($description) : ?>
                        <p class="package-card__description"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>

                    <?php if ($price !== '' && $price !== null) : ?>
                        <?php
                        $price_number = is_numeric($price) ? (float) $price : null;
                        $price_label  = $price_number === null ? (string) $price : number_format_i18n($price_number, 2);
                        ?>
                        <p class="package-card__price"><?php echo esc_html('€ ' . $price_label); ?></p>
                    <?php endif; ?>

                    <?php if ($price !== '' && $price !== null) : ?>
                        <p class="package-card__btw"><?php esc_html_e('Excl. 21% BTW', 'arehbo-theme'); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($features)) : ?>
                        <ul class="package-card__features">
                            <?php foreach ($features as $feature) :
                                $text = $feature['text'] ?? '';
                                if (empty($text)) continue;
                            ?>
                                <li class="package-feature">
                                    <span class="package-feature__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">
                                            <path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </span>
                                    <span class="package-feature__text"><?php echo esc_html($text); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($button && !empty($button['url'])) : ?>
                        <div class="package-card__cta">
                            <?php get_template_part('components/button', '', [
                                'label'   => $button['title'],
                                'url'     => $button['url'],
                                'target'  => $button['target'] ?? '_self',
                                'variant' => $accent_button ? 'accent' : 'outline',
                                'icon'    => false,
                            ]); ?>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>
        </div>
    </div>
</section>
