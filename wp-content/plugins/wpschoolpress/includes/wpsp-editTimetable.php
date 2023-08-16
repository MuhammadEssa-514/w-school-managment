<?php
if ( !defined( 'ABSPATH' ) ) exit;
	$tt_table 		=	$wpdb->prefix. "wpsp_timetable";
	$subject_table 	=	$wpdb->prefix . "wpsp_subject";
	$h_table 		=	$wpdb->prefix . "wpsp_workinghours";
	$class_id 		=	intval($_GET['timetable']);
	$sess_template 	=	isset($_POST['sessions_template']) ? sanitize_text_field($_POST['sessions_template']) : '';
?>
<!-- This form is used for Edit Timetable Form -->
<section class="content">
	<div class="wpsp-row">
		<div class="wpsp-col-md-12">
			<div class="wpsp-card">
				<div class="wpsp-card-head">
					<h3 class="wpsp-card-title"><?php esc_html_e( 'Drag and Drop Subjects', 'wpschoolpress' );?> </h3>
				</div>
				<div class="wpsp-card-body">
					<?php
						$check_tt = $wpdb->get_row("Select heading from $tt_table where class_id='".esc_sql($class_id)."' and heading!=''");
                        $error = ''; $session = array();
						if (!empty($check_tt)) {
							$error = 0;
							$get_sessions = unserialize($check_tt->heading);
							foreach ($get_sessions as $sesio) {
								$session[] = $sesio;
							}
						}
						else {
							$error = 1;
							echo "<div class='wpsp-text-red'>Can't fetch template from the selected class</div>";
						}
					if (count($session) > 0) {
						$chck_hd = $wpdb->get_row("SELECT * from $tt_table where class_id='".esc_sql($class_id)."' and time_id='0' and day='0' and heading!=''");
						if (empty($chck_hd)) {
						//if(count($chck_hd) == null) {
							$ins = $wpdb->insert($tt_table, array('class_id' => $class_id,'heading' => serialize($session)));
						}
					} else {
						$error = 1;
						echo "<div class='wpsp-text-red'>No Sessions Retrieved</div>";
					}
					$wpsp_hours_table 		=	$wpdb->prefix . "wpsp_workinghours";
					$wpsp_subjects_table	=	$wpdb->prefix . "wpsp_subject";
					$clt = $wpdb->get_results("SELECT * FROM $wpsp_subjects_table WHERE class_id='".esc_sql($class_id)."' or class_id=0 order by class_id desc");
					if( count($clt) == 0 ) {
						$error = 1;
						echo "<div class='wpsp-text-red'>No Subjects retrieved, Check you have subject for this class at <a href='".esc_url(site_url()."/wp-admin/admin.php?page=sch-subject")."'>Subjects</a></div>";
					}
					if( $error == 0 ) {
						$timetable	=	array();
						$tt_days	=	$wpdb->get_results("select * from $tt_table where class_id='".esc_sql($class_id)."' and time_id !='0' ",ARRAY_A);
						foreach( $tt_days as $ttd ) {
							$timetable[$ttd['day']][$ttd['time_id']]	=	$ttd['subject_id'];
						}
						?>
						<div class="wpsp-row">
							<div class="wpsp-col-md-12">
								<div class="wpsp-form-group">
									<label class="wpsp-labelMain">
									<?php esc_html_e("Class","wpschoolpress");?> :  <?php echo wpsp_GetClassName( $class_id ); ?>
									</label>
								</div>
							</div>
						</div>
					<!-- <div class="table-responsive" cellspacing="0" width="100%" style="width:100%">
							<table align="center" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
								<tbody>
								<tr style="background: transparent;">
									<?php
										foreach ($clt as $id) {
											/*echo '<td class="removesubject"><div class="item" id="' . $id->id . '" style="width:auto; color:#5cb85c; font-weight:500;">' . $id->sub_name . '</div>	</td>';*/
											echo '<div class="removesubject"><div class="item" id="' . esc_attr($id->id) . '" style="width:auto; color:#5cb85c; font-weight:500;">' . esc_html($id->sub_name) . '</div>	</div>';
										}
									?>
								</tr>
								</tbody>
							</table>
					</div> -->
					<div class="wpsp-form-group">
					<?php
					foreach ($clt as $id) {
					echo '<div class="removesubject">
						<div class="item" id="' . esc_attr($id->id) . '" style="width:auto; color:#5cb85c; font-weight:500;">' . esc_html($id->sub_name) . '
						</div>
					</div>';
					}
					?>
					</div>
					<div class="text-right" id="ajax_response_exist" style="width: auto;float: right;text-align: center;"></div>
						<div class="right wpsp-table-responsive" id="TimetableContainer">
							<table class="wpsp-table wpsp-table-bordered" cellspacing="0" width="100%" style="width:100%">
								<thead>
									<tr><th><!-- <select class="daytype"><option value="0">Days</option><option value="1"> -->Week<!-- </option></select> --></th>
										<?php foreach ($session as $sess) { ?>
											<th><?php  $sess = esc_sql($sess); $ses_info = $wpdb->get_row("Select * from $wpsp_hours_table where id='$sess'");
												echo esc_html($ses_info->begintime . " to " . $ses_info->endtime) ?></th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php
									$dayname = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
									for ($j = 1; $j <= 7; $j++) { ?>
										<tr id="<?php echo $j; ?>">
											<td><!-- <span class="dayval">Day <?php echo esc_html($j); ?></span> --><span class="daynam"><?php echo esc_html($dayname[$j - 1]); ?></span> </td>
											<?php
											$subida = array();
											 $subida1 = array();
											  $subtimesloat = array();
                                    // echo "SELECT * from $tt_table where class_id=$class_id and day = $j ORDER BY session_id ASC";
                                    $get_heading = $wpdb->get_results("SELECT * from $tt_table where class_id='".esc_sql($class_id)."' and day = '".esc_sql($j)."' ORDER BY session_id ASC");
                                    foreach ($get_heading as $subid)
                                    {
                                         $subida[] = $subid->subject_id;
                                          $subtimesloat[] = $subid->time_id;
                                         $subida1[] = $subid->is_active;
                                         reset($subida);
                                         reset($subida1);
                                           reset($subtimesloat);
                                    }
 										$pa = 0;
										foreach ($session as $key => $ses) {
                                            $ses = esc_sql($ses);
											if($ses != $subtimesloat[$pa]){
												$hour_det	=	$wpdb->get_row("Select * from $wpsp_hours_table where id='$ses'");
								                $td_class	=	$hour_det->type == "1" ? "drop" : "Break";
                                            ?>
                                            <!-- <td class = "<//?php echo esc_attr($ses); ?> " tid="<//?php echo esc_attr($subida[$pa]); ?>" data-sessionid="<//?php echo esc_attr($key); ?>"> - </td> -->
											<td class = "<?php echo $td_class; ?>" tid="<?php if(!empty($subida[$pa])) { echo esc_attr($subida[$pa]);}else{ echo esc_attr($ses);} ?>" data-sessionid="<?php echo esc_attr($key); ?>">
											  <?php if($td_class=='Break'){ echo $td_class;}else{ '-';} ?>
										    </td>
											<?php
                                                continue;
                                            }
								$hour_det	=	$wpdb->get_row("Select * from $wpsp_hours_table where id='$ses'");
								$td_class	=	$hour_det->type == "1" ? "drop" : "break";
								$sub_id		=	$sub_name	=	'';
								if( isset($timetable[$j][$ses]) )
								$sub_id	=	$timetable[$j][$ses];
								if( $sub_id >0 ) {
 									$sub_name_f = $wpdb->get_results("SELECT sub_name from $subject_table where id=$subida[$pa]");
 															// echo "<pre>";
                //                                print_r($sub_name_f);
                                                $sub_name = isset( $sub_name_f[0]->sub_name ) ? $sub_name_f[0]->sub_name : 'N/A';
													}
												if( !empty( $sub_name ) ) {
													if($td_class == "break"){
													$sub_name	=	'<div class="item1 assigned wpsp-assigned-item">Break<a href="javascript:void(0)" class="" data-id='. esc_attr($key).' data-rowid='.esc_attr($j).'  ></a></div>';
													} else {
 														if($subida1[$pa] == 1){
                                                $sub_name = '-';
                                                    } else {
                                                        $sub_name	=	'<div class="item1 assigned wpsp-assigned-item">'.esc_attr($sub_name).'<a class="daleteid wpsp-tt-delete-icon" data-id='. esc_attr($key).' data-rowid='.esc_attr($j).'  ></a></div>';
                                                    }

													}
												}

												?>
												<td class="<?php echo esc_attr($td_class); ?>" tid="<?php echo esc_attr($ses); ?>" data-sessionid="<?php echo esc_attr($key); ?>"><?php echo wp_kses_post($sub_name); ?> </td>
												<?php
												$pa++;
											} ?>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
							<div class="wpsp-form-group">
								<div class="wpsp-col-md-offset-10">
									<input type="hidden" name="class_id" id="class_id" value="<?php echo esc_attr(intval($class_id)); ?>">
									<div id="ajax_response"></div>
								</div>
							</div>
						</div>
					<?php  } ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End of Edit Timetable Form -->