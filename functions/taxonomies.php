<?php

function arehbo_register_taxonomies() {
    register_taxonomy('cursus_categorie', ['cursussen'], [
        'labels' => [
            'name'          => 'Cursus categorieën',
            'singular_name' => 'Cursus categorie',
            'search_items'  => 'Categorieën zoeken',
            'all_items'     => 'Alle categorieën',
            'edit_item'     => 'Categorie bewerken',
            'add_new_item'  => 'Nieuwe categorie toevoegen',
            'new_item_name' => 'Naam nieuwe categorie',
        ],
        'hierarchical' => true,
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'query_var'    => false,
        'show_in_nav_menus' => false,
        'meta_box_cb'  => 'arehbo_single_term_taxonomy_meta_box',
        'rewrite'      => false,
    ]);

    register_taxonomy('dienst_categorie', ['diensten'], [
        'labels' => [
            'name'          => 'Dienst categorieën',
            'singular_name' => 'Dienst categorie',
            'search_items'  => 'Categorieën zoeken',
            'all_items'     => 'Alle categorieën',
            'edit_item'     => 'Categorie bewerken',
            'add_new_item'  => 'Nieuwe categorie toevoegen',
            'new_item_name' => 'Naam nieuwe categorie',
        ],
        'hierarchical' => true,
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => true,
        'query_var'    => false,
        'show_in_nav_menus' => false,
        'meta_box_cb'  => 'arehbo_single_term_taxonomy_meta_box',
        'rewrite'      => false,
    ]);
}

add_action('init', 'arehbo_register_taxonomies', 5);

function arehbo_single_term_taxonomy_meta_box($post, $box) {
    $taxonomy = $box['args']['taxonomy'] ?? '';

    if (!$taxonomy) {
        return;
    }

    $current_terms = wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'ids']);
    $current_term  = (!is_wp_error($current_terms) && !empty($current_terms)) ? (int) $current_terms[0] : 0;

    wp_dropdown_categories([
        'taxonomy'         => $taxonomy,
        'hide_empty'       => false,
        'orderby'          => 'name',
        'selected'         => $current_term,
        'show_option_none' => 'Kies een categorie',
        'option_none_value'=> 0,
        'name'             => 'tax_input[' . $taxonomy . '][]',
        'value_field'      => 'term_id',
        'hierarchical'     => true,
    ]);
}

function arehbo_taxonomy_for_post_type($post_type) {
    $map = [
        'cursussen' => 'cursus_categorie',
        'diensten'  => 'dienst_categorie',
    ];
    return $map[$post_type] ?? '';
}

function arehbo_post_type_for_taxonomy($taxonomy) {
    $map = [
        'cursus_categorie' => 'cursussen',
        'dienst_categorie'  => 'diensten',
    ];
    return $map[$taxonomy] ?? '';
}

function arehbo_post_taxonomy_slug($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);

    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    $primary_term = array_shift($terms);

    return $primary_term ? $primary_term->slug : '';
}

function arehbo_post_primary_term($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);

    if (is_wp_error($terms) || empty($terms)) {
        return null;
    }

    $primary_term = array_shift($terms);

    return $primary_term instanceof WP_Term ? $primary_term : null;
}

function arehbo_normalize_post_object($value) {
    if ($value instanceof WP_Post) {
        return $value;
    }

    if (is_numeric($value)) {
        $post = get_post((int) $value);
        return $post instanceof WP_Post ? $post : null;
    }

    if (is_array($value) && !empty($value['ID'])) {
        $post = get_post((int) $value['ID']);
        return $post instanceof WP_Post ? $post : null;
    }

    return null;
}

function arehbo_term_page_link_post($term) {
    if (!$term instanceof WP_Term) {
        return null;
    }

    return arehbo_normalize_post_object(get_field('term_page_link', $term));
}

function arehbo_term_page_link_url($term) {
    $page_link = arehbo_term_page_link_post($term);
    return $page_link ? get_permalink($page_link) : '';
}

function arehbo_term_page_link_title($term) {
    $page_link = arehbo_term_page_link_post($term);
    return $page_link ? get_the_title($page_link) : '';
}

function arehbo_option_page_link_post($field_name) {
    return arehbo_normalize_post_object(get_field($field_name, 'option'));
}

function arehbo_option_page_link_url($field_name) {
    $page_link = arehbo_option_page_link_post($field_name);
    return $page_link ? get_permalink($page_link) : '';
}

function arehbo_option_page_link_title($field_name) {
    $page_link = arehbo_option_page_link_post($field_name);
    return $page_link ? get_the_title($page_link) : '';
}

