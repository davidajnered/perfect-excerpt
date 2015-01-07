<?php
/**
 *
 */
namespace PerfectExcerpt;

class Options
{
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var array
     */
    private $options = array(
        'length',
        'text'
    );

    /**
     * @var string default format for options.
     */
    private $optionKeyTemplate = 'perfect_excerpt_%s_term_%s';

    /**
     * Contructor.
     */
    public function __construct()
    {
        add_filter('edit_category_form', [$this, 'categoryOptions']);
        add_filter('edited_terms', [$this, 'updateOptions']);
        add_filter('deleted_term_taxonomy', [$this, 'removeOptions']);
    }

    /**
     * Get option value from.
     *
     * @param string $name
     */
    public function getOption($option, $termId)
    {
        $optionKey = sprintf($this->optionKeyTemplate, $option, $termId);
        return get_option($optionKey, '');
    }

    /**
     * Get option name and value for template.
     *
     * @param string $option
     * @param int $termId
     */
    public function getOptionNameAndValueTmplData($option, $termId = null)
    {
        return array(
            $option . '_name' => $option,
            $option . '_value' => get_option('perfect_excerpt_' . $option . '_term_' . $termId, '')
        );
    }

    /**
     * Get option name name and value.
     *
     * @param string $option
     * @param int $termId
     */
    public function getOptionNameAndValue($option, $termId = null)
    {
        $optionKey = sprintf($this->optionKeyTemplate, $option, $termId);

        return array(
            'name' => $option,
            'value' => get_option($optionKey, '')
        );
    }

    /**
     * Render option to page.
     *
     * @param array $termData
     */
    public function categoryOptions($termData)
    {
        $termId = !empty($termData) && isset($termData->term_id) ? $termData->term_id : '';
        $data = array('term_id' => $termId);

        foreach ($this->options as $option) {
            $data += $this->getOptionNameAndValueTmplData($option, $termId);
        }

        $viewsHandler = new ViewsHandler();
        $viewsHandler->render('form', $data);
    }

    /**
     * Update option.
     *
     * @param int $termId
     */
    public function updateOptions($termId)
    {
        foreach ($this->options as $option) {
            $optionNameAndValue = $this->getOptionNameAndValue($option);
            if (isset($_POST[$optionNameAndValue['name']])) {
                $value = $_POST[$optionNameAndValue['name']];
                $optionKey = sprintf($this->optionKeyTemplate, $option, $termId);
                update_option($optionKey, $value);
            }
        }
    }

    /**
     * Delete options for term.
     *
     * @param int $termId
     */
    public function removeOptions($termId)
    {
        foreach ($this->options as $option) {
            $optionKey = sprintf($this->optionKeyTemplate, $option, $termId);
            delete_option($optionKey);
        }
    }
}