<?php

namespace WPGenForms\Includes;

use WPGenForms\Includes\Menus as WPGenFormsMenus;
use WPGenForms\Includes\NoticeHandler;
use WPGenForms\Modules\Setting\Controller as settingController;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Actions
{
    public function __construct()
    {

        /** plugin activation Hook */
        register_activation_hook(WPGEN_FORMS__FILE__, array($this, 'activation_hook_callback'));

        /** plugin deactivation Hook */
        register_deactivation_hook(WPGEN_FORMS__FILE__, array($this, 'deactivation'));

        WPGenFormsMenus::get_instance();

        add_action('admin_init', [new settingController(), 'save']);

        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        (new AjaxHandler())->init();
        (new NoticeHandler())->init();
    }

    public function activation_hook_callback()
    {

    }

    public function deactivation()
    {
        $options = get_option(WPGEN_FORMS_OPTIONS);

        $shouldRemovedProps = ['open-ai-secret-key'];

        foreach ($options as $optionKey => $optionValue) {
            if (in_array($optionKey, $shouldRemovedProps)) {
                unset($options[$optionKey]);
            }
        }

        update_option(WPGEN_FORMS_OPTIONS, $options);
    }

    public function enqueue_admin_scripts()
    {
        $bundle_name = WPGEN_FORMS_DOMAIN . '-admin-bundle';
        wp_enqueue_style($bundle_name, WPGEN_FORMS_URL . 'dist/main.css', array(), WPGEN_FORMS_VERSION, 'all');
        wp_enqueue_script($bundle_name, WPGEN_FORMS_URL . 'dist/main.js', array('jquery'), WPGEN_FORMS_VERSION, true);

        $localizeObject = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpgen-forms-nonce'),
            'wpgen_forms_generate_form_nonce' => wp_create_nonce('wpgen_forms_generate_form'),
            'available_plugins' => $this->getAvailablePlugins(),
            'available_service_providers' => $this->getAvailableAIProviders(),
            'support_forum_url' => esc_url('https://wordpress.org/support/plugin/wpgen-forms'),
        );

        wp_localize_script($bundle_name, 'wpgen_forms', $localizeObject);

    }

    private function getAvailablePlugins()
    {
        $isCF7Active = class_exists('WPCF7') ? true : false;
        $isWPFormsActive = class_exists('WPForms') ? true : false;

        $plugins = [];

        if ($isCF7Active) {
            $plugins[] = [
                'label' => 'Contact Form 7',
                'value' => 'contact-form-7',
            ];
        }

        if ($isWPFormsActive) {
            $plugins[] = [
                'label' => 'WP Forms',
                'value' => 'wp-forms',
            ];
        }
        return $plugins;
    }

    public function getAvailableAIProviders()
    {
        $options = get_option(WPGEN_FORMS_OPTIONS);

        return [
            [
                'label' => 'OpenAI',
                'value' => 'open-ai',
                'is_active' => isset($options['open-ai-secret-key']) && !empty($options['open-ai-secret-key']),
            ],
            [
                'label' => 'Groq',
                'value' => 'groq',
                'is_active' => isset($options['groq-ai-secret-key']) && !empty($options['groq-ai-secret-key']),
            ],
        ];
    }
}