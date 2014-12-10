<?php
/**
 * Plugin Name: Perfect Excerpt
 * Version: 1.5-dev
 * Plugin URI: http://davidajnered.com
 * Description: Perfect Excerpt is a WordPress plugin that shortens your excerpts to whole sentences.
 * Author: David Ajnered
 */
require_once('PerfectExcerpt.php');

/**
 * Initialize plugin, but only if option perfect_excerpt_disable_auto_init is false or not set at all.
 */
if (!get_option('perfect_excerpt_disable_auto_init')) {
    add_action('init', function () {
        $perfectExcerpt = PerfectExcerpt\PerfectExcerpt::getInstance();
    });
}

/**
 * Add function to enable developers to programmatically call plugin.
 */
function perfect_excerpt($excerpt, $length = null, $includeReadMore = false)
{
    $perfectExcerpt = PerfectExcerpt\PerfectExcerpt::getInstance();
    return $perfectExcerpt->shorten($excerpt, $length, $includeReadMore);
}
