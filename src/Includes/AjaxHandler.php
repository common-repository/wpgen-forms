<?php

namespace WPGenForms\Includes;

use WPGenForms\Services\Groq;
use WPGenForms\Services\OpenAi;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AjaxHandler
{
    public function init()
    {
        add_action('wp_ajax_wpgen_forms_generate_form', array($this, 'generate_form'));
        add_action('wp_ajax_nopriv_wpgen_forms_generate_form', array($this, 'generate_form'));

    }

    public function generate_form()
    {
        $instance = null;
        $formName = sanitize_text_field(wp_unslash($_POST['form_name']));
        $pluginType = sanitize_text_field(wp_unslash($_POST['plugin_type']));
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
        $serviceProvider = sanitize_text_field(wp_unslash($_POST['service_provider']));

        if (empty($formName) || empty($pluginType)) {
            return wp_send_json([
                'status' => 'error',
                'message' => __('Form Name and Plugin Type are required', 'wpgen-forms'),
            ]);
        }

        if (!wp_verify_nonce($nonce, 'wpgen_forms_generate_form')) {
            return wp_send_json([
                'status' => 'error',
                'message' => __('Invalid Nonce', 'wpgen-forms'),
            ]);
        }

        if ("contact-form-7" == $pluginType) {
            $instance = new \WPGenForms\Modules\Form\ContactForm7();
            $supportedFieldTypes = $instance->supportedFieldTypes;
        } else if ("wp-forms" == $pluginType) {
            $instance = new \WPGenForms\Modules\Form\WPForm();
            $supportedFieldTypes = $instance->supportedFieldTypes;
        }

        if ("open-ai" == $serviceProvider) {
            $openaiService = new OpenAi();
            $response = $openaiService->generateForm($formName, $supportedFieldTypes);
        } else if ("groq" == $serviceProvider) {
            $groqService = new Groq();
            $response = $groqService->generateForm($formName, $supportedFieldTypes);
        }

        $status = $response['status'] ?? 'error';

        if ('error' === $status) {
            return wp_send_json($response);
        }

        $data = $response['data'] ?? [];

        // $sampleResponsePath = WPGEN_FORMS_URL . 'sample/open-ai-raw-response.json';
        // $response = file_get_contents($sampleResponsePath);
        // $data = json_decode($response, true);
        // $formContent = $data['choices'][0]['message']['content'] ?? '';
        // $formData = !empty($formContent) ? json_decode($formContent, true) : [];
        // error_log('Overall Form Data : ' . print_r($data, true));
        $url = "";
        if ("contact-form-7" === $pluginType) {
            $result = $instance->handle($formName, $data);
            $url = admin_url('admin.php?page=wpcf7&action=edit&post=' . $result->ID);
        } else if ("wp-forms" == $pluginType) {
            $result = $instance->handle($formName, $data);
            $url = admin_url('admin.php?page=wpforms-builder&form_id=' . $result->ID);
        }

        $response = array(
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Form Generated Successfully' : 'Error Generating Form',
            'redirect_url' => $url,
        );

        return wp_send_json($response);
    }

}