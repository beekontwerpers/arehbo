<?php

$label   = $args['label']   ?? '';
$url     = $args['url']     ?? '';
$target  = $args['target']  ?? '_self';
$variant = $args['variant'] ?? 'accent';
$icon    = $args['icon']    ?? true;
$data_attrs = $args['data_attrs'] ?? [];

if (empty($label) || empty($url)) {
    return;
}

$is_external = $target === '_blank';
$classes     = 'btn btn--' . esc_attr($variant) . ($icon ? ' btn--icon' : '');

$a11y_label = null;
if (in_array($variant, ['accent', 'outline'], true)) {
    $a11y_label = 'Navigeren naar: ' . trim(wp_strip_all_tags($label));
}

$data_attr_html = '';
if (is_array($data_attrs)) {
    foreach ($data_attrs as $name => $value) {
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string) $name);
        if ($name === '' || $value === null || $value === '') {
            continue;
        }

        $data_attr_html .= sprintf(
            ' data-%s="%s"',
            esc_attr($name),
            esc_attr((string) $value)
        );
    }
}
?>

<a
    href="<?php echo esc_url($url); ?>"
    class="<?php echo $classes; ?>"
    <?php echo $data_attr_html; ?>
    <?php if ($a11y_label) : ?>aria-label="<?php echo esc_attr($a11y_label); ?>"<?php endif; ?>
    <?php if ($is_external) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
>
    <?php echo esc_html($label); ?>
    <?php if ($icon) : ?>
        <span class="btn__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    <?php endif; ?>
</a>
