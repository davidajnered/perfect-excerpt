<?php
/**
 * Perfect Excerpt is a WordPress plugin that shortens your excerpts to whole sentences.
 * Written by David Ajnered
 */
namespace PerfectExcerpt;

class PerfectExcerpt
{

    /**
     * @var int
     */
    private $excerptLength;

    /**
     * @var array
     */
    private $punctuations = [
        'dot' => '.',
        'question_mark' => '?',
        'exclamation_mark' => '!',
    ];

     /**
     * Singleton.
     *
     * @return PerfectExcerpt
     */
    public static function getObject()
    {
        static $instance;

        if (!$instance) {
            $instance = new PerfectExcerpt();
        }

        return $instance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->excerptLength = get_option('excerpt_length', 55);
        add_filter('the_excerpt', array($this, 'make'), 999, 2);
    }

    /**
     * Shorten excerpt to the sentence closest to the word length.
     *
     * @param string $excerpt
     */
    public function make($excerpt, $length = null)
    {
        // If length is not passed as argument, use option value or WP default.
        if (!$length) {
            $length = $this->excerptLength;
        }

        // Remove markup from excerpt. utf8 decode so char count is accurate.
        $excerpt = utf8_decode(strip_tags($excerpt));

        // If text is shorter than the requested length, return the whole text
        if (strlen($excerpt) <= $length) {
            return utf8_encode($excerpt);
        }

        $breakPositions = [];

        // Loop throught all possible break positions and find where they are in the string
        foreach ($this->punctuations as $punctuation) {
            $offset = 0;
            while (($position = strpos($excerpt, $punctuation, $offset)) != false) {
                // Save break position
                $breakPositions[] = $position + 1;
                // Update offset for while loop
                $offset = $position + 1;
            }
        }

        asort($breakPositions);

        $breakAt = 0;
        foreach ($breakPositions as $breakPosition) {
            if ($breakPosition < $length) {
                $breakAt = $breakPosition;
            }
        }

        $excerpt = substr($excerpt, 0, $breakAt);

        return utf8_encode($excerpt);
    }
}
