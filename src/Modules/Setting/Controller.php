<?php

namespace WPGenForms\Modules\Setting;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Controller
{
    public function __construct()
    {

    }

    public function handle()
    {
        $settingData = get_option(WPGEN_FORMS_OPTIONS);

        $settingViewHandler = new View();
        $settingViewHandler->render($settingData);
    }

    public function save()
    {

        $action = sanitize_text_field($_POST['action'] ?? '');
        $wpgenFormsSettingsNonce = sanitize_text_field($_POST['wpgen_forms_settings_nonce'] ?? '');
        $openAiSecretKey = sanitize_text_field($_POST[WPGEN_FORMS_OPTIONS]['open-ai-secret-key'] ?? '');
        $groqSecretKey = sanitize_text_field($_POST[WPGEN_FORMS_OPTIONS]['groq-ai-secret-key'] ?? '');

        if ($action == 'wpgen_forms_settings' && wp_verify_nonce($wpgenFormsSettingsNonce, 'wpgen-forms-settings')) {
            $options = get_option(WPGEN_FORMS_OPTIONS);
            $options['open-ai-secret-key'] = $openAiSecretKey;
            $options['groq-ai-secret-key'] = $groqSecretKey;
            update_option(WPGEN_FORMS_OPTIONS, $options);

            // Redirect to the same page
            wp_redirect(admin_url('admin.php?page=wpgen-forms-settings&notice_status=SETTINGS_SAVED'));

            exit;
        }
    }
}