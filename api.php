<?php

header('Content-Type: application/json');
include '../../../wp-load.php';
global $wpdb;

$ret = array();
try {
    require_once './push/push_config.php';


    $obj = new UserTable($g_config);
    $ret = $obj->start();
} catch (Exception $e) {
    $ret['response'] = 600;
    $ret['error'] = $e;
}

echo json_encode($ret);

class UserTable {

    private $fp = NULL;
    private $host = "localhost";
    private $dbname = "reward";
    private $username = "root";
    private $password = "";
    private $config;
    private $limit_datatxt = 1;
    private $limit_datam = 2;
    private $setting_values = array();
    private $crond_values = array();

    function __construct($config) {
        $this->setting_values = array(
            'api_key' => 'Api Key',
            'api_secret' => 'API SECRET',
            'mesmo_code' => 'ACTIVATION CODE',
            'key_isvalid' => 'Api Key is Valid',
            'mesmo_id' => 'Activation Code Row ID',
        );

        $this->crond_values = array(
            'crond_cm_list' => 'Show Commercial listings',
            'crond_resd_list' => 'Show residential listings',
            'crond_show_imglocally' => 'Show Images locally',
            'crond_run_time' => 'Cron run time',
        );

        $this->config = $config;
    }

    function checkValid($data, $action) {
        switch ($action) {
            case 'update_option':
                $required_list = array(
                    'api_key' => 'Api Key',
                    'api_secret' => 'API SECRET',
                );
                foreach ($required_list as $key => $value) {
                    if (!isset($data[$key]) || strlen($data[$key]) <= 0) {
                        return false;
                    }
                }

                return true;
            default:
                break;
        }
        return true;
    }

    function start() {
        $ret = array('response' => 400);
        $action = $_REQUEST['action'];
        switch ($action) {
            case 'update_option':

                $ret['request'] = $_REQUEST;

                if (isset($_REQUEST['kindredrest_plugin']) && isset($_REQUEST['mode'])) {
                    $mode = $_REQUEST['mode'];
                    $req_option = $_REQUEST['kindredrest_plugin'];
                    if ($this->checkValid($req_option, $action)) {
                        if ($mode == "activate") {
                            $ret['kindredrest_plugin'] = $req_option;

                            $req_option['mesmo_id'] = 0;
                            $ret_mesmo = $this->get_mesmo_id($req_option);
                            if (isset($ret_mesmo['response'])) {
                                if ($ret_mesmo['response'] == 200) {
                                    $row_code = $ret_mesmo['activation_code'];
                                    $req_option['mesmo_id'] = $row_code['code_id'];
                                    $req_option['mesmo_host'] = $row_code['code_host'];
                                    $req_option['mesmo_code'] = $row_code['code_code'];
                                    $req_option['mesmo_url_cron'] = $row_code['code_url_cron'];
                                    update_option('kindredrest_plugin', $req_option);

                                    update_option('kindredrest_key_valid', true);
                                    
                                    $ret['row_code'] = $row_code;
                                    $ret['response'] = 200;
                                }
                            }
                            if ($ret['response'] != 200) {
                                update_option('kindredrest_key_valid', false);
                            }
                        } else if ($mode == "get_code") {
                            // request new code for this domain
                            $tmp_request = array();
                            $tmp_request['host'] = $_SERVER['HTTP_HOST'];
//                        $tmp_request['url_cron'] = basename($_SERVER['REQUEST_URI']);
                            $tmp_request['action'] = "get_one";
                            $tmp_request['action2'] = $_SERVER;

                            $path_parts = pathinfo($_SERVER['REQUEST_URI']);
                            $tmp_request['url_cron'] = $_SERVER['HTTP_ORIGIN'] . $path_parts['dirname'];

                            $ret['tmp_request'] = $tmp_request;
                            $ret_mesmo = $this->get_one($tmp_request);

                            $req_option['mesmo_id'] = 0;
                            if (isset($ret_mesmo['response'])) {
                                if ($ret_mesmo['response'] == 200) {
                                    $row_code = $ret_mesmo['activation_code'];
                                    $req_option['mesmo_id'] = $row_code['code_id'];
                                    $req_option['mesmo_host'] = $row_code['code_host'];
                                    $req_option['mesmo_code'] = $row_code['code_code'];
                                    $req_option['mesmo_url_cron'] = $row_code['code_url_cron'];
                                    update_option('kindredrest_plugin', $req_option);

                                    update_option('kindredrest_key_valid', true);
                                    $ret['row_code'] = $row_code;
                                    $ret['response'] = 200;
                                }
                            }
                        }
                    }
                }
                break;
            case 'update_cron':
                $ret['request'] = $_REQUEST;
                if (isset($_REQUEST['kindredrest_cronjob'])) {
                    $req_option = $_REQUEST['kindredrest_cronjob'];

                    foreach ($this->crond_values as $key => $value) {
                        if ($key != 'crond_run_time') {
                            if (isset($req_option[$key])) {
                                if ($req_option[$key] == 'on') {
                                    $req_option[$key] = 1;
                                } else {
                                    $req_option[$key] = 0;
                                }
                            }
                        }
                    }

                    $timestamp = wp_next_scheduled('bl_cron_hook');
                    wp_unschedule_event($timestamp, 'bl_cron_hook');

                    $ret['kindredrest_cronjob'] = $req_option;
                    update_option('kindredrest_cronjob', $req_option);
                    $ret['response'] = 200;
                }
                break;
        }
        ob_clean();
        return $ret;
    }

    function get_mesmo_id($input_param) {
        global $g_env;
        if (isset($input_param['mesmo_code'])) {
            $mesmo_code = $input_param['mesmo_code'];

            // request to mesmo server
            if ($g_env == 1) {
                $urlApi = "http://mesmo.co/api_sig/ntest4/general.php?action=get_mesmo_id&mesmo_code=$mesmo_code";
            } else {
                $urlApi = "http://localhost:88/api_sig/ntest4/general.php?action=get_mesmo_id&mesmo_code=$mesmo_code";
            }

            $ch = curl_init($urlApi);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
//            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
//            curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
//            curl_setopt($ch, CURLOPT_USERPWD, $access_token . ":" . "");
            $result = curl_exec($ch);

            return json_decode($result, true);
        }
        return array();
    }

    function get_one($input_param) {
        global $g_env;
        if ($g_env == 1) {
            $fields_string = http_build_query($input_param);
            $urlApi = "http://mesmo.co/api_sig/ntest4/get_key.php?" . $fields_string;
        } else {
//            $urlApi = "http://localhost:88/api_sig/ntest4/general.php?action=get_mesmo_id&mesmo_code=$mesmo_code";
            $fields_string = http_build_query($input_param);
            $urlApi = "http://localhost:88/api_sig/ntest4/get_key.php?" . $fields_string;
        }

        $ch = curl_init($urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
//            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
//        curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
//        curl_setopt($ch, CURLOPT_USERPWD, $access_token . ":" . "");
        $result = curl_exec($ch);

        return json_decode($result, true);
    }

}

?>
