<?php
/**
 * Perfect Excerpt is a WordPress plugin that shortens your excerpts to whole sentences.
 * Coded by David Ajnered
 */
namespace PerfectExcerpt;

class PerfectExcerpt
{
    /**
     * @var int
     */
    private $excerptLength;

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
     * Constructor.
     */
    private function __construct()
    {
        $this->excerptLength = get_option('excerpt_length', 275); // Standard word length (5) multiplied with WP default of 55 words
        add_filter('the_excerpt', [$this, 'shorten'], 999, 2);
        add_filter('admin_init', [$this, 'optionPage']);
        add_action('wp_footer', [$this, 'addStyleAndScripts']);
        // add_action('wp_enqueue_scripts', [$this, 'addStyleAndScripts', ['jquery'], false, true);
    }

    /**
     * Singleton.
     *
     * @return PerfectExcerpt
     */
    public static function getInstance()
    {
        static $instance;

        if (!$instance) {
            $instance = new PerfectExcerpt();
        }

        return $instance;
    }

    /**
     * Shorten excerpt to the sentence closest to the word length.
     *
     * @param string $excerpt
     * @param int $length
     * @return array
     */
    public function shorten($content, $length = null, $includeReadMore = false)
    {
        // If length is not passed as argument, use option value or WP default.
        if (!$length) {
            $length = $this->excerptLength;
        }

        // Remove markup from excerpt. utf8 decode so char count is accurate.
        $content = utf8_decode(strip_tags($content));

        if (!$this->validate($content, $length)) {
            return utf8_encode($content);
        }

        $allBreakPoints = $this->findAllBreakPoints($content);
        $finalBreakPoint = $this->findFinalBreakPoint($allBreakPoints, $length);

        $excerpt = utf8_encode(substr($content, 0, $finalBreakPoint));
        $extendedExcerpt = utf8_encode(substr($content, $finalBreakPoint));

        if (!$includeReadMore) {
            return apply_filters('the_content', $excerpt);
        }

        $extendableExcerpt = $this->getFormattedExtendableExcerpt($excerpt, $extendedExcerpt);
        return apply_filters('the_content', $extendableExcerpt);
    }

    /**
     * Combine and formats the excerpt. Adds a read more link and a wrapped div with the extendable content
     * not included in the shortened excerpt. The extendable content is hidden with css and displayed by
     * an click event handler on the read more link.
     *
     * @param string $excerpt
     * @param string $extendedExcerpt
     * @return string $extendableExcerpt
     */
    private function getFormattedExtendableExcerpt($excerpt, $extendedExcerpt)
    {
        $extendableExcerpt = '<div class="perfect-excerpt">';
        $extendableExcerpt .= $excerpt;
        $extendableExcerpt .= '<div class="extendable-excerpt">';
        $extendableExcerpt .= '<a class="extendable-excerpt-action" href="#">' . get_option('excerpt_text', true) . '</a>';
        $extendableExcerpt .= '<div class="extended-excerpt">' . $extendedExcerpt . '</div>';
        $extendableExcerpt .= '</div>';
        $extendableExcerpt .= '</div>';

        return $extendableExcerpt;
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

    /**
     * Register options.
     */
    public function optionPage()
    {
        register_setting('reading', 'excerpt_length', 'intval');
        register_setting('reading', 'excerpt_text', 'esc_attr');
        add_settings_field('excerpt_length', 'Perfect excerpt length', [$this, 'perfectExcerptLengthOption'], 'reading');
        add_settings_field('excerpt_text', 'Perfect excerpt text', [$this, 'perfectExcerptTextOption'], 'reading');
    }

    /**
     * Option callback.
     */
    public function perfectExcerptLengthOption()
    {
        echo '<input type="number" name="excerpt_length" value="' . get_option('excerpt_length', true) . '">';
    }

    /**
     * Option callback.
     */
    public function perfectExcerptTextOption()
    {
        echo '<input type="text" name="excerpt_text" value="' . get_option('excerpt_text', true) . '">';
    }

    /**
     *
     */
    public function addStyleAndScripts()
    {
        echo "
        <style>
            .extended-excerpt {
                display: none;
            }
        </style>
        ";

        echo "
        <script>
            jQuery('document').ready(function($) {
                $('.extendable-excerpt-action').click(function(event) {
                    event.preventDefault();
                    var excerpt = $(this).closest('.perfect-excerpt');
                    excerpt.find('.extendable-excerpt-action').remove();
                    excerpt.find('.extended-excerpt').fadeIn(1000);
                });
            });
        </script>
        ";
    }

}
