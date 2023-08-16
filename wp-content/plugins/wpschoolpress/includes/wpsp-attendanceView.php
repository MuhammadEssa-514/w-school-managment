<?php
if ( !defined( 'ABSPATH' ) ) exit;
	$att_table=$wpdb->prefix."wpsp_attendance";
	$class_table=$wpdb->prefix."wpsp_class";
    $leave_table=$wpdb->prefix."wpsp_leavedays";
	$get_classes=$wpdb->get_results("select * from $class_table");
    $today=date('Y-m-d');
	?>
    <h3 class="wpsp-card-title"><?php esc_html_e( 'Attendance Overview', 'wpschoolpress' ); ?></h3>
	<?php
		foreach( $get_classes as $get_class )
		{
            $date_warning='';
		    $sdate=sanitize_text_field($get_class->c_sdate);
            $edate=sanitize_text_field($get_class->c_edate);
            if($sdate=='' || $sdate=='0000-00-00'){
                $sdate=date('Y-m-d');
                $date_warning= esc_html("*Please enter valid start date.", "wpschoolpress");
            }
            if($edate=='' || $edate=='0000-00-00' || $edate>$today){
                $edate=date('Y-m-d');
            }
			$class_id		=	intval($get_class->cid);
			$att			=	$wpdb->get_row("SELECT * from $att_table where date='".esc_sql($today)."' and class_id='".esc_sql($class_id)."'");
            $check_leave	=	$wpdb->get_row("SELECT * from $leave_table where leave_date='".esc_sql($today)."' and class_id='".esc_sql($class_id)."'");
			if($att) {
                if($att->absents=='Nil')
                    $tot_abs=0;
                if($att->absents!='Nil')
                    $tot_abs=count(json_decode($att->absents));
				if($tot_abs==0) {
				    $box_class="cbox-success";
                }else {
                    $box_class="cbox-danger";
                }
			?>
            <!-- Absents -->
                <div class="col-md-4">
                    <div class="cbox <?php echo esc_attr($box_class); ?>">
                        <div class="shape">
                            <div class="shape-text">
                                <?php echo esc_html($tot_abs); ?>
                            </div>
                        </div>
                        <div class="cbox-content">
                            <h3>
                                <?php echo esc_html($get_class->c_name); ?>
                            </h3>
                            <?php
                            $work_days=wpsp_AttStatus($sdate,$edate,$class_id);
                            ?>
                            <div class="col-md-12">
                                <span class="col-md-10 PZero"><?php esc_html_e( 'No. of Absents (Today)', 'wpschoolpress' )?>:</span>
                                <span class="label label-info pointer viewAbsentees" data-id="<?php echo esc_attr($class_id);?>"><?php echo esc_html($tot_abs);?></span>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <span class="col-md-10 PZero" title="<?php echo wpsp_ViewDate(esc_attr($sdate))." - ".wpsp_ViewDate(esc_attr($edate)); ?>"><?php esc_html_e( 'No. of Working Days:', 'wpschoolpress' )?></span>
                                <span class="label label-info"><?php echo esc_html($work_days['wdays']);?></span>
                            </div>
                            <div class="col-md-12">
                                <span class="col-md-10 PZero" title="<?php echo wpsp_ViewDate(esc_attr($sdate))." - ".wpsp_ViewDate(esc_attr($edate)); ?>"><?php esc_html_e( 'No. of days not entered:', 'wpschoolpress' )?></span>
                                <span class="label label-warning"><?php echo esc_html($work_days['not_entered']); ?></span>
                            </div>
                            <div class="col-md-12 red"><?php echo esc_html($date_warning);?></div>
                        </div>
                    </div>
                </div>
            <?php
			} else { ?>
				<!-- Not Yet Entered -->
				<div class="col-md-4">
					<div class="cbox cbox-warning">
						<div class="shape">
							<div class="shape-text">
							</div>
						</div>
						<div class="cbox-content">
							<h3 class="lead">
								<?php echo esc_html($get_class->c_name); ?>
							</h3>
                            <?php
                            $work_days=wpsp_AttStatus($sdate,$edate,$class_id);
                            ?>
                            <div class="col-md-12">
                                <span class="col-md-10 PZero"><?php esc_html_e( 'No. of Absents (Today):', 'wpschoolpress' );?></span>
                                <span class="label label-danger"><?php echo (count($check_leave)>0)?'N/A':'N/E'; ?></span>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <span class="col-md-10 PZero" title="<?php echo wpsp_ViewDate(esc_attr($sdate))." - ".wpsp_ViewDate(esc_attr($edate)); ?>"><?php esc_html_e( 'No. of Working Days:', 'wpschoolpress' );?></span>
                                <span class="label label-info"><?php echo esc_html($work_days['wdays']);?></span>
                            </div>
                            <div class="col-md-12">
                                <span class="col-md-10 PZero" title="<?php echo wpsp_ViewDate(esc_attr($sdate))." - ".wpsp_ViewDate(esc_attr($edate)); ?>"><?php esc_html_e( 'No. of days not entered:', 'wpschoolpress' );?></span>
                                <span class="label label-warning"><?php echo esc_html($work_days['not_entered']); ?></span>
                            </div>
                            <?php if(count($check_leave)>0){ ?>
                                    <div class="fa fa-exclamation-triangle text-red text-center col-md-10 MTTen" title="Today is marked as leave!"><?php echo esc_html($check_leave->description);?></div>
                            <?php } ?>
						</div>
					</div>
				</div>
			<?php
			}
			unset($attarray);
		}
	?>
    <div class="col-md-12"><span class="label wpsp-label-danger"><?php echo esc_html('N/E','wpschoolpress');?></span> <?php esc_html_e( 'Not yet Entered', 'wpschoolpress' );?> <span class="label wpsp-label-danger"><?php echo esc_html('N/A','wpschoolpress');?></span> <?php esc_html_e( 'Not Applicable(Date is marked as leave)', 'wpschoolpress' );?></div>
	<div class="modal fade" id="ViewModal" tabindex="-1" role="dialog" aria-labelledby="ViewModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" id="ViewModalContent">
			</div>
		</div>
	</div>
