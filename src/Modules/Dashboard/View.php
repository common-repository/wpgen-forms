<?php

namespace WPGenForms\Modules\Dashboard;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class View
{

    public function render($data)
    {
        $doesAnyWPGenFormsSupportedPluginsIsActive = wpgen_forms_does_anyone_supported_plugins_is_active();
        $openAiSecretKey = wpgen_forms_get_option_by_key('open-ai-secret-key');

        $this->printHeaderContent($doesAnyWPGenFormsSupportedPluginsIsActive);

        // Dashboard Cards
        $this->printDashboardCards($data);

        $this->printStepsHowToUse();
        add_thickbox();
        $this->loadModalContent($openAiSecretKey);
    }

    private function printHeaderContent($doesAnyWPGenFormsSupportedPluginsIsActive)
    {
        ?>
<div class="wrap mt-3"> </div>
<div class="wpgen-forms__headerWrapper">
    <h1>Dashboard</h1>
    <?php if ($doesAnyWPGenFormsSupportedPluginsIsActive) {?>
    <a role="button" class="thickbox wpgen-forms__generative-button" title="Create New Form by Generative AI"
        href="<?php echo esc_url('#TB_inline?&width=600&height=300&inlineId=wp-gen-form-creation-modal'); ?>">
        <?php echo esc_html('Add New Form by Generative AI', 'wpgen-forms'); ?>
    </a>
    <?php }?>
</div>
<p>Welcome to WPGen Forms. It's a simple and easy plugin to create a WordPress forms and checklists with help of the
    <b>Generative AI.</b>
</p>
<?php
}

    private function printDashboardCards($data)
    {
        ?>
<div class="wpgen-forms__cards wpgen-forms__mb-2 wpgen-forms__mr-2">
    <?php
$this->printCard(esc_html__('Total Forms Created', 'wpgen-forms'), $data['total_created_forms_count'] ?? 0);
        $this->printCard(esc_html__('Total Active CF7 Forms Count', 'wpgen-forms'), $data['cf7_created_forms_count'] ?? 0);
        $this->printCard(esc_html__('Total Active WPForms Count', 'wpgen-forms'), $data['wpforms_created_forms_count'] ?? 0);
        ?>
</div>
<?php
}

    public function loadModalContent($openAiSecretKey)
    {
        ?>
<div id="wp-gen-form-creation-modal" style="display:none;">
    <?php if (empty($openAiSecretKey)) {?>
    <div class="wpgen-forms__text-danger wpgen-forms__mt-2"><b>* Please enter the OpenAI Secret Key in the settings
            to use this feature.</b></div>
    <?php }?>

    <table class="form-table wpgen-forms__table">
        <tr>
            <th scope="row"><label for="wpgen-forms__form-name">Form Name<span
                        class="wpgen-forms__is-required">*</span></label></th>
            <td><input type="text" id="wpgen-forms__form-name" class="regular-text" required
                    placeholder="<?php echo esc_attr('Ex: Create an Simple Contact Form for WordPress', 'wpgen-forms'); ?>"
                    <?php echo (empty($openAiSecretKey) ? 'disabled' : ''); ?>>
                <p class="wpgen-forms__desc">
                    Give the form/checklist name to the field with the help of <b>Generative AI</b> the form/checklist
                    will
                    generated in WordPress.
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="wpgen-forms__form-plugin">Target Plugin<span
                        class="wpgen-forms__is-required">*</span></label></th>
            <td>
                <select class="wpgen-forms__form-plugin" id="wpgen-forms__form-plugin" required>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="wpgen-forms__form-service-provider">Select the AI Service Provider<span
                        class="wpgen-forms__is-required">*</span></label></th>
            <td>
                <select class="wpgen-forms__form-service-provider" id="wpgen-forms__form-service-provider" required>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td>
                <button
                    class="button button-primary wpgen-forms__generative-button wpgen-forms__generative-button--small text-right"
                    id="wpgen-forms__generative-action" <?php echo (empty($openAiSecretKey) ? 'disabled' : ''); ?>>
                    Generate Form
                </button>
            </td>
        </tr>
    </table>
</div>
<?php
}

    private function printStepsHowToUse()
    {
        ?>
<div class="wpgen-forms__card wpgen-forms__mt-2 wpgen-forms__mr-2">
    <h2 class="wpgen-forms__mb-1">How to use WPGen Forms?</h2>
    <div><i><u>Note:-</u> Before following the below steps please install and activate the Contact Form 7 or
            WPForms plugin to use WPGen Forms.</i></div>
    <p>Follow the below steps to create a form by <b>Generative AI.</b></p>
    <p>1. Click on the <b>Add New Form by Generative AI</b> button to create a new form.</p>
    <p>2. Descripe the content of the form/checklist, and then select the type of plugin to use to create the form.</p>
    <p>3. Click on the <b>Generate Form</b> button to generate the form.</p>
    <p>4. Please <a href="<?php echo esc_url('https://wordpress.org/support/plugin/wpgen-forms/reviews/#new-post'); ?>"
            target="_blank"><strong>click the link</strong></a> to share your feedback and suggestions to improve the
        plugin.</p>
</div>
<?php
}

    private function printCard($title, $count)
    {
        ?>
<div class="wpgen-forms__card">
    <div class="wpgen-forms__card--header">
        <h3><?php echo esc_html($title, 'wpgen-forms'); ?></h3>
    </div>
    <div class="wpgen-forms__card--body">
        <div class="wpgen-forms__count"><?php echo esc_html($count, 'wpgen-forms'); ?></div>
    </div>
</div>
<?php
}
}