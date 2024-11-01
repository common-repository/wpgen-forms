<?php

/***
 * Plugin Name: WPGen Forms
 * Plugin URI: https://github.com/gnanasekaran08/wpgen-forms-plugin
 * Description: Creating WordPress Forms or Checklists with help of Generative AI...
 * Version: 0.1.7
 * Author: Gnanasekaran R
 * Author URI: https://github.com/gnanasekaran08
 * Text Domain: wpgen-forms
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 *
 * @package WPGen Forms
 * @author sekar0369
 * @license GPL-3.0
 *
 * WPGen Forms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPGen Forms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPGen Forms. If not, see {URI to Plugin License}.
 */

namespace WPGenForms;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!class_exists('WPGen_Forms_Plugin')) {
    class WPGen_Forms_Plugin
    {
        private static $instance;
        public static function get_instance()
        {
            if (!isset(self::$instance) && !self::$instance instanceof WPGen_Forms_Plugin) {
                self::$instance = new WPGen_Forms_Plugin();
                self::$instance->init();
            }
            return self::$instance;
        }

        public static function init()
        {
            self::$instance->setup_constants();
            self::$instance->pluginActivation();
        }

        public static function setup_constants()
        {

            $constants = [
                'WPGEN_FORMS_VERSION' => '0.1.7',
                'WPGEN_FORMS_DOMAIN' => 'wpgen-forms',
                'WPGEN_FORMS__FILE__' => __FILE__,
                'WPGEN_FORMS_PLUGIN_BASE' => plugin_basename(__FILE__),
                'WPGEN_FORMS_PATH' => plugin_dir_path(__FILE__),
                'WPGEN_FORMS_URL' => plugins_url('/', __FILE__),

                'WPGEN_FORMS_OPTIONS' => 'wpgen_forms_options',
            ];

            foreach ($constants as $constant => $value) {
                if (!defined($constant)) {
                    define($constant, $value);
                }
            }
        }

        public static function pluginActivation()
        {
            if (!version_compare(PHP_VERSION, '5.4', '>=')) {
                add_action('admin_notices', [self::$instance, 'wpgen_forms_fail_php_version']);
            } elseif (!version_compare(get_bloginfo('version'), '4.5', '>=')) {
                add_action('admin_notices', [self::$instance, 'wpgen_forms_fail_wp_version']);
            } else {
                require plugin_dir_path(__FILE__) . 'src/plugin.php';
            }
        }

        /**
         * Show in WP Dashboard notice about the plugin is not activated (PHP version).
         * @since 1.0.0
         * @return void
         */
        public function wpgen_forms_fail_wp_version()
        {
            /* translators: %s: PHP version */
            $message = sprintf(esc_html__('WPGen Forms requires PHP version %s+, plugin is currently NOT ACTIVE.', 'wpgen-forms'), '5.4');
            $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
            echo wp_kses_post($html_message);
        }

        /**
         * Show in WP Dashboard notice about the plugin is not activated (WP version).
         * @since 1.5.0
         * @return void
         */
        public function wpgen_forms_fail_php_version()
        {
            /* translators: %s: WP version */
            $message = sprintf(esc_html__('WPGen Forms requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT ACTIVE.', 'wpgen-forms'), '4.5');
            $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
            echo wp_kses_post($html_message);
        }
    }
}

WPGen_Forms_Plugin::get_instance();