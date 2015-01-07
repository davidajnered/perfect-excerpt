<?php
/**
 *
 */
namespace PerfectExcerpt;

class ViewsHandler
{
    /**
     *
     */
    public function render($view, $data)
    {
        $viewPath = PE_ABSPATH . 'views/' . $view . '.html';
        if (file_exists($viewPath) && !empty($data)) {
            $viewContent = file_get_contents($viewPath);

            // Find all placeholders
            preg_match_all('/{{([a-z_]+)}}/', $viewContent, $placeholders);

            // Ugly fix for bad regex
            $placeholders = $placeholders[1];
            foreach ($placeholders as $placeholder) {
                // Add empty string if no placeholder data
                if (!isset($data[$placeholder])) {
                    $data[$placeholder] = '';
                }

                // Format excerpt line breaks
                if ($placeholder == 'excerpt' || $placeholder == 'extended_excerpt') {
                    $data[$placeholder] = wpautop($data[$placeholder]);
                }
            }

            foreach ($data as $name => $value) {
                $viewContent = str_replace('{{' . $name . '}}', $value, $viewContent);
            }

            echo $viewContent;
        }
    }
}