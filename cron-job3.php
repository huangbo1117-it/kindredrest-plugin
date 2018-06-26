#!/usr/bin/php -q
<?php
include '../../../wp-load.php';
global $wpdb;

function log_var($tmp_log, $mode = 0) {
    $filename = './log/cron_log3';
    if ($mode == 0) {
        if (is_string($tmp_log)) {
            print_r($tmp_log);
        } else if (is_array($tmp_log)) {
            print_r($tmp_log);
        } else {
            var_dump($tmp_log);
        }
        $myfile = file_put_contents($filename, $tmp_log . PHP_EOL, FILE_APPEND | LOCK_EX);
    } else if ($mode == 1) {
        $myfile = file_put_contents($filename, $tmp_log . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

$op_cron = get_option('kindredrest_cronjob');
if ($op_cron['crond_is_valid'] != 1) {
    $tmp_log = "crond_is_valid Exit " . date("D M d, Y G:i:s");
    log_var($tmp_log);
    exit();
}

$option = get_option('kindredrest_plugin');
$apiKey = $option['api_key'];
$access_token = $option['api_secret'];
$mesmo_id = $option['mesmo_id'];
$mesmo_code = $option['mesmo_code'];
$mesmo_path = "https://mesmo.co/api_sig/data/user" . "$mesmo_id/";

$proeprtiesData = $wpdb->get_results("SELECT * FROM property_ids");
$propIDArr = array();
if (!empty($proeprtiesData)) {
    foreach ($proeprtiesData as $proeprtiesID) {
        $propertyListingtype = $proeprtiesID->type;

        $propIDArr[] = $proeprtiesID->property_id;
    }
}

$tmp_log = "Array of property IDS received from API <br/>";
log_var($tmp_log);
$tmp_log = $propIDArr;
log_var($tmp_log);
$tmp_log = "<br/>";
log_var($tmp_log);
$tmp_log = "DELETED RECORDS<br/>";
log_var($tmp_log);
if (!empty($propIDArr)) {
    $counter = 1;
    $args = array(
        'post_type' => 'property',
        'post_status' => 'publish',
        'meta_key' => 'post_exist',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'post_exist',
                'value' => $propIDArr,
                'compare' => 'NOT IN',
            ),
        )
    );

    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) :
        while ($the_query->have_posts()) : $the_query->the_post();
            $tmp_log = '<br> ' . $counter . '.) ' . get_the_ID() . ' -- ' . get_post_meta(get_the_ID(), 'post_exist', true) . ' -- ' . get_post_meta(get_the_ID(), 'display_address', true) . '<br>';
            log_var($tmp_log);
            wp_trash_post(get_the_ID());
            $counter++;
        endwhile;
        wp_reset_postdata();
    endif;
}
