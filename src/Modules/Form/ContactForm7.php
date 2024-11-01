<?php

namespace WPGenForms\Modules\Form;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class ContactForm7
{

    public $supportedFieldTypes = [
        "text",
        "email",
        "url",
        "tel",
        "number",
        "textarea",
        "select",
        "checkbox",
        "radio",
    ];

    public $formName = '';

    public function handle($formName, $formData)
    {

        $this->formName = $formName;

        // Converting the object into CF7 Content
        $content = $this->convert($formData);

        // Create a new CF7 post
        $post = array(
            'post_title' => $formName,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_type' => 'wpcf7_contact_form',
        );
        $postId = wp_insert_post($post);

        // update hash
        if ($postId) {
            $post = get_post($postId);
            update_post_meta($postId, '_hash', sha1($postId));
            update_post_meta($postId, '_form', $content);

            // Add a custom meta to identify the form
            update_post_meta($postId, '_by_wpgen_forms', "yes");
            // Store the response data which is generated by the AI
            update_post_meta($postId, '_wpgen_forms_reponse_data', $formData);
        }

        return $postId ? $post : null;
    }

    private function convert($formData)
    {
        $content = '';
        $fields = $formData['questions'] ?? [];

        foreach ($fields as $field) {
            $fieldType = $field['type'] ?? 'text';
            if ("text" == $fieldType) {
                $content .= $this->getTextFieldContent($field, 'text');
            } else if ("textarea" == $fieldType) {
                $content .= $this->getTextFieldContent($field, "textarea");
            } else if ("numeric" == $fieldType) {
                $content .= $this->getTextFieldContent($field, "number");
            } else if ("email" == $fieldType) {
                $content .= $this->getTextFieldContent($field, "email");
            } else if ("tel" == $fieldType) {
                $content .= $this->getTextFieldContent($field, "tel");
            } else if ("url" == $fieldType) {
                $content .= $this->getTextFieldContent($field, "url");
            } else if ("select" == $fieldType) {
                $content .= $this->getSelectFieldContent($field);
            } else if ("checkbox" == $fieldType) {
                $content .= $this->getCheckboxFieldContent($field);
            } else if ("radio" == $fieldType) {
                $content .= $this->getRadioFieldContent($field);
            }
        }

        // Add line break
        $content .= "\n";

        // Submit Action Button
        $content .= '[submit]';
        return $content;
    }

    protected function getTextFieldContent($data, $fieldType = 'text')
    {
        $randomId = uniqid();
        // Extraction
        $question = $data['question'] ?? '';
        $isRequired = $data['required'] ?? false;

        // Attributes
        $attributes = [];
        $attributes[] = $isRequired ? "{$fieldType}*" : "{$fieldType}";
        $attributes[] = "text-{$randomId}";

        $optionalContent = $isRequired ? '' : ' (Optional)';
        // Content
        $content = '';
        $content .= "<label>{$question} {$optionalContent} \n";
        $content .= "\t[" . implode(" ", $attributes) . "]\n";
        $content .= "</label>";

        $content .= "\n\n";

        return $content;
    }

    protected function getSelectFieldContent($data)
    {
        $randomId = uniqid();

        // Extraction
        $question = $data['question'] ?? '';
        $isRequired = $data['required'] ?? false;
        $options = $data['options'] ?? [];

        // Attributes
        $attributes = [];
        $attributes[] = $isRequired ? "select*" : "select";
        $attributes[] = "select-{$randomId}"; // field name
        $attributes[] = "include_blank"; // Including blank option

        // Options
        foreach ($options as $option) {
            $attributes[] .= "'{$option}'";
        }

        // Content
        $content = '';
        $content .= "<label>{$question} \n";
        $content .= "\t[" . implode(" ", $attributes) . "]\n";
        $content .= "</label>";

        $content .= "\n\n";
        return $content;
    }

    protected function getCheckboxFieldContent($data)
    {
        $uniqueId = uniqid();
        $question = $data['question'] ?? '';
        $isRequired = $data['required'] ?? false;
        $options = $data['options'] ?? [];

        $attributes = [];
        $attributes[] = $isRequired ? "checkbox*" : "checkbox";
        $attributes[] = "checkbox-{$uniqueId}";

        foreach ($options as $option) {
            $attributes[] = "'{$option}'";
        }

        $content = '';
        $content .= "<label>{$question} \n";
        $content .= "\t[" . implode(" ", $attributes) . "]\n";
        $content .= "</label>";

        $content .= "\n\n";
        return $content;
    }

    protected function getRadioFieldContent($data)
    {
        $uniqueId = uniqid();
        $question = $data['question'] ?? '';
        $isRequired = $data['required'] ?? false;
        $options = $data['options'] ?? [];

        $attributes = [];
        $attributes[] = $isRequired ? "radio" : "radio";
        $attributes[] = "radio-{$uniqueId}";

        foreach ($options as $option) {
            $attributes[] = "'{$option}'";
        }

        $content = '';
        $content .= "<label>{$question} \n";
        $content .= "\t[" . implode(" ", $attributes) . "]\n";
        $content .= "</label>";

        $content .= "\n\n";
        return $content;
    }
}
