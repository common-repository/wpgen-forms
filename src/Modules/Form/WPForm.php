<?php

namespace WPGenForms\Modules\Form;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WPForm
{

    public $supportedFieldTypes = [
        "text",
        "textarea",
        "select",
        "radio",
        "checkbox",
        "number",
        "email",
        "number-slider",

        // fancy fields
        "phone",
        // "address",
        "date-time",
        "url",
    ];

    public $formName = '';

    public function handle($formName, $formData)
    {
        $this->formName = $formName;

        $content = $this->convert($formData);

        $post = array(
            'post_title' => $formName,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_type' => 'wpforms',
        );

        $postId = wp_insert_post($post);

        if ($postId) {
            $post = get_post($postId);

            // Bind the Post ID to the fields meta
            $postContent = json_decode($post->post_content, true);
            $postContent['id'] = $postId;
            $postContent = wp_json_encode($postContent);
            // Update the post
            wp_update_post(array(
                'ID' => $postId,
                'post_content' => $postContent,
            ));

            update_post_meta($postId, '_by_wpgen_forms', "yes");
            update_post_meta($postId, '_wpgen_forms_reponse_data', $formData);
        }

        return $postId ? $post : null;
    }

    private function convert($formData)
    {
        $data = [];

        $formattedFields = [];

        $serialNumber = 1;
        $fields = $formData['questions'] ?? [];

        foreach ($fields as $field) {
            $fieldType = $field['type'] ?? '';

            if (in_array($fieldType, ['text', 'email', 'textarea', 'number'])) {
                $fieldData = $this->getTextInputFieldByType($field, $serialNumber, $fieldType);
            } else if ("select" == $fieldType) {
                $fieldData = $this->getMultipleChoiceFieldByType($field, $serialNumber, "select");
            } else if ("checkbox" == $fieldType) {
                $fieldData = $this->getMultipleChoiceFieldByType($field, $serialNumber, "checkbox");
            } else if ("radio" == $fieldType) {
                $fieldData = $this->getMultipleChoiceFieldByType($field, $serialNumber, "radio");
            } else if ("number-slider" == $fieldType) {
                $fieldData = $this->getNumberSliderField($field, $serialNumber);
            } else {
                continue;
            }

            $formattedFields[$serialNumber] = $fieldData;
            $serialNumber++;
        }

        $data['fields'] = $formattedFields;
        $data['field_id'] = $serialNumber;
        $data['settings'] = $this->getDefaultSettings();

        return wp_json_encode($data);
    }

    protected function getDefaultSettings()
    {
        return [
            "form_title" => $this->formName,
            "form_desc" => "",
            "submit_text" => "Submit",
            "submit_text_processing" => "Processing...",
            "ajax_submit" => 1,
            "notification_enable" => 1,
            "notifications" => [
                1 => [
                    "email" => "{admin_email}",
                    "subject" => "New Entry: {$this->formName}",
                    "sender_name" => "admin",
                    "sender_address" => "{admin_email}",
                    "message" => "{all_fields}",
                ],
            ],
        ];
    }

    protected function getTextInputFieldByType($field, $serialNumber, $fieldType)
    {
        return [
            "id" => $serialNumber,
            "type" => $fieldType,
            "label" => $field['question'] ?? '',
            "description" => "",
            "placeholder" => '',
            "size" => "medium",
            "required" => $field['required'] ?? false,
            "default_value" => '',
            "css" => '',
        ];
    }

    protected function getMultipleChoiceFieldByType($field, $serialNumber, $fieldType)
    {
        $formattedOptions = [];

        $options = $field['options'] ?? [];
        $isRequired = $field['required'] ?? false;

        $optionSerialNumber = 1;
        foreach ($options as $option) {

            $formattedOptions[$optionSerialNumber] = [
                "label" => $option ?? '',
                "value" => "",
                "image" => "",
                "icon" => "face-smile",
                "icon_style" => "regular",
            ];

            $optionSerialNumber++;
        }

        $fieldInfo = [
            "id" => $serialNumber,
            "type" => $fieldType,
            "label" => $field['question'] ?? '',
            "description" => "",
            "placeholder" => '',
            "required" => $isRequired,
            "choices" => $formattedOptions,
        ];

        if ("checkbox" == $fieldType) {
            $fieldInfo["choices_images_style"] = "modern";
            $fieldInfo["choices_icons_size"] = "large";
            $fieldInfo["choices_icons_style"] = "default";
        }

        return $fieldInfo;
    }

    private function getNumberSliderField($field, $serialNumber)
    {
        $min = $field['min'] ?? 0;
        $max = $field['max'] ?? 10;
        $step = $field['step'] ?? 1;

        return [
            "id" => $serialNumber,
            "type" => "number-slider",
            "label" => $field['question'] ?? '',
            "description" => "",
            "placeholder" => '',
            "size" => "medium",
            "required" => $field['required'] ?? false,
            "min" => $min,
            "max" => $max,
            "step" => $step,
        ];
    }
}