<?php

$product_name   = $args['product_name']   ?? '';
$background     = $args['background']     ?? 'light';
$tiers          = $args['tiers']          ?? [];
$buttons        = $args['buttons']        ?? [];

$is_dark = $background === 'dark';

$lowest_price_fmt = '';
if (!empty($tiers)) {
    usort($tiers, static function ($a, $b) {
        $step_a = isset($a['stap']) ? (int) $a['stap'] : (isset($a['tier_min']) ? (int) $a['tier_min'] : 0);
        $step_b = isset($b['stap']) ? (int) $b['stap'] : (isset($b['tier_min']) ? (int) $b['tier_min'] : 0);

        return $step_a <=> $step_b;
    });

    $last = end($tiers);
    $val  = floatval(str_replace(',', '.', $last['prijs_per_stuk'] ?? ($last['tier_price'] ?? '0')));
    $lowest_price_fmt = ($val == floor($val))
        ? '€' . number_format($val, 0, ',', '.') . ',-'
        : '€' . number_format($val, 2, ',', '.');
}

$js_tiers = [];
foreach ($tiers as $t) {
    $step = isset($t['stap']) ? intval($t['stap']) : intval($t['tier_min'] ?? 0);
    $price = $t['prijs_per_stuk'] ?? ($t['tier_price'] ?? '0');

    if ($step < 1) {
        continue;
    }

    $js_tiers[] = [
        'step'  => $step,
        'price' => floatval(str_replace(',', '.', $price)),
    ];
}

?>

<section class="rekentool<?= $is_dark ? ' rekentool--dark' : ' rekentool--light'; ?>">
    <div class="container">
        <div class="rekentool__wrapper">
            <div class="container">

                <div class="rekentool__bar" data-tiers="<?= esc_attr(wp_json_encode($js_tiers)); ?>">

                    <div class="rekentool__left">
                        <span class="rekentool__title">Bereken uw prijs:</span>
                        <?php if ($lowest_price_fmt) : ?>
                            <span class="rekentool__vanaf">vanaf <?= esc_html($lowest_price_fmt); ?> per stuk</span>
                        <?php endif; ?>
                    </div>

                    <div class="rekentool__qty-wrap">
                        <button class="rekentool__btn rekentool__btn--decrease" type="button" aria-label="Verminder aantal">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" aria-hidden="true">
                                <path d="M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="rekentool__qty-wrapper">
                            <span class="rekentool__count" aria-live="polite">1</span>
                            <?php if ($product_name) : ?>
                                <span class="rekentool__product"><?= esc_html($product_name); ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="rekentool__btn rekentool__btn--increase" type="button" aria-label="Verhoog aantal">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>

                    <div class="rekentool__right">
                        <span class="rekentool__current-price" aria-live="polite">-</span>
                        <span class="rekentool__per-stuk">per stuk</span>
                    </div>

                    <?php if (!empty($buttons)) : ?>
                        <div class="rekentool__btn-wrap">
                            <?php foreach (array_slice($buttons, 0, 3) as $i => $btn) :
                                $link = $btn['link'] ?? [];
                                $label = $link['title'] ?? '';
                                $url = $link['url'] ?? '';
                                $target = $link['target'] ?? '_self';
                                $variant = $btn['variant'] ?? ($i === 0 ? 'accent' : 'outline');

                                if (empty($label) || empty($url)) {
                                    continue;
                                }
                                get_template_part('components/button', '', [
                                    'label'   => $label,
                                    'url'     => $url,
                                    'target'  => $target,
                                    'variant' => $variant,
                                    'icon'    => true,
                                    'data_attrs' => [
                                        'qty-url-template' => $url,
                                    ],
                                ]);
                            endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </div>
</section>