function arehbo_post_overview_link_data($post_id, $taxonomy, $option_field_name) {
    $primary_term = $taxonomy ? arehbo_post_primary_term($post_id, $taxonomy) : null;

    if ($primary_term) {
        $term_page_link_post = arehbo_term_page_link_post($primary_term);

        if ($term_page_link_post) {
            return [
                'url'   => get_permalink($term_page_link_post),
                'label' => $primary_term->name,
            ];
        }
    }

    $option_page_link_post = $option_field_name ? arehbo_option_page_link_post($option_field_name) : null;

    if ($option_page_link_post) {
        return [
            'url'   => get_permalink($option_page_link_post),
            'label' => get_the_title($option_page_link_post),
        ];
    }

    return [
        'url'   => '',
        'label' => '',
    ];
}

function arehbo_post_term_archive_link($post_id, $taxonomy) {
    $link_data = arehbo_post_overview_link_data($post_id, $taxonomy, '');

    return $link_data['url'] ?: home_url('/');
}

function arehbo_post_type_permalink_with_term($post_link, $post, $leavename, $sample) {
    $taxonomy = arehbo_taxonomy_for_post_type($post->post_type);

    if (!$taxonomy) {
        return $post_link;
    }

    $term_slug = arehbo_post_taxonomy_slug($post->ID, $taxonomy);

    if (!$term_slug) {
        return home_url(user_trailingslashit($post->post_name));
    }

    return home_url(user_trailingslashit($term_slug . '/' . $post->post_name));
}

add_filter('post_type_link', 'arehbo_post_type_permalink_with_term', 10, 4);

function arehbo_register_single_slug_rule() {
    add_rewrite_rule(
        '^([^/]+)/?$',
        'index.php?arehbo_single_slug=$matches[1]',
        'bottom'
    );
}

add_action('init', 'arehbo_register_single_slug_rule', 30);

function arehbo_register_single_slug_query_var($vars) {
    $vars[] = 'arehbo_single_slug';

    return $vars;
}

add_filter('query_vars', 'arehbo_register_single_slug_query_var');

function arehbo_resolve_single_slug_request($query_vars) {
    if (empty($query_vars['arehbo_single_slug'])) {
        return $query_vars;
    }

    $slug = sanitize_title_for_query($query_vars['arehbo_single_slug']);

    if (!$slug) {
        return $query_vars;
    }

    foreach (['diensten', 'cursussen'] as $post_type) {
        $post = get_page_by_path($slug, OBJECT, $post_type);

        if (!$post instanceof WP_Post) {
            continue;
        }

        $taxonomy = arehbo_taxonomy_for_post_type($post_type);
        $terms    = $taxonomy ? get_the_terms($post->ID, $taxonomy) : [];

        if (!empty($terms) && !is_wp_error($terms)) {
            continue;
        }

        $query_vars['post_type'] = $post_type;
        $query_vars['name'] = $slug;
        unset($query_vars['arehbo_single_slug']);
        return $query_vars;
    }

    // Let WordPress render a real 404 instead of falling back to home.php.
    return ['error' => '404'];
}

add_filter('request', 'arehbo_resolve_single_slug_request');

function arehbo_register_term_permalink_rules() {
    $map = [
        'cursus_categorie' => 'cursussen',
        'dienst_categorie'  => 'diensten',
    ];

    foreach ($map as $taxonomy => $post_type) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            continue;
        }

        foreach ($terms as $term) {
            add_rewrite_rule(
                '^' . preg_quote($term->slug, '/') . '/([^/]+)/?$',
                'index.php?post_type=' . $post_type . '&name=$matches[1]',
                'top'
            );
        }
    }
}

add_action('init', 'arehbo_register_term_permalink_rules', 20);

function arehbo_flush_term_permalink_rules() {
    flush_rewrite_rules(false);
}

add_action('created_cursus_categorie', 'arehbo_flush_term_permalink_rules', 10, 3);
add_action('edited_cursus_categorie', 'arehbo_flush_term_permalink_rules', 10, 3);
add_action('delete_cursus_categorie', 'arehbo_flush_term_permalink_rules', 10, 3);
add_action('created_dienst_categorie', 'arehbo_flush_term_permalink_rules', 10, 3);
add_action('edited_dienst_categorie', 'arehbo_flush_term_permalink_rules', 10, 3);
add_action('delete_dienst_categorie', 'arehbo_flush_term_permalink_rules', 10, 3);

function arehbo_maybe_flush_cursus_permalink_rules_on_save($post_id, $post, $update) {
    if (!$post instanceof WP_Post || $post->post_type !== 'cursussen') {
        return;
    }

    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $taxonomy      = 'cursus_categorie';
    $current_slug   = arehbo_post_taxonomy_slug($post_id, $taxonomy);
    $stored_slug    = (string) get_post_meta($post_id, '_arehbo_cursus_permalink_term_slug', true);
    $has_stored_slug = $stored_slug !== '';

    if ($current_slug === $stored_slug) {
        return;
    }

    update_post_meta($post_id, '_arehbo_cursus_permalink_term_slug', $current_slug);

    if ($has_stored_slug) {
        flush_rewrite_rules(false);
    }
}

add_action('save_post_cursussen', 'arehbo_maybe_flush_cursus_permalink_rules_on_save', 20, 3);
