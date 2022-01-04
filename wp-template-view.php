<?php
/**
 * WP Template View
 *
 * Easily see what custom templates your pages are using.
 *
 * PHP version 7.4.27
 *
 * @category WordPress_Plugin
 * @package  WPTemplateView
 * @author   Tom McFarlin <tom@tommcfarlin.com>
 * @license  GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 * @link     https://github.com/tommcfarlin/wp-template-view/
 * @since    11 November 2021
 *
 * @wordpress-plugin
 * Plugin Name: WP Template View
 * Plugin URI:  https://github.com/tommcfarlin/wp-template-view/
 * Description: Easily see what custom templates your pages are using.
 * Author:      Tom McFarlin <tom@tommcfarlin.com>
 * Version:     1.0.0
 */

namespace WPTemplateView;

use Spatie\Ray;

defined('WPINC') || die;
require_once __DIR__ . '/vendor/autoload.php';

add_filter(
    'manage_edit-page_columns',
    /**
     * Adds a new column to the 'All Page' sub menu item for rendering
     * the name of the template.
     *
     * @param array $columnName The array of columns for the page
     *
     * @return array            An updated array of columns.
     */
    function (array $pageColumns) {
        return array_merge(
            $pageColumns,
            ['template' => 'Template']
        );
    }
);

add_action(
    'manage_page_posts_custom_column',
    /**
     * Determines the name of the template applied to the current
     * page in the columns. If it's the standard template, then 'Default'
     * is rendered.
     *
     * @param string $columnName The name of the column currently being rendered.
     */
    function (string $columnName) {
        if ('template' !== strtolower($columnName)) {
            return;
        }

        // Read the name of the template applied to this post.
        global $post;
        $templateName = \get_page_template_slug($post->ID);

        // Locate the template associated with this post, if it exists.
        $template = locate_template($templateName, false, false);

        // If its empty, use 'Default'; otherwise, use the template name.
        if (empty($template)) {
            $templateName = 'Default';
        } else {
            $templateData = implode('', file($template));
            if (preg_match('|Template Name:(.*)$|mi', $templateData, $name)) {
                $templateName = \_cleanup_header_comment($name[1]);
            }
        }

        echo esc_html($templateName);
    }
);
