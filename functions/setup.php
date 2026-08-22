<?php

add_filter('show_admin_bar', '__return_false');

function arehbo_get_editor_style_paths(): array {
    $base_dir = trailingslashit(get_template_directory()) . 'styles';
    if (!is_dir($base_dir)) {
        return [];
    }

    $paths = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base_dir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        if (strtolower($file->getExtension()) !== 'css') {
            continue;
        }

        $paths[] = ltrim(str_replace(trailingslashit(get_template_directory()), '', $file->getPathname()), '/');
    }

    sort($paths);

    return $paths;
}

function arehbo_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');

    foreach (arehbo_get_editor_style_paths() as $editor_style) {
        add_editor_style($editor_style);
    }
}

add_action('after_setup_theme', 'arehbo_theme_setup');
