<?php

namespace WPGenForms\Services;

class Groq
{
    public $endpoint = "https://api.groq.com/openai/v1/chat/completions";

    public $model = "llama3-8b-8192";

    public $secretKey = "";

    public $botMessage = [
        "role" => 'system',
        "content" => "I am bot (Form Creator), and I am here to help you create a WordPress forms. The following fields are only supported in our system that are {supportedFields}. Always given the most top 10 important questions only with options. Here is the sample format of the form object: {\n 'name': 'form name', 'questions' : [\n {\n'type': 'checkpoint','question': 'name of the question','options':['opt-1','opt-2'], 'required': true ]\n }\n ] \n}.",
    ];

    public function getSecretKey()
    {
        $options = get_option(WPGEN_FORMS_OPTIONS);
        $secretKey = $options['groq-ai-secret-key'] ?? '';
        $this->secretKey = $secretKey;
        return $secretKey;
    }

    private function getUserMessage($prompt)
    {
        return [
            "role" => "user",
            "content" => "Create a '$prompt' form. Response should be a json object. And, it contains name (name of the checklist) and questions (collection of fields). Don't put extra content to the response.",
        ];
    }

    public function generateForm($prompt, $supportedFieldTypes)
    {
        $userMessage = $this->getUserMessage($prompt);
        $botMessage = $this->botMessage;
        // TODO: Add supported field types in the bot message
        $supportedFields = implode(", ", $supportedFieldTypes);
        $botMessage['content'] = str_replace("{supportedFields}", $supportedFields, $botMessage['content']);

        $messages = array_merge([$botMessage, $userMessage]);

        $payload = [
            "model" => $this->model,
            "messages" => $messages,
        ];

        $reponse = $this->makeApiRequest($payload);
        return $reponse;
    }

    private function makeApiRequest($payload)
    {

        $this->getSecretKey();

        if (empty($this->secretKey)) {
            $message = __("API key is missing or invalid. Please add a valid API key in the plugin settings.", "wpgen-forms");
            return [
                'status' => 'error',
                'message' => $message,
                'data' => [],
            ];
        }

        $request = wp_remote_post($this->endpoint, array(
            'body' => wp_json_encode($payload),
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
            ),
        ));

        $bodyData = wp_remote_retrieve_body($request);
        $statusCode = wp_remote_retrieve_response_code($request);

        $response = json_decode($bodyData, true);

        $objectId = $response['id'] ?? "";
        if (!$objectId) {
            return [
                'status' => 'error',
                'message' => __("Something Went Wrong. Try later...", "wpgen-forms"),
                'data' => [],
            ];
        }

        $content = $response['choices'][0]['message']['content'] ?? "";
        $content = trim($content);
        $data = !empty($content) ? json_decode($content, true) : [];

        return [
            'status' => 'success',
            'message' => __("Form Generated Successfully", "wpgen-forms"),
            'data' => $data,
        ];
    }
}