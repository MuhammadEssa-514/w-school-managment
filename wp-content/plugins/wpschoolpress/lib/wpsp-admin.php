<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($_POST['lcode'])&& check_admin_referer('save_lcode', 'lcode_nonce' )){
    update_option('wpsp-lcode',sanitize_text_field($_POST['lcode']),'','no');
}?>
<div id="wpbody">
    <div aria-label="Main content" tabindex="0">
        <div class="wrap">
            <h1><?php echo esc_html("WPSchoolpress", "wpschoolpress");?></h1>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div id="postbox-container-1" class="postbox-container">
                        <div id="normal-sortables" class="meta-box-sortables">
                            <div class="postbox ">
                                <h2 class="hndle"><span><?php _e( 'Advanced Plan', 'wpschoolpress'); ?> </span></h2>
                                <div class="inside">
                                    <form name="post" action="" method="post" >
                                        <div class="input-text-wrap">
                                            <label for="lcode"><?php echo esc_html("License Code", "wpschoolpress");?></label>
                                            <input type="text" name="lcode" id="lcode" value="<?php echo esc_attr(get_option('wpsp-lcode')); ?>" autocomplete="off">
                                            <?php wp_nonce_field( 'save_lcode', 'lcode_nonce' ); ?>
                                        </div>
                                        <br/>
                                        <p class="submit">
                                            <input type="submit" name="save" class="button button-primary" value="Save">
                                            <br class="clear">
                                        </p>
                                    </form>
                                    <p><?php _e( 'Subscribers can update new features right from their plugin dashboard. Kindly, make sure that you have license code above. If not contact us.', 'wpschoolpress'); ?> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-2" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortable">
                            <div id="dashboard_quick_press" class="postbox ">
                               <h2 class="hndle"><span><?php echo esc_html("Contact Us", "wpschoolpress");?></span></h2>
                                <div class="inside">
                                    <form name="post" action="javascript:;" method="post" id="contactForm">
                                         <div class="textarea-wrap">
                                            <label for="message"><?php _e( 'Name', 'wpschoolpress'); ?> </label>
                                            <input type="text" name="inputName" id="inputName">
                                        </div><br>
                                        <div class="textarea-wrap">
                                            <label for="message"><?php _e( 'Email', 'wpschoolpress'); ?> </label>
                                            <input type="email" name="inputEmail" id="inputEmail">
                                        </div><br>
                                        <div class="textarea-wrap">
                                            <label for="message"><?php _e( 'Message', 'wpschoolpress'); ?> </label>
                                            <textarea name="message" id="inputMessage"  rows="6" cols="15" autocomplete="off"></textarea>
                                        </div>
                                        <br/>
                                        <p class="submit">
                                            <input type="hidden" name="wpsp_referrer" value="<?php echo site_url();?>">
                                            <input type="submit" name="save" id="save-post" class="button button-primary" value="Send">
                                            <br class="clear">
                                        </p>
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
