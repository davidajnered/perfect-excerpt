<?php
/**
 * Plugin Name: Perfect Excerpt
 * Version: 1.5-dev
 * Plugin URI: http://davidajnered.com
 * Description: Perfect Excerpt is a WordPress plugin that shortens your excerpts to whole sentences.
 * Author: David Ajnered
 */
namespace PerfectExcerpt;

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define constants.
 */
if (!defined('PE_RELPATH')) {
    define('PE_RELPATH', str_replace(ABSPATH, '', plugin_dir_path(__FILE__)));
}

if (!defined('PE_ABSPATH')) {
    define('PE_ABSPATH', plugin_dir_path(__FILE__));
}

/**
 * Autoload classes.
 *
 * @param callable
 */
spl_autoload_register(function ($className) {
    if (strpos($className, __NAMESPACE__) === 0) {
        $classPath = str_replace(__NAMESPACE__, '', str_replace('\\', '/', $className)) . '.php';
        $classPath = PE_ABSPATH . 'models' . $classPath;

        if (file_exists($classPath)) {
            include_once($classPath);
        }
    }
});

/**
 * Initialize plugin.
 */
add_action('init', function () {
    $perfectExcerpt = Controller::getInstance();
});

$perfectExcerpt = Controller::getInstance();

/**
 * Add function to enable developers to programmatically call plugin.
 *
 * @param string $excerpt
 * @param int $length
 * @param bool $includeReadMore
 * @return string
 */
function perfect_excerpt($excerpt, $length = null, $includeReadMore = false)
{
    $perfectExcerpt = Controller::getInstance();
    return $perfectExcerpt->shorten($excerpt, $length, $includeReadMore);
}
