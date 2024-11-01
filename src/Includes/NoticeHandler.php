<?php

namespace WPGenForms\Includes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class NoticeHandler
{
    public function init()
    {
        add_action('admin_notices', [$this, 'showSettingsSavedNotice']);
        add_action('admin_notices', [$this, 'insistOnActivatingSupportedPlugins']);
    }

    public function showSettingsSavedNotice()
    {
        $page = $_GET['page'] ?? '';
        $noticeStatus = $_GET['notice_status'] ?? '';

        $page = sanitize_text_field($page);
        $noticeStatus = sanitize_text_field($noticeStatus);

        if ($page !== 'wpgen-forms-settings' || $noticeStatus !== 'SETTINGS_SAVED') {
            return;
        }
        ?>

<div class="notice notice-success is-dismissible">
    <p><?php echo esc_html('Settings saved successfully.', 'wpgen-forms'); ?></p>
</div>

<?php
}

    public function insistOnActivatingSupportedPlugins()
    {
        $pluginActive = wpgen_forms_does_anyone_supported_plugins_is_active();

        if ($pluginActive) {
            return;
        }
        ?>
<div class="notice notice-error is-dismissible">
    <p>
        <?php
echo esc_html('🤖 WPGen Forms requires at least one of the following plugins to be active: <a target="_blank" href="' . esc_url('https://wordpress.org/plugins/contact-form-7/') . '"><b>Contact Form 7</b></a>, <a href="' . esc_url('https://wordpress.org/plugins/wpforms-lite/') . '" target="_blank"> <b>WP Forms</b></a>.', 'wpgen-forms');
        ?>
    </p>
</div>
<?php
}
}