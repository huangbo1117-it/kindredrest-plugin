<!--#!/usr/bin/php -q-->
<?php
include '../../../wp-load.php';
global $wpdb;

function curl_get_contents($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function log_var($tmp_log, $mode = 0) {
    $filename = './log/cron_log1';
    if ($mode == 0) {
        if (is_string($tmp_log)) {
            echo $tmp_log;
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
$mesmo_path = "https://mesmo.co/api_sig/data/user$mesmo_id/";
//$mesmo_path = "http://localhost:88/api_sig/data/user$mesmo_id/";
//$mesmo_path = "C:/xampp7/htdocs/api_sig/data/user$mesmo_id/";
$a = 1;

//var_dump($option);
//return;

$propIDArr = array();
$baseurl = 'https://integrations.mydesktop.com.au/api/v1.2';
$nextLinkData = null;
$iteration = 0;
$tmp_path = $mesmo_path . 'kindred2/data.m';

log_var(date("D M d, Y G:i:s"), 1);
log_var($tmp_path, 1);

$tmp_log = "\r\n\r\n--------------------\r\nNew execution at " . time() . "\r\n--------------------";
log_var($tmp_log);

$file = file_get_contents($tmp_path);

$array1 = explode('|', $file);
log_var($array1);
foreach ($array1 as $propertiess) {
    ///print_r($propertiess);
    $properties = explode(',', $propertiess);
    //print_r($properties);
    if ($properties[1] == 1 || $properties[2] == 1) {
        //print_r($properties);

        $proId = $properties[0];

        if ($properties[2] == 1) {
            $type = 'sale';
        } elseif ($properties[1] == 1) {
            $type = 'rent';
        }
        if ($type == 'rent' || $type == 'sale') {
            $propIDArr[] = $proId;
            $chkprop = $wpdb->get_results("SELECT * FROM property_ids WHERE property_id = '$proId'");
            if (empty($chkprop)) {
                if ($wpdb->query("INSERT INTO property_ids (property_id, type) VALUES('$proId', '$type')")) {
                    $tmp_log = "\r\n " . $proId . " Inserted,";
                    log_var($tmp_log);
                } else {
                    $tmp_log = "\r\n " . $proId . " Not Inserted,";
                    log_var($tmp_log);
                }
            } else {
                $tmp_log = "\r\n " . $proId . " Already Exists,";
                log_var($tmp_log);
            }
        }
        $a++;
    }
}
if (!empty($propIDArr)) {
    $proIds = implode(',', $propIDArr);
    $tmp_log = "\r\nSELECT * FROM property_ids WHERE property_id NOT IN ($proIds)";
    log_var($tmp_log);
    $chkpropID = $wpdb->get_results("SELECT * FROM property_ids WHERE property_id NOT IN ($proIds)");
    if (!empty($chkpropID)) {
        foreach ($chkpropID as $id) {
            $curID = $id->id;
            $tmp_log = "\r\nDELETE FROM property_ids WHERE id='$curID'";
            log_var($tmp_log);
            $wpdb->query("DELETE FROM property_ids WHERE id='$curID'");
        }
    }
}
