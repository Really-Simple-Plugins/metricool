<?php if (!defined('ABSPATH')) {
    exit;
}

// The environment config can be used BEFORE the 'init' hook.
return [
    'plugin' => [
        'name' => 'Metricool',
        'version' => '2.0.0-alpha.1',
        'pro' => true,
        'path' => dirname(__DIR__),
        'base_path' => dirname(__DIR__) . '/' . plugin_basename(dirname(__DIR__)) . '.php',
        'assets_path' => dirname(__DIR__) . '/assets/',
        'lang_path' => dirname(__DIR__) . '/assets/languages/',
        'view_path' => dirname(__DIR__).'/views/',
        'feature_path' => dirname(__DIR__) . '/app/Features/',
        'react_path' => dirname(__DIR__) . '/react',
        'dir' => plugin_basename(dirname(__DIR__)),
        'base_file' => plugin_basename(dirname(__DIR__)) . '/' . plugin_basename(dirname(__DIR__)) . '.php',
        'lang' => plugin_basename(dirname(__DIR__)) . '/assets/languages',
        'url' => plugin_dir_url(__DIR__),
        'assets_url' => plugin_dir_url(__DIR__) . 'assets/',
        'views_url' => plugin_dir_url(__DIR__) . 'views/',
        'react_url' => plugin_dir_url(__DIR__) . 'react',
        'dashboard_url' => admin_url('admin.php?page=metricool'),
        'support_url' => 'https://wordpress.org/support/plugin/metricool/',
        'review_url' => 'https://wordpress.org/support/plugin/metricool/reviews/#new-post',
    ],
    'metricool' => [
        'upgrade_premium_url' => 'https://app.metricool.com/user-settings/plan',
        'connect_network_url' => 'https://app.metricool.com/evolution/brandSummary',
        'connect_linkedin_url' => 'https://app.metricool.com/evolution/linkedin',
        'connect_twitter_url' => 'https://app.metricool.com/evolution/twitter',
        'tracking_script_url' => 'https://tracker.metricool.com/resources/be.js',
        'create_post_url' => 'https://app.metricool.com/planner/post',
    ],
    'http' => [
        'version' => 'v1',
        'namespace' => 'metricool',
    ],
];