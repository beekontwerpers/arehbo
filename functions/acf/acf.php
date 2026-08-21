<?php

function arehbo_register_blocks() {
    foreach (glob(__DIR__ . '/../../blocks/*', GLOB_ONLYDIR) as $block_dir) {
        register_block_type($block_dir, [
            'api_version'         => 3,
            'acf_block_version'   => 3,
            'auto_inline_editing' => false,
        ]);
    }
}

add_action('init', 'arehbo_register_blocks');

function arehbo_block_categories(array $categories): array {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'custom_blocks',
                'title' => __('Aangepaste blokken', 'arehbo-theme'),
            ],
        ]
    );
}

add_filter(
    'block_categories_all',
    function ($categories, $editor_context) {
        return arehbo_block_categories($categories);
    },
    10,
    2
);

function arehbo_get_custom_block_names(): array {
    static $names = null;
    if (is_array($names)) {
        return $names;
    }

    $names = [];
    foreach (glob(__DIR__ . '/../../blocks/*/block.json') as $block_json) {
        $raw = file_get_contents($block_json);
        if (!$raw) continue;

        $meta = json_decode($raw, true);
        if (!is_array($meta)) continue;

        $name = $meta['name'] ?? '';
        if (is_string($name) && $name !== '') {
            $names[] = $name;
        }
    }

    $names = array_values(array_unique($names));
    sort($names);
    return $names;
}

add_filter('allowed_block_types_all', function ($allowed_block_types, $editor_context) {
    if (!isset($editor_context->post) || !is_object($editor_context->post)) {
        return $allowed_block_types;
    }

    $post_type = $editor_context->post->post_type ?? '';
    if (!$post_type) {
        return $allowed_block_types;
    }

    // Posts + vacatures: allow core typing blocks + our custom button block.
    if (in_array($post_type, ['post', 'vacatures'], true)) {
        return [
            'core/paragraph',
            'core/heading',
            'core/list',
            'core/list-item',
            'core/image',
            'core/video',
            'core/quote',
            'core/embed',
            'core/html',
            'core/table',
            'acf/button',
            'rank-math/toc-block',
            'rank-math/toc',
            'gravityforms/form',
        ];
    }

    // Other post types: only custom blocks, no core blocks, and exclude the button block.
    // course-calendar is only allowed on cursussen post type.
    $excluded = ['acf/button'];
    if ($post_type !== 'cursussen') {
        $excluded[] = 'acf/course-calendar';
    }

    $custom = array_values(array_diff(arehbo_get_custom_block_names(), $excluded));
    return $custom;
}, 10, 2);
