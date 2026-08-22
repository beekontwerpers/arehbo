<?php

function arehbo_should_blank_acf_block_backend() {
    if (!is_admin()) {
        return false;
    }

    if (function_exists('acf_get_data') && acf_get_data('acf_doing_block_preview')) {
        return true;
    }

    if (function_exists('acf_is_block_editor') && acf_is_block_editor()) {
        return true;
    }

    return false;
}

function arehbo_enqueue_block_editor_assets() {
    wp_register_style(
        'arehbo-editor-admin',
        false,
        [],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style('arehbo-editor-admin');
    wp_add_inline_style(
        'arehbo-editor-admin',
        'body.block-editor-page .interface-interface-skeleton__sidebar,body.block-editor-page .interface-interface-skeleton__sidebar .interface-complementary-area,body.block-editor-page .edit-post-layout__sidebar,body.block-editor-page .edit-post-sidebar{width:550px;min-width:550px;flex-basis:550px;}'
    );
}

add_action('enqueue_block_editor_assets', 'arehbo_enqueue_block_editor_assets');
