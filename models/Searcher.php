<?php
/**
 *
 */
namespace PerfectExcerpt;

class Searcher
{
    /**
     * Array containing all break points. Probably needs to be extended for use with some languages.
     *
     * @var array
     */
    private $punctuations = [
        'dot' => '.',
        'question_mark' => '?',
        'exclamation_mark' => '!',
    ];

    /**
     * @var int
     */
    private $excerptLength;

    /**
     * Contructor.
     */
    public function __construct()
    {
        $this->excerptLength = get_option('excerpt_length', 275); // Standard word length (5) multiplied with WP default of 55 words
    }

    /**
     * Shorten excerpt to the sentence closest to the length.
     *
     * @param string $excerpt
     * @param int $length
     * @return array
     */
    public function shorten($content, $includeReadMore = false, $length = null)
    {
        // If length is not passed as argument, use option value or WP default.
        if (!$length) {
            $length = $this->excerptLength;
        }

        // Remove markup from excerpt. utf8 decode so char count is accurate.
        $content = utf8_decode(strip_tags($content));

        if (!$this->validate($content, $length)) {
            return array('excerpt' => utf8_encode($content));
        }

        $allBreakPoints = $this->findAllBreakPoints($content);
        $finalBreakPoint = $this->findFinalBreakPoint($allBreakPoints, $length);

        $excerpt = utf8_encode(substr($content, 0, $finalBreakPoint));
        $extendedExcerpt = $includeReadMore ? utf8_encode(substr($content, $finalBreakPoint)) : '';

        return array(
            'excerpt' => apply_filters('the_content', $excerpt),
            'extended_excerpt' => apply_filters('the_content', $extendedExcerpt)
        );
    }

    /**
     * Validate excerpt length.
     *
     * @param string $excerpt
     * @param int $length
     * @return boolean
     */
    private function validate($excerpt, $length = null)
    {
        // If text is shorter than the requested length, return the whole text
        if (strlen($excerpt) > $length) {
            return true;
        }

        return false;
    }

    /**
     * Find all possible break points.
     *
     * @param string $content
     * @return arrray $breakPoints
     */
    private function findAllBreakPoints($content)
    {
        $breakPoints = [];

        // Loop throught all possible break positions and find where they are in the string
        foreach ($this->punctuations as $punctuation) {
            $offset = 0;
            while (($position = strpos($content, $punctuation, $offset)) != false) {
                // Save break position
                $breakPoints[] = $position + 1;
                // Update offset for while loop
                $offset = $position + 1;
            }
        }

        asort($breakPoints);

        return $breakPoints;
    }

    /**
     * Find the final break points, the one that's going to be used.
     *
     * @param array $allBreakPoints
     * @param int $length
     * @return int $finalBreakPoint
     */
    private function findFinalBreakPoint($allBreakPoints, $length)
    {
        $finalBreakPoint = 0;
        foreach ($allBreakPoints as $breakPoint) {
            if ($breakPoint < $length) {
                $finalBreakPoint = $breakPoint;
            }
        }

        return $finalBreakPoint;
    }
}