<?php

/**
 * @package  KindredrestPlugin
 */

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\CronCallbacks;
use PhilipBrown\Signature\Token;
use PhilipBrown\Signature\Request;

/**
 *
 */
class SettingCronjob extends BaseController {

    public $callbacks;
    public $subpages = array();
    public $callbacks_mngr;
    public $settings;

    public function activated(string $key) {
        $option = get_option('kindredrest_key_valid');
        if (isset($option)) {
            return $option ? true : false;
        }
        return false;
    }

    public function register() {
        if (!$this->activated('kindredrest_key_valid'))
            return;

        $data = array("key" => 'keykey', "time" => date("D M d, Y G:i:s"));
        $filename = plugin_dir_path(dirname(__FILE__, 1)) . 'test_file';
        $fp = fopen($filename, 'w');
        if ($fp) {
            fwrite($fp, serialize($data));
            fclose($fp);
//            var_dump($filename);
//            echo $filename;
        } else {
            var_dump("unable to open file $filename");
        }


        $this->setCronTimes();

        add_action('bl_cron_hook', array($this, 'bl_cron_exec'));
        $option = get_option('kindredrest_cronjob');
        if (isset($option['crond_is_valid']) && $option['crond_is_valid'] == 1) {
            if (!wp_next_scheduled('bl_cron_hook')) {
                $hr = $option['crond_run_time'];
                $cron_name = "hour_$hr";

                wp_schedule_event(time(), $cron_name, 'bl_cron_hook');
//                wp_schedule_event(time(), "second_60", 'bl_cron_hook');
//                var_dump("schedule cron $cron_name");
            } else {
//                var_dump("already scheduled");
            }
        } else {
            if (wp_next_scheduled('bl_cron_hook')) {
                $timestamp = wp_next_scheduled('bl_cron_hook');
                wp_unschedule_event($timestamp, 'bl_cron_hook');
//                var_dump("delete scheduled cron");
            }
        }

        // var_dump("SettingCronjob");
        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->callbacks_mngr = new CronCallbacks();

        $this->setSubpages();

        $this->setSettings();
        $this->setSections();
        $this->setFields();

        $this->settings->addSubPages($this->subpages)->register();
    }

    public function setSubpages() {
        $this->subpages = array(
            array(
                'parent_slug' => 'kindredrest_plugin',
                'page_title' => 'Cron Job Setting',
                'menu_title' => 'Cron Job Setting',
                'capability' => 'manage_options',
                'menu_slug' => 'kindredrest_cronjob',
                'callback' => array($this->callbacks, 'adminSettingCronjob')
            )
        );
    }

    public function setSettings() {
        $args = array(
            array(
                'option_group' => 'kindredrest_plugin_cronjob',
                'option_name' => 'kindredrest_cronjob',
                'callback' => array($this->callbacks_mngr, 'checkboxSanitize')
            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections() {
        $args = array(
            array(
                'id' => 'kindredrest_cron_index',
                'title' => 'Cronjob Setting',
                'callback' => array($this->callbacks_mngr, 'cronSectionManager'),
                'page' => 'kindredrest_cronjob'
            )
        );

        $this->settings->setSections($args);
    }

    public function setFields() {
        $args = array();

        foreach ($this->crond_values as $key => $value) {
            if ($key == 'crond_run_time') {
                $args[] = array(
                    'id' => $key,
                    'title' => $value,
                    'callback' => array($this->callbacks_mngr, 'optionboxField'),
                    'page' => 'kindredrest_cronjob',
                    'section' => 'kindredrest_cron_index',
                    'args' => array(
                        'option_name' => 'kindredrest_cronjob',
                        'label_for' => $key,
                        'class' => 'ui-toggle'
                    )
                );
            } else {
                $args[] = array(
                    'id' => $key,
                    'title' => $value,
                    'callback' => array($this->callbacks_mngr, 'checkboxField'),
                    'page' => 'kindredrest_cronjob',
                    'section' => 'kindredrest_cron_index',
                    'args' => array(
                        'option_name' => 'kindredrest_cronjob',
                        'label_for' => $key,
                        'class' => 'ui-toggle'
                    )
                );
            }
        }

        // var_dump("xxx");
        $this->settings->setFields($args);
    }

    public function bl_cron_exec() {
        //var_dump("bl_cron_exec every 5 seconds");
        // $data = array("key"=>'keykey',"time"=>time());
        // $fp = 		fopen($filename, 'w');
        // fwrite($fp, serialize($data));
        // fclose($fp);
        // var_dump($filename);
        // echo $filename;

        date_default_timezone_set("UTC");

        $filename = plugin_dir_path(dirname(__FILE__, 1)) . 'cron_file';
        $txt = date("D M d, Y G:i:s");
        $myfile = file_put_contents($filename, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);

        $op_plugin = get_option('kindredrest_plugin');
        $op_cron = get_option('kindredrest_cronjob');
        if ($op_cron['crond_is_valid'] == 1) {
            $myfile = file_put_contents($filename, 'Cron Start' . PHP_EOL, FILE_APPEND | LOCK_EX);

            $option_key = isset($op_plugin['api_key']) ? ($op_plugin['api_key']) : "";
            $option_secret = isset($op_plugin['api_secret']) ? ($op_plugin['api_secret']) : "";
            $option_code = isset($op_plugin['mesmo_code']) ? ($op_plugin['mesmo_code']) : "";
            $option_id = isset($op_plugin['mesmo_id']) ? ($op_plugin['mesmo_id']) : "";
            $option_host = isset($op_plugin['mesmo_host']) ? ($op_plugin['mesmo_host']) : "";

            $option_cm_list = isset($op_cron['crond_cm_list']) ? $op_cron['crond_cm_list'] : false;
            $option_resd_list = isset($op_cron['crond_resd_list']) ? $op_cron['crond_resd_list'] : false;
            $option_show_img = isset($op_cron['crond_show_imglocally']) ? $op_cron['crond_show_imglocally'] : false;
            $option_run_time = isset($op_cron['crond_run_time']) ? $op_cron['crond_run_time'] : "";

            // generate key

            $host = $option_host;
            $secret = $option_code;
            $auth_data = ['host' => $host, 'code_id' => $option_id];


            $key = $host;
            $secret = $secret;

            $token = new Token($key, $secret);
            $request = new Request('POST', 'users', $auth_data);

            $auth = $request->sign($token);

            // var_dump($auth);
            // unset($auth['auth_key']);

            $request_data = array(
                'key' => $option_key,
                'token' => $option_secret,
                'action' => 'script1',
                'cron_setting' => $op_cron);
            $request_data['auth_data'] = array_merge($auth, $auth_data);


            // var_dump($request_data);

            $fields_string = http_build_query($request_data);



            $url = "http://mesmo.co/api_sig/ntest4/test_kindred.php?" . $fields_string;

            $myfile = file_put_contents($filename, $url . PHP_EOL, FILE_APPEND | LOCK_EX);
//            var_dump($url);
            $ch = curl_init();

//set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//execute post
            $result = curl_exec($ch);
// echo $result;
//close connection
            curl_close($ch);
//
// var_dump($fields_string);
//            var_dump($result);
        }
    }

}
