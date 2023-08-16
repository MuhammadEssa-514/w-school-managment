<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if( isset( $_POST['saveoptions'] ) && sanitize_text_field($_POST['saveoptions'])=='Save' ) {
    $remove_data    =   isset( $_POST['remove_data'] ) &&  sanitize_text_field($_POST['remove_data'])== 1 ? 1 : 0;
    update_option( 'wpsp_remove_data', $remove_data );
}
$remove_data_status =   get_option( 'wpsp_remove_data');
?>
<div id="wpbody">
    <div aria-label="Main content" tabindex="0">
        <div class="wrap">
            <h1><?php echo esc_html("WPSchoolpress", "wpschoolpress");?></h1>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder columns-2">
                    <div id="postbox-container-1" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables">
                            <div class="postbox ">
                                <h2 class="hndle"><span><?php _e( 'Settings', 'wpschoolpress'); ?> </span></h2>
                                <div class="inside">
                                    <form name="post" action="" method="post">
                                      <table class="plg-form-table">
                                            <tr class="spaceUnder">
                                            <td class="plg-option"><label for="lcode"><strong><?php echo esc_html("Delete Data", "wpschoolpress");?></strong></label></td>
                                            <td class="plg-value"><input type="checkbox" name="remove_data" id="lcode" value="1" <?php checked( $remove_data_status, 1, true ); ?>>
                                            <i><?php echo esc_html("If you don't want to use the WPSChoolPress Plugin on your site anymore, you can check the delete data box.This makes sure, that all the pages and tables are being deleted from the database when you delete the plugin.", "wpschoolpress");?></i>
                                            <p class="submit" style="margin: 5px 0 15px;">
                                                <input type="submit" name="saveoptions" class="button button-primary" value="Save">
                                                <br class="clear">
                                            </p></td>
                                            </tr>
                                            <tr>
                                                <td class="plg-option"><label for="lcode"><strong><?php echo esc_html("Import", "wpschoolpress");?> </strong></label></td>
                                                <td class="plg-value">
                                                    <button name="wpsp-import-data" id="wpsp-import-data" class="button button-primary"><?php echo esc_html("Import Demo Data", "wpschoolpress");?></button>
                                                    <span class="spinner"></span>
                                                    <br />
                                                    <i style="padding-top: 5px; display: block; font-weight: 500;"><?php echo esc_html("If you want to import demo data, click on Import Demo Data button", "wpschoolpress");?></i>
                                                </td>
                                            </tr>
                                        </table>
                                        <p class="response" style="margin: 0;"></p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
