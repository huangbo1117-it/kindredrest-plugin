<?php
require_once 'common.php';
?>
<div class="wrap">
    <h1>Real Estate Connector - MyDesktop</h1>
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

    $plugin_url = plugin_dir_url(dirname(__FILE__, 1));
//    var_dump($plugin_url);

    $key_valid = get_option('kindredrest_key_valid');
    ?>

    <div>
        <img src="<?php echo plugin_dir_url(dirname(__FILE__, 1)); ?>assets/logo.jpg" alt="Logo"/>
    </div>


    <div>
        <!--<form method="post" id="frm_option" action="options.php">-->
        <?php
//            settings_fields('kindredrest_plugin_settings');
//            do_settings_sections('kindredrest_plugin');
//            submit_button();
//            settings_fields('kindredrest_plugin_settings');
//            do_settings_sections('kindredrest_plugin');
//            submit_button();
        ?>
        <!--</form>-->
        <form method="post" id="frm_option" action="options.php">

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
                                <div class="ui-mtext"><input type="text" id="api_key" name="kindredrest_plugin[api_key]" value="<?php echo $option_key; ?>" class="">
                                    <label for="api_key"><div></div></label>
                                </div>
                            </td>
                        </tr>
                        <tr class="ui-mtext">
                            <th scope="row"><label for="api_secret">API SECRET</label></th>
                            <td>
                                <div class="ui-mtext">
                                    <input type="text" id="api_secret" name="kindredrest_plugin[api_secret]" value="<?php echo $option_secret; ?>" class="">
                                    <label for="api_secret"><div></div></label>
                                </div>
                            </td>
                        </tr>
                        <tr class="ui-mtext">
                            <th scope="row"><label for="mesmo_code">ACTIVATION CODE</label></th>
                            <td>
                                <div class="ui-mtext"><input type="text" id="mesmo_code" name="kindredrest_plugin[mesmo_code]" value="<?php echo $option_code; ?>" class="">
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
                <p class="submit">
                    <!--<label>Activated</label>-->
                    <?php
                    if(!$key_valid){
                        echo '<input type="button" name="button" class="button button-primary option_action option_activate" value="Activate Code">';
                         echo '<a style="margin-left: 20px;" target="_blank" class="button button-primary" href="https://mesmo.co/api_sig/ntest5/index.php" >Buy Code</a>';
//                        echo '<a style="margin-left: 20px;" target="_blank" class="button button-primary" href="http://localhost:88/api_sig/ntest5/index.php" >Buy Code</a>';
                    }else{
                        echo '<label>Activated</label>';
                    }
                    ?>


                </p>
            </div>
            <!--<input type="text" id="mesmo_host" name="mesmo_host" value="" class="">-->
        </form>
    </div>
</div>


<div id="wait" class="loader" style="display:none;position:absolute;top:50%;left:50%;padding:2px;">
<!--    <img src='demo_wait.gif' width="64" height="64" />
    <br>Loading..-->
</div>
