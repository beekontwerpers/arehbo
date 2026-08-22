<?php

if (arehbo_should_blank_acf_block_backend()) {
    return;
}

$space      = get_spacing_class(get_field('space'));
$full_id    = get_full_id(get_field('id'));
$background = $args['background'] ?? get_field('background') ?: 'light';
$items      = get_field('items');

$bg_map   = ['dark' => 'bg-dark', 'light' => 'bg-light'];
$bg_class = $bg_map[$background] ?? 'bg-light';

if (empty($items)) {
    return;
}

?>

<section <?= $full_id; ?> class="usp-list <?= esc_attr($bg_class); ?>">
     <div class="container ">  <div class="usp-list__wrapper <?= esc_attr($space); ?>">
        <div class="container">
            <div class="usp-list__track">
                <?php get_template_part('components/usp-items', '', [
                    'items'     => $items,
                    'show_icon' => true,
                ]); ?>

                <div class="usp-list__clone" aria-hidden="true">
                    <?php get_template_part('components/usp-items', '', [
                        'items'     => $items,
                        'show_icon' => true,
                        'label'     => '',
                    ]); ?>
                </div>
            </div>

        </div>
    </div>
   </div>
</section>
