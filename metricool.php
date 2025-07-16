<?php
/**
 * @package Metricool
 * @author Really Simple Plugins
 * @copyright 2025 Really Simple Plugins
 * @license GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Metricool
 * Plugin URI: https://metricool.com/
 * Description: Allows you to track your users and readers using metricool.com
 * Version: 2.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Really Simple Plugins
 * Author URI: https://really-simple-plugins.com
 * License: GPL v2 or later
 * Text Domain: metricool
 * Domain Path: /assets/languages
 */

/**
 * Load the Jetpack packages autoloader.
 * @see https://packagist.org/packages/automattic/jetpack-autoloader
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

// Boot the plugin.
$plugin = new \Metricool\Plugin();
$plugin->boot();