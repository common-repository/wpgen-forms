<?php

namespace WPGenForms\Includes;

use WPGenForms\Modules\Dashboard\Controller as dashboardController;
use WPGenForms\Modules\Setting\Controller as settingController;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed
}

class Menus
{

    private static $instance;
    public static function get_instance()
    {
        if (!isset(self::$instance) && !self::$instance instanceof Menus) {
            self::$instance = new Menus();
            self::$instance->init();
        }
        return self::$instance;
    }

    public function init()
    {
        add_action('admin_menu', array($this, 'admin_menu_callback'));
    }

    public function admin_menu_callback()
    {
        add_menu_page(
            __('WPGen Forms', 'wpgen-forms'),
            __('WPGen Forms', 'wpgen-forms'),
            'manage_options',
            'wpgen-forms',
            array(new dashboardController(), 'handle'),
            'dashicons-feedback',
        );

        add_submenu_page(
            'wpgen-forms', // parent slug
            __('Settings', 'wpgen-forms'), // page title
            __('Settings', 'wpgen-forms'), // menu title
            'manage_options', // capability
            'wpgen-forms-settings', // menu slug
            array(new settingController(), 'handle') // callback function
        );

        add_submenu_page(
            'wpgen-forms', // parent slug
            __('Support Forum', 'wpgen-forms'), // page title
            __('Support Forum', 'wpgen-forms'), // menu title
            'manage_options', // capability
            'wpgen-forms-support-forum', // menu slug
            false
        );
    }
}

Menus::get_instance();