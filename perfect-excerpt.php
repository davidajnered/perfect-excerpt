<?php
/**
 * Plugin Name: Perfect Excerpt
 * Version: 1.0
 * Plugin URI: http://davidajnered.com
 * Description: Shorten excerpts to whole sentences.
 * Author: David Ajnered
 */

require_once('PerfectExcerpt.php');

/**
 * Initialize plugin
 */
add_action('init', function () {
    $perfectExcerpt = PerfectExcerpt\PerfectExcerpt::getObject();
});

/**
 * Add function to enable developers to programmatically call plugin.
 */
function make_perfect_excerpt($excerpt, $length = null)
{
    $perfectExcerpt = PerfectExcerpt\PerfectExcerpt::getObject();
    return $perfectExcerpt->make($excerpt, $length);
}
