<?php
/**
 * Perfect Excerpt is a WordPress plugin that shortens your excerpts to whole sentences.
 * Coded by David Ajnered
 */
namespace PerfectExcerpt;

class Controller
{
    /**
     * @var bool
     */
    private $autoInitDisabled;

    /**
     * Constructor.
     */
    private function __construct()
    {
        // Load settings
        $this->autoInitDisabled = get_option('perfect_excerpt_disable_auto_init', true);

        // Run wordpress hooks depending on context
        if (is_admin()) {
            add_filter('admin_init', function() {
                $options = new Options();
            });
        } else {
            add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
            add_action('wp_enqueue_scripts', [$this, 'enqueueStyle']);
        }

        if (!$this->autoInitDisabled) {
            add_filter('the_excerpt', [$this, 'shorten'], 999, 2);
        }
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
            $instance = new Controller();
        }

        return $instance;
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
    public function getExcerpt($excerpt, $category, $includeReadMore = false, $length = null)
    {
        $searcher = new Searcher();
        $viewHandler = new ViewsHandler();
        $options = new Options();

        $category = get_category_by_slug($category);
        $termId = $category->term_id;
        if (!$length) {
            $length = $options->getOption('length', $termId);
        }

        $viewData = $searcher->shorten($excerpt, $includeReadMore, $length);

        $readMoreText = $options->getOption('text', $termId);
        $viewData['excerpt_text'] = !empty($viewData['extended_excerpt']) ? $readMoreText : '';

        $viewHandler->render('output', $viewData);
    }

    /**
     * Enqueue styles.
     */
    public function enqueueStyle()
    {
        wp_enqueue_style('perfect-excerpt-css', get_site_url() . '/' . PE_RELPATH . 'style.css');
    }

    /**
     * Enqueue scripts.
     */
    public function enqueueScripts()
    {
        wp_enqueue_script('perfect-excerpt-js', get_site_url() . '/' . PE_RELPATH . 'script.js', ['jquery'], false, true);
    }

}
