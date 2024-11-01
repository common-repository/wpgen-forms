<?php

namespace WPGenForms\Modules\Dashboard;

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
        $data = $this->getDashboardData();

        $dashboardViewHandler = new View();
        $dashboardViewHandler->render($data);
    }

    public function getDashboardData()
    {
        $data = [];

        global $wpdb;

        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}posts";
        $query .= " left join {$wpdb->prefix}postmeta on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id";
        $query .= " WHERE post_type = '%s' AND post_status = '%s'";
        $query .= " and {$wpdb->prefix}postmeta.meta_key = '%s' and {$wpdb->prefix}postmeta.meta_value = '%s'";
        $cf7CreatedFormsCount = $wpdb->get_var($wpdb->prepare($query, 'wpcf7_contact_form', 'publish', '_by_wpgen_forms', 'yes'));

        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}posts";
        $query .= " left join {$wpdb->prefix}postmeta on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id";
        $query .= " where post_type = '%s' AND post_status = '%s'";
        $query .= " and {$wpdb->prefix}postmeta.meta_key = '%s' and {$wpdb->prefix}postmeta.meta_value = '%s'";
        $wpformsCreatedFormsCount = $wpdb->get_var($wpdb->prepare($query, 'wpforms', 'publish', '_by_wpgen_forms', 'yes'));

        $totalCreatedFormsCount = $cf7CreatedFormsCount + $wpformsCreatedFormsCount;

        $data['total_created_forms_count'] = $totalCreatedFormsCount;
        $data['cf7_created_forms_count'] = $cf7CreatedFormsCount;
        $data['wpforms_created_forms_count'] = $wpformsCreatedFormsCount;

        return $data;
    }

}
