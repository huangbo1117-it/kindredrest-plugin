<?php

/**
 * @package  KindredrestPlugin
 */

namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\ManagerCallbacks;

class Dashboard extends BaseController {

    public $settings;
    public $callbacks;
    public $callbacks_mngr;
    public $pages = array();

    public function register() {
        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->callbacks_mngr = new ManagerCallbacks();

        $this->setPages();

        $this->setSettings();
        $this->setSections();
        $this->setFields();

        $this->settings->addPages($this->pages)->withSubPage('Dashboard')->register();
        $this->test1();
    }

    public function setPages() {
        $this->pages = array(
            array(
                'page_title' => 'Kindredrest Plugin',
                'menu_title' => 'Kindredrest',
                'capability' => 'manage_options',
                'menu_slug' => 'kindredrest_plugin',
                'callback' => array($this->callbacks, 'adminDashboard'),
                'icon_url' => 'dashicons-store',
                'position' => 110
            )
        );
    }

    public function setSettings() {
        $args = array(
            array(
                'option_group' => 'kindredrest_plugin_settings',
                'option_name' => 'kindredrest_plugin',
                'callback' => '',
            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections() {
        $args = array(
            array(
                'id' => 'kindredrest_admin_index',
                'title' => 'Settings Manager',
                'callback' => array($this->callbacks_mngr, 'adminSectionManager'),
                'page' => 'kindredrest_plugin'
            )
        );

        $this->settings->setSections($args);
    }

    public function setFields() {
        $args = array();

        foreach ($this->setting_values as $key => $value) {
            if ($key == 'key_isvalid') {
                // $args[] = array(
                // 	'id' => $key,
                // 	'title' => $value,
                // 	'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
                // 	'page' => 'kindredrest_plugin',
                // 	'section' => 'kindredrest_admin_index',
                // 	'args' => array(
                // 		'option_name' => 'kindredrest_plugin',
                // 		'label_for' => $key,
                // 		'class' => 'ui-toggle'
                // 	)
                // );
            } else {
                $args[] = array(
                    'id' => $key,
                    'title' => $value,
                    'callback' => array($this->callbacks_mngr, 'textboxField'),
                    'page' => 'kindredrest_plugin',
                    'section' => 'kindredrest_admin_index',
                    'args' => array(
                        'option_name' => 'kindredrest_plugin',
                        'label_for' => $key,
                        'class' => 'ui-mtext'
                    )
                );
            }
        }

        $this->settings->setFields($args);
    }

    public function test1() {


        if (function_exists("register_field_group")) {
            register_field_group(array(
                'id' => 'acf_agent-details',
                'title' => 'Agent Details',
                'fields' => array(
                    array(
                        'key' => 'field_596e0f3995a4d',
                        'label' => 'ID',
                        'name' => 'agent_id',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596e0f7795a4f',
                        'label' => 'Email',
                        'name' => 'agent_email',
                        'type' => 'email',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_596e0f8495a50',
                        'label' => 'Mobile',
                        'name' => 'agent_mobile',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596e0fa395a51',
                        'label' => 'Mobile Display',
                        'name' => 'agent_mobile_display',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596e0fb495a52',
                        'label' => 'Telephone',
                        'name' => 'agent_telephone',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596e0fbe95a53',
                        'label' => 'Fax',
                        'name' => 'agent_fax',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596e0fcb95a54',
                        'label' => 'Profile Image',
                        'name' => 'agent_profile_image',
                        'type' => 'image',
                        'save_format' => 'object',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                    ),
                    array(
                        'key' => 'field_5a107294d5d3f',
                        'label' => 'Profile Image',
                        'name' => 'profile_image_url',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'ef_taxonomy',
                            'operator' => '==',
                            'value' => 'property-agents',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),
                'options' => array(
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => array(
                    ),
                ),
                'menu_order' => 0,
            ));
            register_field_group(array(
                'id' => 'acf_property-details',
                'title' => 'Property Details',
                'fields' => array(
                    array(
                        'key' => 'field_5a10a912237c8',
                        'label' => 'Featured Image',
                        'name' => 'featured_image',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5a0f32fc8e6df',
                        'label' => 'Gallert Images',
                        'name' => 'gallert_images_links',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_5a0f33238e6e0',
                                'label' => 'Image URL',
                                'name' => 'image_url',
                                'type' => 'text',
                                'column_width' => '',
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'formatting' => 'html',
                                'maxlength' => '',
                            ),
                        ),
                        'row_min' => '',
                        'row_limit' => '',
                        'layout' => 'table',
                        'button_label' => 'Add Row',
                    ),
                    array(
                        'key' => 'field_596c97539dc31',
                        'label' => 'Gallery Images',
                        'name' => 'gallery_images1',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_596c9a6a0f1a9',
                                'label' => 'Property Images',
                                'name' => 'property_images',
                                'type' => 'image',
                                'column_width' => '',
                                'save_format' => 'object',
                                'preview_size' => 'thumbnail',
                                'library' => 'all',
                            ),
                        ),
                        'row_min' => '',
                        'row_limit' => '',
                        'layout' => 'table',
                        'button_label' => 'Add Row',
                    ),
                    array(
                        'key' => 'field_596ca5e296c56',
                        'label' => 'Address',
                        'name' => 'address_map',
                        'type' => 'google_map',
                        'center_lat' => '',
                        'center_lng' => '',
                        'zoom' => '',
                        'height' => '',
                    ),
                    array(
                        'key' => 'field_596e04e931954',
                        'label' => 'Display Address',
                        'name' => 'display_address',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596e065b35366',
                        'label' => 'Price',
                        'name' => 'price',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596f73408a158',
                        'label' => 'BedRoom',
                        'name' => 'bedrooms',
                        'type' => 'number',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_596f73578a159',
                        'label' => 'Bathroom',
                        'name' => 'bathrooms',
                        'type' => 'number',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_596f73888a15a',
                        'label' => 'Toilets',
                        'name' => 'toilets',
                        'type' => 'number',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_596f738e8a15b',
                        'label' => 'Garages',
                        'name' => 'garages',
                        'type' => 'number',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_59e156be9cc99',
                        'label' => 'Carports',
                        'name' => 'carports',
                        'type' => 'number',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
                    ),
                    array(
                        'key' => 'field_596fce3e4b2c3',
                        'label' => 'Landarea',
                        'name' => 'landarea',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596fce494b2c4',
                        'label' => 'Building Area',
                        'name' => 'building_area',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_597290bb17c5b',
                        'label' => 'Auction Date',
                        'name' => 'auction_date',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => 'Saturday 24 June at 1.30pm',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_597290d717c5c',
                        'label' => 'Auction Venue',
                        'name' => 'auction_venue',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_597293742ee09',
                        'label' => 'Floor Plans',
                        'name' => 'floor_plans',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_597293822ee0a',
                                'label' => 'Floorplan',
                                'name' => 'floorplan',
                                'type' => 'text',
                                'column_width' => '',
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'formatting' => 'html',
                                'maxlength' => '',
                            ),
                        ),
                        'row_min' => '',
                        'row_limit' => '',
                        'layout' => 'table',
                        'button_label' => 'Add Row',
                    ),
                    array(
                        'key' => 'field_5975b8d044b53',
                        'label' => 'Video Url',
                        'name' => 'video_url',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5975bda96996a',
                        'label' => 'Water Rates',
                        'name' => 'water_rates',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5975bdb66996b',
                        'label' => 'Council Rates',
                        'name' => 'council_rates',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5975bdbf6996c',
                        'label' => 'Strata Levies',
                        'name' => 'strata_levies',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_596e139998e18',
                        'label' => 'Files',
                        'name' => 'files',
                        'type' => 'repeater',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_596e13a498e19',
                                'label' => 'Property Files',
                                'name' => 'property_files',
                                'type' => 'text',
                                'column_width' => '',
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'formatting' => 'html',
                                'maxlength' => '',
                            ),
                        ),
                        'row_min' => '',
                        'row_limit' => '',
                        'layout' => 'table',
                        'button_label' => 'Add Row',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'property',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),
                'options' => array(
                    'position' => 'normal',
                    'layout' => 'default',
                    'hide_on_screen' => array(
                    ),
                ),
                'menu_order' => 0,
            ));
            // var_dump("exist function");
        } else {
            // var_dump("non exist function");
        }
    }

}
