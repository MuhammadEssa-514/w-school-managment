<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
    if( is_user_logged_in() ) {
        global $current_user, $wpdb;
        $current_user_role=$current_user->roles[0];
            if( $current_user_role=='administrator' || $current_user_role=='teacher')
            {
            wpsp_topbar();
            wpsp_sidebar();
            wpsp_body_start();
            $id = sanitize_text_field(stripslashes($_GET['id']));
            $user_id = esc_sql(base64_decode($id));
            ?>
        <div class="wpsp-row">
                    <div class="wpsp-col-md-12">
                        <div class="wpsp-card">
                        <div class="wpsp-card-head ui-sortable-handle">
                            <h3 class="wpsp-card-title"><?php esc_html_e( 'Payment Details', 'wpschoolpress' ); ?></h3>
                        </div>
                            <div class="wpsp-card-body">

                                <table id="class_table" class="wpsp-table wpsp-table-bordered wpsp-table-striped" cellspacing="0" width="100%" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Student Name', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Roll No', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Class Name', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Class Teacher Name', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Amount', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Date', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Class Fee Status', 'wpschoolpress' ); ?></th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                <?php
$classid = array();
$productid = array();
$stable=$wpdb->prefix."wpsp_student";
$ctlass=$wpdb->prefix."wpsp_class";
$cteacher =$wpdb->prefix."wpsp_teacher";
$wpsp_stud_data =$wpdb->get_results("SELECT * FROM  $stable s
INNER JOIN $ctlass c
where s.wp_usr_id = '".$user_id."'");
$classid_da = maybe_unserialize($wpsp_stud_data[0]->class_id);
$courses1 = get_user_meta($wpsp_stud_data[0]->parent_wp_usr_id, '_pay_woocommerce_enrolled_class_access_counter' );
$courses2 = get_user_meta( $user_id, '_pay_woocommerce_enrolled_class_access_counter');
$courses = array_merge($courses1,$courses2);

if ( ! empty( $courses ) ) {
      $courses = maybe_unserialize( $courses );
    } else {
      $courses = array();
    }

$courses_check=array();
$courses_value=array();
$courses_value1=array();
foreach($courses as $key => $value){
    foreach($value as $key1 => $value1){
                    $courses_check[] = $key1;
                        $courses_value[] = $value1;
                }

    $pr++;
}
foreach($courses_value as   $valueorder){
    $courses_value1[] = $valueorder[0];
}
$wpsp_clas_data =$wpdb->get_results("SELECT * FROM  $ctlass
 where cid IN('".implode("','",$classid_da)."') and c_fee_type = 'paid'");

$proid = 0;

$wpsp_fees_data = $wpdb->get_results("SELECT s.wp_usr_id,f.student_id,f.order_id FROM wp_wpsp_student AS s INNER JOIN wp_wpsp_fees AS f ON f.student_id =  s.wp_usr_id");


foreach($wpsp_fees_data as $res){
$siid[] = $res->wp_usr_id;
}

foreach($wpsp_clas_data as $clsloop){

        $wpsp_teacher_data =$wpdb->get_results("SELECT * FROM  $cteacher
        where wp_usr_id = '".esc_sql($clsloop->teacher_id)."'");

        $wpsp_order_data =$wpdb->get_results("SELECT c.cid,f.class_id,f.fees_amount,f.created_date FROM  wp_wpsp_class as c INNER JOIN wp_wpsp_fees AS f ON f.class_id = c.cid where f.class_id = '".esc_sql($clsloop->cid)."'");
        $lastKey = key(array_slice($wpsp_order_data, -1, 1, true));


        if(in_array($wpsp_stud_data[0]->wp_usr_id, $siid)){
            $paid = esc_html('Paid','wpschoolpress');
        }else{
            $paid = esc_html('Not Paid','wpschoolpress');
        }
        if(in_array($wpsp_stud_data[0]->wp_usr_id, $siid)){
                        //   echo"<pre>";print_r($courses_value1);
                        //   $order = new WC_Order($courses_value1[$proid]);
                        //   $order_data = $order->get_data();
                          $order_date_created = $wpsp_order_data[$lastKey]->created_date;
                          $newDate = date("F d, Y", strtotime($order_date_created));
                          $price = $wpsp_order_data[$lastKey]->fees_amount.'.00';
                        //   $proid++;
                }

?>
                                    <tr>
                                        <td><?php echo  esc_html($wpsp_stud_data[0]->s_fname.' '.$wpsp_stud_data[0]->s_lname);?></td>
                                        <td><?php echo  esc_html($wpsp_stud_data[0]->s_rollno);?></td>
                                        <td> <?php echo  esc_html($clsloop->c_name);?></td>
                                        <td> <?php echo  esc_html($wpsp_teacher_data[0]->first_name.' '. $wpsp_teacher_data[0]->last_name) ;?></td>
                                        <td><?php if($price != ''){ echo get_woocommerce_currency_symbol() .$price; }else { echo '-'; }?></td>
                                        <td><?php if($newDate != ''){ echo esc_html($newDate); }else { echo '-'; }?></td>
                                        <td><?php echo esc_html($paid);?></td>

                                    </tr>
                                    <?php
                                // $pr++;
                                 }?>
                                </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>

        <?php

            wpsp_body_end();
            wpsp_footer();
        }
        elseif($current_user_role=='parent' || $current_user_role='student')
        {
            wpsp_topbar();
            wpsp_sidebar();
            wpsp_body_start();
            ?>

                <div class="wpsp-row">
                    <div class="wpsp-col-md-12">
                        <div class="wpsp-card">
                        <div class="wpsp-card-head ui-sortable-handle">
                            <h3 class="wpsp-card-title"><?php esc_html_e( 'Payment Details', 'wpschoolpress' )?> </h3>
                        </div>
                            <div class="wpsp-card-body">

                                <table id="class_table" class="wpsp-table wpsp-table-bordered wpsp-table-striped" cellspacing="0" width="100%" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Student Name', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Roll No', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Class Name', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Class Teacher Name', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Amount', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Date', 'wpschoolpress' ); ?></th>
                                        <th><?php esc_html_e( 'Class Fee Status', 'wpschoolpress' ); ?></th>

                                    </tr>
                                    </thead>
                                    <tbody>


<?php
$proid = 0;
$proid1 = 0;
$classid = array();
$productid = array();
$stable=$wpdb->prefix."wpsp_student";
$ctlass=$wpdb->prefix."wpsp_class";
$cteacher =$wpdb->prefix."wpsp_teacher";
if($current_user_role == 'parent')
{

    $user_id = $wpdb->get_var("SELECT wp_usr_id FROM $stable where parent_wp_usr_id = '".esc_sql($current_user->ID)."'");
}
else {
 $user_id = esc_sql($current_user->ID);
}
if($current_user_role == 'parent')
{
    $wpsp_stud_data =$wpdb->get_results("SELECT * FROM  $stable s
INNER JOIN $ctlass c
where s.parent_wp_usr_id = '".$current_user->ID."' and c.c_fee_type = 'paid'");
 }else {
$wpsp_stud_data =$wpdb->get_results("SELECT * FROM  $stable s
INNER JOIN $ctlass c
where s.wp_usr_id = '".$user_id."' and c.c_fee_type = 'paid'");
}
$classid_da = maybe_unserialize($wpsp_stud_data[0]->class_id);
    $cid = [];
    if(is_numeric($wpsp_stud_data[0]->class_id) ){
        $cid[] = $wpsp_stud_data[0]->class_id;
    }else{
        $class_id_array = unserialize( $wpsp_stud_data[0]->class_id );
        $cid[] = $class_id_array;
    }
    if($current_user_role == 'parent')
{
     $courses1 = get_user_meta( $current_user->ID, '_pay_woocommerce_enrolled_class_access_counter');
 $courses2 = get_user_meta($wpsp_stud_data[0]->wp_usr_id, '_pay_woocommerce_enrolled_class_access_counter' );
 $courses = array_merge($courses1,$courses2);
}
else {
$courses1 = get_user_meta($wpsp_stud_data[0]->parent_wp_usr_id, '_pay_woocommerce_enrolled_class_access_counter' );
$courses2 = get_user_meta( $user_id, '_pay_woocommerce_enrolled_class_access_counter' );
$courses = array_merge($courses1,$courses2);
}

if ( ! empty( $courses ) ) {
      $courses = maybe_unserialize( $courses );
    } else {
      $courses = array();
    }

 $courses_check=array();
 $courses_value=array();
 $courses_value1=array();

 $courses_check=array();
                foreach($courses as $key => $value)
                {
                    foreach($value as $key1 => $value1)
                    {
                        if(!empty($value1))
                        {
                            $courses_check[] = $key1;
                                $courses_value[] = $value1;
                        }
                    }
                }

foreach($courses_value as   $valueorder){
    $courses_value1[] = $valueorder[0];
}
  $courses_valu_final = array();
foreach($courses_value as $key => $value)
{
    foreach($value as $key1 => $value1)
    {
        $courses_valu_final[] = $value1;
    }
}

$product_id=array();
$tes = arsort($courses_valu_final);
foreach($courses_valu_final as $key => $value ){
$order = wc_get_order($value);
$items = $order->get_items();

foreach ( $items as $item ) {
    $product_id[] = $item['product_id'];
}
}


$wpsp_teacher_data =$wpdb->get_results("SELECT * FROM  $cteacher
 where wp_usr_id = '".$clsloop->teacher_id."'");
        if(in_array($clsloop->cid, $courses_check)){
        $paid = esc_html('Paid','wpschoolpress');
    }else{
        $paid = esc_html('Not Paid','wpschoolpress');
    }

        $args = array(
            'post_type'      => 'product',
            'orderby' => 'publish_date',
            'order' => 'DESC',
            'posts_per_page' => -1
        );
        // $obituary_query = new WP_Query($args);

        // // while ($obituary_query->have_posts()) : $obituary_query->the_post();
        // $courses1 = get_post_meta( get_the_ID(), '_related_class', true );
        // if ( ! empty( $courses1 ) ) {
        //     $courses1 = maybe_unserialize( $courses1 );
        // } else {
        //     $courses1 = array();
        // }

        // $pro2 = 0;
        $wpsp_fees_data = $wpdb->get_results("SELECT s.wp_usr_id,f.student_id,f.class_id,f.order_id FROM wp_wpsp_student AS s INNER JOIN wp_wpsp_fees AS f ON f.student_id =  s.wp_usr_id where s.parent_wp_usr_id = '".$current_user->ID."'");
        foreach($wpsp_fees_data as $res){
            $siid[] = $res->wp_usr_id;
            $ciid = $res->class_id;
        }

        // echo"<pre>";print_r($courses_valu_final);
        // echo"<pre>";print_r($courses_check);
         $wpsp_stud =$wpdb->get_results("SELECT * FROM  $stable s  where  s.parent_wp_usr_id = '".esc_sql($current_user->ID)."' ORDER BY s.wp_usr_id DESC");


        $wpsp_tech =$wpdb->get_results("SELECT s.class_id,c.cid,c.teacher_id FROM  wp_wpsp_fees AS s INNER JOIN wp_wpsp_class AS c ON c.cid=s.class_id");
        foreach($wpsp_tech as $key =>$value11 ){
            $tech_id = $value11->teacher_id;

        }

        foreach($wpsp_stud as $value){

            if(in_array($value->wp_usr_id, $siid)){

            $wpsp_cls_data =$wpdb->get_results("SELECT s.order_id,s.fees_amount,s.created_date,s.student_id,s.class_id,c.cid,c.c_name,c.teacher_id  FROM  wp_wpsp_fees AS s INNER JOIN wp_wpsp_class AS c ON c.cid=s.class_id where student_id = '".esc_sql($value->wp_usr_id)."' ORDER BY s.student_id DESC");

            $wpsp_teacher_data =$wpdb->get_results("SELECT t.first_name,t.last_name, c.teacher_id FROM  wp_wpsp_teacher as t INNER JOIN wp_wpsp_class AS c ON c.teacher_id=t.wp_usr_id where wp_usr_id = '".$wpsp_cls_data[0]->teacher_id."'");
            // echo"<pre>";print_r($wpsp_teacher_data);

    ?>
                                    <tr>
                                        <td><?php echo  esc_html($value->s_fname.' '.$value->s_lname);?></td>
                                        <td><?php echo  esc_html($value->s_rollno);?></td>
                                        <td><?php echo  esc_html($wpsp_cls_data[0]->c_name)?></td>
                                        <td> <?php echo esc_html($wpsp_teacher_data[0]->first_name.' '.$wpsp_teacher_data[0]->last_name);?></td>

                                        <td><?php
                                        $price = esc_html($wpsp_cls_data[0]->fees_amount);
                                        if($price != ''){ echo get_woocommerce_currency_symbol().$price .'.00'; }else { echo '-'; }?>
                                        </td>
                                        <td>
                                        <?php
                                        // ->date('Y F j, g:i A');
                                        $order_date_created = esc_html($wpsp_cls_data[0]->created_date);
                                        if($order_date_created != ''){ echo $newDate = date("F d, Y", strtotime($order_date_created)); }else { echo '-'; }?></td>
                                        <td><?php
                                            echo esc_html("Fees Paid","wpschoolpress");?></td>
                                        <!-- <td><a class="wpsp-btn" href="<?php echo esc_url(get_permalink());?>" target="_blank">Pay Now</a></td> -->

                                    </tr>
                                        <?php
                                    //  $pro2++;
                                     }
                                     }
                                    // endwhile;
                wp_reset_query();
                ?>
                                </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
            wpsp_body_end();
            wpsp_footer();
        }
    }
    else{
        //Include Login Section
        include_once( WPSP_PLUGIN_PATH .'/includes/wpsp-login.php');
    }
?>
