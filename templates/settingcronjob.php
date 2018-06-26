<?php
require_once 'common.php';
?>
<div class="wrap">
    <h1>Cronjob Setting</h1>
    <?php
    settings_errors();
//    echo '<pre>';
//    print_r(_get_cron_array());
//    echo '</pre>';
    $option = get_option('kindredrest_plugin');
//    var_dump($option);
    $option_key = isset($option['api_key']) ? ($option['api_key']) : "";
    $option_secret = isset($option['api_secret']) ? ($option['api_secret']) : "";
    $option_code = isset($option['mesmo_code']) ? ($option['mesmo_code']) : "";


    $option = get_option('kindredrest_cronjob');
//    var_dump($option);

    $option_cm_list = isset($option['crond_cm_list']) ? $option['crond_cm_list'] : false;
    $option_resd_list = isset($option['crond_resd_list']) ? $option['crond_resd_list'] : false;
    $option_show_img = isset($option['crond_show_imglocally']) ? $option['crond_show_imglocally'] : false;
    $option_run_time = isset($option['crond_run_time']) ? $option['crond_run_time'] : "";
    $option_cron_valid = isset($option['crond_is_valid']) ? $option['crond_is_valid'] : false;

    $option_cm_list = array($option_cm_list ? 1 : 0, $option_cm_list ? "checked" : "");
    $option_resd_list = array($option_resd_list ? 1 : 0, $option_resd_list ? "checked" : "");
    $option_show_img = array($option_show_img ? 1 : 0, $option_show_img ? "checked" : "");

    if ($option_cron_valid) {
        echo "<h2>Status:  Cron job Is Running Now</h2>";
    } else {
        echo "<h2>Status:  No Cron Job Now</h2>";
    }
    ?>

    <div>

        <form id="frmCron" method="post" action="options.php">
            <!--<input type="hidden" name="option_page" value="kindredrest_plugin_cronjob">-->
            <input type="hidden" name="action" value="update_cron">
            <input type="hidden" id="crond_is_valid" name="kindredrest_cronjob[crond_is_valid]" value="">
            <!--<input type="hidden" id="_wpnonce" name="_wpnonce" value="9c2c877e21">-->
            <!--<input type="hidden" name="_wp_http_referer" value="/wordpress/wp-admin/admin.php?page=kindredrest_cronjob">-->
            Api Key and Secret values that you are using.
            <table class="form-table"><tbody>
                    <tr class="ui-toggle">
                        <th scope="row"><label for="crond_cm_list">Show Commercial listings</label>
                        </th>
                        <td>
                            <div class="ui-toggle"><input type="checkbox" id="crond_cm_list" name="kindredrest_cronjob[crond_cm_list]"  class="" <?php echo $option_cm_list[1]; ?>>
                                <label for="crond_cm_list"><div></div></label>

                            </div>
                        </td>
                    </tr>
                    <tr class="ui-toggle">
                        <th scope="row">
                            <label for="crond_resd_list">Show residential listings
                            </label>
                        </th>
                        <td><div class="ui-toggle">
                                <input type="checkbox" id="crond_resd_list" name="kindredrest_cronjob[crond_resd_list]" class="" <?php echo $option_resd_list[1]; ?>>
                                <label for="crond_resd_list">
                                    <div>

                                    </div>
                                </label>

                            </div>
                        </td>
                    </tr>
                    <tr class="ui-toggle"><th scope="row">
                            <label for="crond_show_imglocally">Show Images locally</label></th><td>
                            <div class="ui-toggle">
                                <input type="checkbox" id="crond_show_imglocally" name="kindredrest_cronjob[crond_show_imglocally]" class="" <?php echo $option_show_img[1]; ?>>
                                <label for="crond_show_imglocally"><div></div></label></div>
                        </td></tr>
                    <tr class="ui-toggle">
                        <th scope="row"><label for="crond_run_time">Cron run time</label>
                        </th>
                        <td>
                            <div class="ui-toggle"><select name="kindredrest_cronjob[crond_run_time]">
                                    <?php
                                    $list_hr = array(8, 16, 24);
                                    foreach ($list_hr as $hr) {
                                        if ($hr == $option_run_time) {
                                            echo "<option value='$hr' selected>$hr hrs</option>";
                                        } else {
                                            echo "<option value='$hr'>$hr hrs</option>";
                                        }
                                    }
                                    ?>
                                    <!--<option value="8">8 hrs</option>-->
                                    <!--<option value="16" selected="">16 hrs</option>-->
                                    <!--<option value="24">24 hrs</option>-->
                                </select>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <!--<div class="loader"></div>-->
                <input type="button" class="button button-primary cron_action cron_start" value="Start Cron">
                <input type="button" class="button button-primary cron_action cron_stop" value="Stop Cron">

            </p>        
        </form>
    </div>


    <div>
        <div method="post" action="options.php">
                <!--<input type="hidden" name="option_page" value="kindredrest_plugin_settings">-->
            <input type="hidden" name="action" value="update_option">
            <input type="hidden" id="mode" name="mode" value="">
            <!--<input type="hidden" id="_wpnonce" name="_wpnonce" value="b4d9bac327">-->
            <!--<input type="hidden" name="_wp_http_referer" value="/wordpress/wp-admin/admin.php?page=kindredrest_plugin">-->
            <h2>Settings Manager</h2>
            Api Key and Secret values that you are using.
            <table class="form-table">
                <tbody>
                    <tr class="ui-mtext">
                        <th scope="row">
                            <label for="api_key">Api Key</label>
                        </th>
                        <td>
                            <div class="ui-mtext"><input type="text" id="api_key" name="kindredrest_plugin[api_key]" value="<?php echo $option_key; ?>" class="" disabled>
                                <label for="api_key"><div></div></label>
                            </div>
                        </td>
                    </tr>
                    <tr class="ui-mtext">
                        <th scope="row"><label for="api_secret">API SECRET</label></th>
                        <td>
                            <div class="ui-mtext">
                                <input type="text" id="api_secret" name="kindredrest_plugin[api_secret]" value="<?php echo $option_secret; ?>" class="" disabled>
                                <label for="api_secret"><div></div></label>        
                            </div>
                        </td>
                    </tr>
                    <tr class="ui-mtext">
                        <th scope="row"><label for="mesmo_code">ACTIVATION CODE</label></th>
                        <td>
                            <div class="ui-mtext"><input type="text" id="mesmo_code" name="kindredrest_plugin[mesmo_code]" value="<?php echo $option_code; ?>" class="" disabled>
                                <label for="mesmo_code"><div></div></label>
                            </div>
                        </td>
                    </tr>
<!--                        <tr class="ui-mtext">
                        <th scope="row"><label for="mesmo_id">Activation Code Row ID</label></th>
                        <td>
                            <div class="ui-mtext"><input type="text" id="mesmo_id" name="kindredrest_plugin[mesmo_id]" value="2" class=""><label for="mesmo_id"><div></div></label></div>
                        </td>
                    </tr>-->
                </tbody>
            </table>     
        </div>
    </div>
</div>

<!--width:69px;height:89px;border:1px solid black-->
<div id="wait" class="loader" style="display:none;position:absolute;top:50%;left:50%;padding:2px;">
<!--    <img src='demo_wait.gif' width="64" height="64" />
    <br>Loading..-->
</div>