<?php

if (!function_exists('wpgen_forms_get_supported_plugins')) {
    function wpgen_forms_get_supported_plugins()
    {
        $isCF7Active = class_exists('WPCF7') ? true : false;
        $isWPFormsActive = class_exists('WPForms') ? true : false;

        $plugins = [
            'contact-form-7' => [
                'label' => 'Contact Form 7',
                'value' => 'contact-form-7',
                'is_active' => $isCF7Active,
            ],
            'wp-forms' => [
                'label' => 'WP Forms',
                'value' => 'wp-forms',
                'is_active' => $isWPFormsActive,
            ],
        ];
        return $plugins;
    }
}

if (!function_exists('wpgen_forms_does_anyone_supported_plugins_is_active')) {
    function wpgen_forms_does_anyone_supported_plugins_is_active()
    {
        $supportedPlugins = wpgen_forms_get_supported_plugins();
        foreach ($supportedPlugins as $plugin) {
            if ($plugin['is_active']) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('wpgen_forms_get_option_by_key')) {
    function wpgen_forms_get_option_by_key($key)
    {
        if (empty($key)) {
            return '';
        }
        $optins = get_option(WPGEN_FORMS_OPTIONS);
        return $optins[$key] ?? '';
    }
}

if (!function_exists('wpgen_forms_get_created_forms_count')) {
    function wpgen_forms_get_created_forms_count()
    {
        global $wpdb;
        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}postmeta";
        $query .= " WHERE meta_key = '%s' AND meta_value = '%s'";
        $query .= " GROUP BY post_id";
        $createdFormsCount = $wpdb->get_var($wpdb->prepare($query, '_by_wpgen_forms', 'yes'));
        return $createdFormsCount;
    }
}