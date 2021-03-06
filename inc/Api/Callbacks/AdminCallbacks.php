<?php

/**
 * @package  RealestateConnectorMydesktop
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class AdminCallbacks extends BaseController {

    public function adminDashboard() {
        return require_once( "$this->plugin_path/templates/admin.php" );
    }

    public function adminSettingCronjob() {
        return require_once( "$this->plugin_path/templates/settingcronjob.php" );
    }

}
