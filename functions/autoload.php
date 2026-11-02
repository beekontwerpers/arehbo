<?php

// Core theme setup + helpers
require_once __DIR__ . '/setup.php';
require_once __DIR__ . '/custom.php';

// Theme assets + admin/editor tweaks
require_once __DIR__ . '/enqueue.php';
require_once __DIR__ . '/admin.php';

// ACF (options + field loading + editor/block rules)
require_once __DIR__ . '/acf/options-pages.php';
require_once __DIR__ . '/acf/load-fields.php';
require_once __DIR__ . '/acf/acf.php';

// Gravity Forms
require_once __DIR__ . '/gform.php';

// Content registration + queries
require_once __DIR__ . '/post-types.php';
require_once __DIR__ . '/taxonomies.php';
require_once __DIR__ . '/queries.php';

// Tracking / dataLayer
require_once __DIR__ . '/tracking.php';

// Upload tweaks
require_once __DIR__ . '/svg.php';

