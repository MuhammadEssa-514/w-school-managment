<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
    if( is_user_logged_in() ) {
        global $current_user, $wp_roles, $wpdb;
        //get_currentuserinfo();
            $current_user_role=$current_user->roles[0];
        wpsp_topbar();
        wpsp_sidebar();
        wpsp_body_start();
        if($current_user_role=='administrator' || $current_user_role=='teacher')
        { ?>
            <div class="wpsp-card">
                 <div class="wpsp-card-body">
                                <?php
                                    $importtable=$wpdb->prefix."wpsp_import_history";
                                    $result = $wpdb->get_results("SELECT * FROM $importtable");
                                    $imtype=array('-','Student','Teacher','Parent','Mark');
                                ?>
                                  <table class="wpsp-table" id="import" cellspacing="0" width="100%" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th class="nosort">#</th>
                                        <th><?php esc_html_e( 'Imported Date', 'wpschoolpress' );?></th>
                                        <th><?php esc_html_e( 'Type', 'wpschoolpress' );?></th>
                                        <th><?php esc_html_e( 'Number Of Rows', 'wpschoolpress' );?></th>
                                        <?php if($current_user_role=='administrator'){?>
                                        <th class="nosort"><?php esc_html_e( 'Undo', 'wpschoolpress' );?></th>
<?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                     <?php
                                     $count = 0;
                                     foreach($result as $value){
                                         $count = $count+1;
                                     ?>
                                        <tr>
                                        <td><?php echo esc_html($count); ?></td>
                                        <td><?php echo wpsp_ViewDate(esc_html($value->time)); ?></td>
                                        <td><?php echo esc_html($imtype[$value->type]);?></td>
                                        <td><?php echo esc_html($value->count); ?></td>
<?php if($current_user_role=='administrator'){?>
                                        <td align="center">
                                            <div class="wpsp-action-col">
                                                <a href="javascript:;" class="undoimport" value="<?php echo esc_attr(intval($value->id));?>"><?php esc_html_e( 'Click to undo', 'wpschoolpress' );?></a>
                                            </div>
                                        </td>
                                    <?php } ?>
                                        </tr>
                                     <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="nosort">#</th>
                                            <th><?php esc_html_e( 'Imported Date', 'wpschoolpress' );?></th>
                                            <th><?php esc_html_e( 'Type', 'wpschoolpress' );?></th>
                                            <th><?php esc_html_e( 'Number Of Rows', 'wpschoolpress' );?></th>
<?php if($current_user_role=='administrator'){?>
                                            <th class="nosort"><?php esc_html_e( 'Undo', 'wpschoolpress' );?></th>
<?php } ?>
                                        </tr>
                                    </tfoot>
                                  </table>
                            </div>
                        </div>
     <?php }
        wpsp_body_end();
        wpsp_footer();
    }
    else {
        include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
    }
    ?>
