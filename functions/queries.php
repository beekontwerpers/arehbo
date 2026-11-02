<?php

add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && ($query->is_archive() || $query->is_home())) {
        $query->set('posts_per_page', 6);
    }
});

function arehbo_course_is_visible($post_id) {
    return (bool) get_field('visible_visual_systems', $post_id);
}

function arehbo_visible_coursussen_query_args(array $query_args) {
    $query_args['meta_query'] = array_merge(
        $query_args['meta_query'] ?? [],
        [
            [
                'key'     => 'visible_visual_systems',
                'value'   => '1',
                'compare' => '=',
            ],
            // Losse dagdeel-cursussen uitsluiten: het veld staat uit of is er (nog) niet.
            [
                'relation' => 'OR',
                [
                    'key'     => 'dagdeel_cursus',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => 'dagdeel_cursus',
                    'value'   => '1',
                    'compare' => '!=',
                ],
            ],
        ]
    );

    return $query_args;
}

add_action('template_redirect', function () {
    if (!is_singular('cursussen')) {
        return;
    }

    $post_id = get_queried_object_id();

    if (!$post_id || arehbo_course_is_visible($post_id)) {
        return;
    }

    if (is_preview() && current_user_can('edit_post', $post_id)) {
        return;
    }

    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    include get_404_template();
    exit;
});
