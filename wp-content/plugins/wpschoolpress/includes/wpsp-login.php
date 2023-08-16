<style>
    @import url(https://fonts.googleapis.com/css?family=Roboto:300);
    .login-page {
        width: 360px;
        padding: 5% 0 0;
        margin: auto;
    }
    .form {
        position: relative;
        z-index: 1;
        background: #FFFFFF;
        max-width: 360px;
        margin: 0 auto 10px;
        padding: 25px 35px 35px 35px;
    }
    .form input {
        font-family: "Roboto", sans-serif;
        outline: 0;
        background: #f2f2f2;
        width: 100%;
        border: 0;
        margin: 0 0 15px;
        padding: 12px;
        box-sizing: border-box;
        font-size: 14px;
    }
    .form button {
        font-family: "Roboto", sans-serif;
        text-transform: uppercase;
        outline: 0;
        background: #3c8dbc;
        width: 100%;
        border: 0;
        padding: 15px;
        color: #FFFFFF;
        font-size: 14px;
        cursor: pointer;
    }
    .form button:hover,.form button:active,.form button:focus {
        background: #337ab7;
    }
    .form .message {
        margin: 15px 0 0;
        color: #b3b3b3;
        font-size: 12px;
    }
    .form .message a {
        color: #b3b3b3;
        text-decoration: none;
    }
    .reference{
        font-family: "Roboto", sans-serif;
        text-align:center;
        color: #b3b3b3;
        font-size: 12px;
    }
    .reference a {
        color: #b3b3b3;
        text-decoration: none;
    }
    body {
        background: #ecf0f5; /* fallback for old browsers */
    }
    .logo{
        -webkit-background-size: 84px;
        background-size: 84px;
        background-position: center top;
        background-repeat: no-repeat;
        color: #444;
        height: 84px;
        font-size: 20px;
        line-height: 1.3em;
        margin: 0 auto 15px;
        padding: 0;
        text-decoration: none;
        width: 84px;
        outline: 0;
        display: block;
    }
    .form label{
        text-align: left !important;
        font-weight: normal;
        color:#72777c;
    }
    .left{
        text-align: left;
    }
    .right{
        text-align: right;
    }
</style>
<?php if ( !defined( 'ABSPATH' ) ) exit;
 global $current_user, $wpdb, $wpsp_settings_data,$post,$current_user_name;
 $url = plugins_url( 'img/wpschoolpresslogo.jpg', dirname(__FILE__) );
 $imglogo  = isset( $wpsp_settings_data['sch_logo'] ) ? sanitize_text_field($wpsp_settings_data['sch_logo']) : sanitize_text_field($url);
  ?>
<div class="login-page">
    <div class="logo"><img src="<?php echo esc_url($imglogo) ;?>" height="84" width="84"></div>
    <div class="form">
       <form class="login-form" action="<?php echo site_url(); ?>/wp-login.php" method="post">
            <label><?php _e( 'Username or Email', 'wpschoolpress' );?></label>
            <input type="text" placeholder="username" name="log" id="user_login">
            <label><?php _e( 'Password', 'wpschoolpress' ); ?></label>
            <input type="password" placeholder="password" name="pwd" id="user_pass">
            <button><?php _e( 'login', 'wpschoolpress'); ?></button>
            <p class="message left"><a href="<?php echo esc_url(site_url()); ?>/wp-login.php?action=lostpassword"><?php _e( 'Forgot password?', 'wpschoolpress'); ?></a></p>
            <p class="message right"><a href="<?php echo esc_url(site_url()); ?>/wp-login.php?action=register"><?php _e( "Register", "wpschoolpress" );?></a></p>
        </form>
    </div>
    <p class="reference"><?php _e( 'Powered by', 'wpschoolpress'); ?><a href="http://wpschoolpress.com" title="school management system" target="_blank"><?php echo esc_html("WPSchoolPress","wpschoolpress");?></a></p>
</div>