<?php

/**
 * @package  RealestateConnectorMydesktop
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class CronCallbacks extends BaseController {

    public function checkboxSanitize($input) {
        $output = array();

        foreach ($this->crond_values as $key => $value) {
            if ($key == 'crond_run_time') {
                $output[$key] = $input[$key];
            } else {
                $output[$key] = isset($input[$key]) ? true : false;
            }
        }

        return $output;
    }

    public function cronSectionManager() {
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
        $p = '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ( $checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
        // var_dump($p);
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

    public function optionboxField($args) {
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
        //var_dump($value);

        $options = array(8, 16, 24);
        $p = '<div class="' . $classes . '">';
        $p = $p . '<select name="' . $option_name . '[' . $name . ']" >';
        foreach ($options as $key => $val) {
            if ($value == $val) {
                $p = $p . "<option value=$val selected>$val hrs</option>";
            } else {
                $p = $p . "<option value=$val >$val hrs</option>";
            }
        }
        $p = $p . '</select>';
        $p = $p . '</div>';
        echo $p;
    }

}
