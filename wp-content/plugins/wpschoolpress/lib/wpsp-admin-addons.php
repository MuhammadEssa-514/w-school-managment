<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="wpbody">
    <div aria-label="Main content" tabindex="0" class="addon-page">
        <div class="wrap">
            <h1><?php esc_html_e("WPSchoolpress Add-Ons", "wpschoolpress");?></h1>
            <div class="container">
                <div class="wpsp-row">
                  <?php  $member_detail = wp_remote_get('https://wpschoolpress.com/wp-json/api/v1/addons/');
                        $team_details = wp_json_decode(wp_remote_retrieve_body($member_detail));
                        echo esc_html($team_details->message);
                  ?>
                </div>
            </div><!-- dashboard-widgets-wrap -->
        </div><!-- wrap -->
    </div><!-- wpbody-content -->
</div>