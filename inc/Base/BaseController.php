<?php

/**
 * @package  RealestateConnectorMydesktop
 */

namespace Inc\Base;

class BaseController {

    public $plugin_path;
    public $plugin_url;
    public $plugin;
    public $setting_values = array();
    public $crond_values = array();

    public function __construct() {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin = plugin_basename(dirname(__FILE__, 3)) . '/kindredrest-plugin.php';

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
    }

    public function activated(string $key) {
        $option = get_option('kindredrest_plugin');

        return isset($option[$key]) ? $option[$key] : false;
    }

    public function setCronTimes() {
        add_filter('cron_schedules', array($this, 'example_add_cron_interval'));
    }

    function example_add_cron_interval($schedules) {
        $schedules['hour_8'] = array(
            'interval' => 8 * 60 * 60,
            'display' => esc_html__('Every 8 Hours'),
        );
        $schedules['hour_16'] = array(
            'interval' => 16 * 60 * 60,
            'display' => esc_html__('Every 16 Hours'),
        );
        $schedules['hour_24'] = array(
            'interval' => 24 * 60 * 60,
            'display' => esc_html__('Every 24 Hours'),
        );

        $schedules['second_5'] = array(
            'interval' => 5,
            'display' => esc_html__('Every 5 seconds'),
        );
        $schedules['second_60'] = array(
            'interval' => 60,
            'display' => esc_html__('Every 5 seconds'),
        );

        return $schedules;
    }

}
