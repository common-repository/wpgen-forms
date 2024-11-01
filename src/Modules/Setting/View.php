<?php

namespace WPGenForms\Modules\Setting;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class View
{

    public function render($settingData = [])
    {
        $nonce = wp_create_nonce('wpgen-forms-settings');
        $openaiSecretKey = $settingData['open-ai-secret-key'] ?? '';
        $groqSecretKey = $settingData['groq-ai-secret-key'] ?? '';

        ?>
<div class="wrap">
    <h1><?php echo esc_html('Settings', 'wpgen-forms'); ?></h1>
</div>

<form method="post" action="options.php">
    <input type="hidden" name="action" value="wpgen_forms_settings" />
    <input type="hidden" name="wpgen_forms_settings_nonce" value="<?php echo esc_html($nonce); ?>" />
    <table class="form-table" width="100%">
        <tr valign="top">
            <th scope="row"><?php echo esc_html("openAI API Secret", "wpgen-forms"); ?></th>
            <td>
                <input type="text" style="width: 80%;" name="wpgen_forms_options[open-ai-secret-key]"
                    value="<?php echo esc_attr($openaiSecretKey, 'wpgen-forms'); ?>" placeholder="Ex: sk-*****">
                <div class="wpgen-form">
                    <i> Note:- To get the API key, you need to create an account in <a
                            href="<?php echo esc_url('https://platform.openai.com/signup') ?>"
                            target="_blank">openAI</a> and get the API key
                        from the dashboard.</i>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php echo esc_html("Groq API Secret", "wpgen-forms"); ?></th>
            <td>
                <input type="text" style="width: 80%;" name="wpgen_forms_options[groq-ai-secret-key]"
                    value="<?php echo esc_attr($groqSecretKey, 'wpgen-forms'); ?>" placeholder="Ex: gsk-*****">
                <div class="wpgen-form">
                    <i> Note:- To get the API key, you need to create an account in <a
                            href="<?php echo esc_url('https://console.groq.com/login') ?>" target="_blank">groq</a>
                        and get the API
                        key
                        from the dashboard.</i>
                </div>
            </td>
        </tr>
    </table>
    <input type="submit" name="submit" id="submit" class="button button-primary"
        value="<?php echo esc_html('Save Changes', 'wpgen-forms'); ?>">
</form>
<?php
}
}