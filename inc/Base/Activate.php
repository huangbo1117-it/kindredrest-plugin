<?php

/**
 * @package  RealestateConnectorMydesktop
 */

namespace Inc\Base;

class Activate {

    public static function activate() {
        flush_rewrite_rules();

        $default = array();

        if (!get_option('kindredrest_plugin')) {
            update_option('kindredrest_plugin', $default);
        }

        // if ( ! get_option( 'kindredrest_plugin_key' ) ) {
        // 	update_option( 'kindredrest_plugin_key', $default );
        // }
        //
		// if ( ! get_option( 'kindredrest_plugin_secret' ) ) {
        // 	update_option( 'kindredrest_plugin_secret', $default );
        // }
        //
		// if ( ! get_option( 'kindredrest_plugin_code' ) ) {
        // 	update_option( 'kindredrest_plugin_code', $default );
        // }

        if (!get_option('kindredrest_key_valid')) {
            update_option('kindredrest_key_valid', false);
        }

        if (!get_option('kindredrest_cronjob')) {

            update_option('kindredrest_cronjob', array("crond_run_time" => "8"));
        }

        Activate::install_db();
    }

    public static function install_db() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		text text NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $sql = "CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `prop_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `bedrooms` varchar(10) NOT NULL,
  `bathrooms` varchar(10) NOT NULL,
  `toilets` varchar(10) NOT NULL,
  `garages` varchar(10) NOT NULL,
  `landarea` varchar(255) NOT NULL,
  `buildingarea` varchar(255) NOT NULL,
  `commercialsaletype` varchar(255) NOT NULL,
  `carports` varchar(10) NOT NULL,
  `propimage` text NOT NULL,
  `displayaddress` text NOT NULL,
  `address` text NOT NULL,
  `lat` varchar(255) NOT NULL,
  `lng` varchar(255) NOT NULL,
  `pricetext` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `agentid` varchar(255) NOT NULL,
  `agentname` text NOT NULL,
  `agentemail` varchar(255) NOT NULL,
  `agentimage` text NOT NULL,
  `agentmobile` varchar(255) NOT NULL,
  `agentmobiledisplay` varchar(255) NOT NULL,
  `agentfax` varchar(255) NOT NULL,
  `agenttel` varchar(255) NOT NULL,
  `last_modified` text NOT NULL,
  `listingtype` varchar(255) NOT NULL,
  `listingdate` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) $charset_collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE `property_ids` (
  `id` int(11) NOT NULL,
  `property_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) $charset_collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE `property_ids_test` (
  `id` int(11) NOT NULL,
  `property_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) $charset_collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE `property_types` (
  `id` int(11) NOT NULL,
  `types` varchar(255) NOT NULL
) $charset_collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE `prop_not_inserted` (
  `id` int(11) NOT NULL,
  `prop_id` varchar(255) NOT NULL
) $charset_collate;";
        dbDelta($sql);

        $sql = "--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_ids`
--
ALTER TABLE `property_ids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_ids_test`
--
ALTER TABLE `property_ids_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_types`
--
ALTER TABLE `property_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prop_not_inserted`
--
ALTER TABLE `prop_not_inserted`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8290;

--
-- AUTO_INCREMENT for table `property_ids`
--
ALTER TABLE `property_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_ids_test`
--
ALTER TABLE `property_ids_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_types`
--
ALTER TABLE `property_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prop_not_inserted`
--
ALTER TABLE `prop_not_inserted`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;";
        dbDelta($sql);
    }

    public static function jal_install_data() {
        global $wpdb;

        $welcome_name = 'Mr. WordPress';
        $welcome_text = 'Congratulations, you just completed the installation!';

        $table_name = $wpdb->prefix . 'liveshoutbox';

        $wpdb->insert(
                $table_name, array(
            'time' => current_time('mysql'),
            'name' => $welcome_name,
            'text' => $welcome_text,
                )
        );
    }

}
