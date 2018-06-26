<?php

/**
 * @package  KindredrestPlugin
 */

namespace Inc\Base;

class Deactivate {

    public static function deactivate() {
        $timestamp = wp_next_scheduled('bl_cron_hook');
        wp_unschedule_event($timestamp, 'bl_cron_hook');

        flush_rewrite_rules();

        $default = array();
        update_option('kindredrest_key_valid', false);
        update_option('kindredrest_cronjob', $default);
    }

}
