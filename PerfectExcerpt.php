<?php
namespace PerfectExcerpt;

class PerfectExcerpt
{

    /**
     * @var int
     */
    private $excerptLength;

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
        if (!$length) {
            $length = $this->excerptLength;
        }

        // Remove markup from excerpt. utf8 decode so char count is accurate.
        $excerpt = utf8_decode(strip_tags($excerpt));

        // If text is shorter than the requested length, return the whole text
        if (strlen($excerpt) <= $length) {
            return utf8_encode($excerpt);
        }

        // Search for the sentence break closes to the text length, starting at position 0
        $searching = true;
        $currentEndOfExcerpt = 0;
        while ($searching) {
            // Find end of next sentence
            $endOfNextSentence = $this->findNextSentenceBreak($excerpt, $currentEndOfExcerpt);

            // Stop searching if end of sentence is bigger than the length
            if ($endOfNextSentence > $length) {
                $excerpt = substr($excerpt, 0, $currentEndOfExcerpt);
                break;
            }

            // Loop still continues so we update the currentEndOfExcerpt to new position
            $currentEndOfExcerpt = $endOfNextSentence;
        }

        // Restore encoding
        return utf8_encode($excerpt);
    }

    /**
     * Find the next punctuation.
     *
     * @param string $excerpt
     * @param int $offset
     * @return string
     */
    private function findNextSentenceBreak($excerpt, $offset)
    {
        $punctuations = [
            'dot' => '.',
            'question_mark' => '?',
            'exclamation_mark' => '!',
        ];

        // Loop through punctuations and find the first occurrence
        $break_positions = [];
        foreach ($punctuations as $punctuation) {
            // Make sure the punctuation is present
            if (($break_position = strpos($excerpt, $punctuation, $offset)) !== false) {
                // Store break position in array
                $break_positions[] = $break_position + 1;
            }
        }

        if (!empty($break_positions)) {
            // Sort array so the first occurance is at the first position of the array
            asort($break_positions);
            return $break_positions[0];
        } else {
            return strlen($text);
        }
    }
}