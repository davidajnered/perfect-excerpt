<?php
/**
 * Plugin Name: Perfect Excerpt
 * Version: 1.0
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
        $perfectExcerpt = PerfectExcerpt\PerfectExcerpt::getObject();
    });
}

/**
 * Add function to enable developers to programmatically call plugin.
 */
function make_perfect_excerpt($excerpt, $length = null)
{
    $perfectExcerpt = PerfectExcerpt\PerfectExcerpt::getObject();
    return $perfectExcerpt->make($excerpt, $length);
}
