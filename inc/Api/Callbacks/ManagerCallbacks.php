<?php

/**
 * @package  KindredrestPlugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class ManagerCallbacks extends BaseController {

    public function checkboxSanitize($input) {
        $output = array();

        foreach ($this->managers as $key => $value) {
            $output[$key] = isset($input[$key]) ? true : false;
        }

        return $output;
    }

    public function adminSectionManager() {
        echo 'Api Key and Secret values that you are using.';
    }

    public function checkboxField($args) {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $checkbox = get_option($option_name);
        $checked = isset($checkbox[$name]) ? ($checkbox[$name] ? true : false) : false;

        // var_dump($option_name);
        // var_dump($checkbox);
        // var_dump($checked);

        echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ( $checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
    }

    public function textboxField($args) {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $checkbox = get_option($option_name);
        // var_dump($option_name);
        // var_dump($checkbox);
        $value = '';
        if (isset($checkbox[$name])) {
            $value = $checkbox[$name];
        }

        echo '<div class="' . $classes . '"><input type="text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" class="" ><label for="' . $name . '"><div></div></label></div>';
    }

}
