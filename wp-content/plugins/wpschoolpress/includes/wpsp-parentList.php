<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
	  $proversion	=	wpsp_check_pro_version();
	  $proclass		=	!$proversion['status'] && isset( $proversion['class'] )? $proversion['class'] : '';
	  $protitle		=	!$proversion['status'] && isset( $proversion['message'] )? $proversion['message']	: '';
	  $prodisable	=	!$proversion['status'] ? 'disabled="disabled"'	: '';
	  $parentFieldList =  array(	'p_fname'		=>	__('First Name', 'wpschoolpress'),
									'p_mname'		=>	__('Middle Name', 'wpschoolpress'),
									'p_lname'		=>	__('Last Name', 'wpschoolpress'),
									's_fname'		=>	__('Student Name', 'wpschoolpress'),
									'user_email'	=>	__('Parent Email ID', 'wpschoolpress'),
									'p_edu'			=>	__('Education', 'wpschoolpress'),
									'p_gender'		=>	__('Gender', 'wpschoolpress'),
									'p_profession'	=>	__('Profession', 'wpschoolpress'),
									'p_bloodgrp'	=>	__('Blood Group', 'wpschoolpress'),
							);
		$sel_classid	=	isset( $_POST['ClassID'] ) ? sanitize_text_field($_POST['ClassID']) : '';
		$class_table	=	$wpdb->prefix."wpsp_class";
		$classQuery		=	"select cid,c_name from $class_table Order By cid ASC";
		// if( $current_user_role=='teacher' ) {
		// 	$cuserId	=	intval($current_user->ID);
		// 	$classQuery	=	"select cid,c_name from $class_table where teacher_id=$cuserId";
		// 	$msg		=	'Please Ask Principal To Assign Class';
		// }
	$sel_class		=	$wpdb->get_results( $classQuery );
	global $current_user;
	$role		=	 $current_user->roles;
	$cuserId	=	 $current_user->ID;
?>
<!-- This file form is used for ParentList -->
<div class="wpsp-card">
    <div class="wpsp-card-head">
        <div class="subject-inner wpsp-left wpsp-class-filter">
            <form name="ClassForm" id="ClassForm" method="post" action="">
                <label class="wpsp-labelMain"><?php _e( 'Select Class Name', 'wpschoolpress' );?></label>
                <select name="ClassID" id="ClassID" class="wpsp-form-control">
                    <option value="all" <?php if($sel_classid=='all') echo esc_html("selected","wpschoolpress"); ?>><?php echo esc_html("All","wpschoolpress");?></option> <?php
					foreach( $sel_class as $classes ) { ?> <option value="<?php echo esc_attr($classes->cid);?>" <?php if($sel_classid==$classes->cid) echo esc_html("selected","wpschoolpress"); ?>> <?php echo esc_html($classes->c_name);?> </option> <?php }
					if($current_user_role=='administrator' ) { ?>  <?php } ?>
                </select>
            </form>
        </div>
        <div class="wpsp-right wpsp-import-export">
            <div class="wpsp-btn-lists" title="<?php echo esc_attr($protitle);?>" <?php echo esc_html($prodisable);?>> <?php if ( in_array( 'teacher', $role ) ) {?> <div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>
                    <div class="wpsp-button-group wpsp-dropdownmain wpsp-left">
                        <button type="button" class="wpsp-btn wpsp-btn-success  print" id="PrintParent" <?php echo esc_html($prodisable);?> title="<?php //echo esc_attr($protitle);?>">
                            <i class="fa fa-print"></i> <?php echo esc_html("Print","wpschoolpress");?></button>
                        <button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo esc_html($prodisable);?> title="<?php //echo esc_attr($protitle);?>">
                            <!--
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
                        </button>
                        <div class="wpsp-dropdown wpsp-dropdown-md">
                            <ul>
                                <form id="ParentColumnForm" name="ParentColumnForm">
                                    <li class="wpsp-drop-title wpsp-checkList"> <?php echo esc_html("Select Columns to Print","wpschoolpress");?></li> <?php foreach( $parentFieldList as $key=>$value ) { ?> <li class="wpsp-checkList">
                                        <input type="checkbox" name="ParentColumn[]" value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" checked="checked">
                                        <label class="wpsp-label" for="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></label>
                                    </li> <?php } ?> <?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?> <input type="hidden" name="ClassID" value="<?php  echo esc_attr($currentSelectClass); ?>">
                                </form>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>
                    <div class="psp-dropdownmain wpsp-button-group">
                        <button type="button" class="wpsp-btn print" id="ExportParents" <?php echo esc_html($prodisable);?> title="<?php echo esc_attr($protitle);?>"><i class="fa fa-upload"></i> <?php echo esc_html("Export","wpschoolpress");?> </button>
                        <button type="button" class="wpsp-btn wpsp-btn-blue wpsp-dropdown-toggle" <?php echo esc_html($prodisable);?> title="<?php echo esc_attr($protitle);?>">
                            <!-- <span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
                        </button>
                        <div id="exportcontainer" style="display:none;"></div>
                        <div class="wpsp-dropdown wpsp-dropdown-md wpsp-dropdown-right">
                            <ul>
                                <form id="ExportColumnForm" name="ExportParentColumn" method="POST">
                                    <li class="wpsp-drop-title wpsp-checkList"> <?php echo esc_html("Select Columns to Export","wpschoolpress");?></li> <?php foreach( $parentFieldList as $key=>$value ) { ?> <li class="wpsp-checkList">
                                        <input type="checkbox" name="ParentColumn[]" value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" checked="checked">
                                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></label>
                                    </li> <?php } ?> <?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?> <input type="hidden" name="ClassID" value="<?php echo esc_attr($currentSelectClass); ?>">
                                    <input type="hidden" name="exportparent" value="exportparent">
                                </form>
                            </ul>
                        </div>
                    </div>
                </div> <?php } ?> <?php if ( in_array( 'administrator', $role ) ) {?> <div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>
                    <div class="wpsp-button-group wpsp-dropdownmain wpsp-left">
                        <button type="button" class="wpsp-btn wpsp-btn-success  print" id="PrintParent" <?php echo esc_html($prodisable);?> title="<?php //echo $protitle;?>">
                            <i class="fa fa-print"></i> <?php echo esc_html("Print","wpschoolpress");?> </button>
                        <button type="button" class="wpsp-btn wpsp-btn-success wpsp-dropdown-toggle" <?php echo esc_html($prodisable);?> title="<?php //echo $protitle;?>">
                            <!--
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
                        </button>
                        <div class="wpsp-dropdown wpsp-dropdown-md">
                            <ul>
                                <form id="ParentColumnForm" name="ParentColumnForm">
                                    <li class="wpsp-drop-title wpsp-checkList"> <?php echo esc_html("Select Columns to Print","wpschoolpress");?></li> <?php foreach( $parentFieldList as $key=>$value ) { ?> <li class="wpsp-checkList">
                                        <input type="checkbox" name="ParentColumn[]" value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" checked="checked">
                                        <label class="wpsp-label" for="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></label>
                                    </li> <?php } ?> <?php $currentSelectClass =	isset($_POST['ClassID']) ? intval($_POST['ClassID']) : '0'; ?> <input type="hidden" name="ClassID" value="<?php echo esc_attr($currentSelectClass); ?>">
                                </form>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="wpsp-btn-list" <?php if($proversion['status'] != "1") {?> wpsp-tooltip="<?php echo esc_attr($protitle);?>" <?php } ?>>
                    <div class="psp-dropdownmain wpsp-button-group">
                        <button type="button" class="wpsp-btn print" id="ExportParents" <?php echo $prodisable;?> title="<?php echo esc_attr($protitle);?>"><i class="fa fa-upload"></i> <?php echo esc_html("Export","wpschoolpress");?> </button>
                        <button type="button" class="wpsp-btn wpsp-btn-blue wpsp-dropdown-toggle" <?php echo esc_html($prodisable);?> title="<?php echo esc_attr($protitle);?>">
                            <!-- <span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span> -->
                        </button>
                        <div id="exportcontainer" style="display:none;"></div>
                        <div class="wpsp-dropdown wpsp-dropdown-md wpsp-dropdown-right">
                            <ul>
                                <form id="ExportColumnForm" name="ExportParentColumn" method="POST">
                                    <li class="wpsp-drop-title wpsp-checkList"> <?php echo esc_html("Select Columns to Export","wpschoolpress");?> </li> <?php foreach( $parentFieldList as $key=>$value ) { ?> <li class="wpsp-checkList">
                                        <input type="checkbox" name="ParentColumn[]" value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" checked="checked">
                                        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></label>
                                    </li> <?php } ?> <input type="hidden" name="ClassID" value="<?php if(isset($_POST['ClassID'])) echo esc_attr(sanitize_text_field($_POST['ClassID'])); else echo 0; ?>">
                                    <input type="hidden" name="exportparent" value="exportparent">
                                </form>
                            </ul>
                        </div>
                    </div>
                </div> <?php }?>
            </div>
        </div>
    </div>
    <div class="wpsp-card-body"> <?php if( empty( $sel_class ) && $current_user_role=='teacher' ) {
				echo '<div class="alert alert-danger wpsp-col-lg-2">'.esc_html($msg).'</div>';
			} else { ?> <div class="wpsp-row">
            <div class="wpsp-col-md-12 table-responsive">
                <table id="parent_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
                    <thead>
                        <tr>
                            <th><?php echo apply_filters( 'wpsp_parent_name_list_detail', esc_html__( 'Parent Name', 'wpschoolpress' )); ?></th>
                            <th><?php echo esc_html("Student Name","wpschoolpress");?></th>
                            <th><?php echo apply_filters( 'wpsp_parent_email_list_detail', esc_html__( 'Parent Email ID', 'wpschoolpress' )); ?></th>
                            <th align="center" class="nosort"><?php echo esc_html("Action","wpschoolpress");?></th>
                        </tr>
                    </thead>
                    <tbody> <?php
			$student_table	=	$wpdb->prefix."wpsp_student";
			$users_table	=	$wpdb->prefix."users";
			$class_id='';
			if( isset($_POST['ClassID'] ) && $_POST['ClassID'] != 'all' ) {
				$class_id=intval($_POST['ClassID']);
				$stl = [];
				$parentlist	=	$wpdb->get_results("select class_id, sid from $student_table WHERE parent_wp_usr_id!='0'");
					foreach ($parentlist as $stu) {
						if(is_numeric($stu->class_id) ){
							if($stu->class_id == $class_id){
								$stl[] = $stu->sid;
							}
						}
						else{
								$class_id_array = unserialize( $stu->class_id );
								if(in_array($class_id, $class_id_array)){
									$stl[] = $stu->sid;
								}
						}
					}
			}
      else if(!isset($_POST['ClassID']) || $_POST['ClassID'] == 'all' ){
        if($current_user_role =='administrator'){
              $studentlists	=	$wpdb->get_results("select sid from $student_table WHERE parent_wp_usr_id!='0'");
              foreach ($studentlists as $stu) {
                  $stl[] = $stu->sid;
              }
            }
            else if($current_user_role =='teacher'){
                // print_r($sel_class);
                  $stl = [];
                foreach ($sel_class as $key => $value) {
                  $studentlists	=	$wpdb->get_results("select class_id, sid from $student_table WHERE parent_wp_usr_id!='0'");
                    foreach ($studentlists as $stu) {
                      if(is_numeric($stu->class_id) ){
                        if($stu->class_id == $value->cid){
                        $stl[] = $stu->sid;
                      }
                      }
                      else{
                        $class_id_array = unserialize( $stu->class_id );
                        if(in_array($value->cid, $class_id_array)){
                          $stl[] = $stu->sid;
                        }
                      }
                    }

                }
            }
      }
      $parent_ids=array();
			foreach($stl as $plist){

				$parent_ids[]= $wpdb->get_row("SELECT DISTINCT  u.user_email, CONCAT_WS(' ', p_fname, p_mname, p_lname ) AS full_name, p.s_fname,p.s_lname, p.wp_usr_id, p.parent_wp_usr_id from $student_table p LEFT JOIN $users_table u ON  u.ID = p.parent_wp_usr_id  WHERE	 p.sid = $plist ");
			}
				foreach($parent_ids as $key=>$pinfo)
				{


				?> <tr>
                            <td><?php echo esc_html($pinfo->full_name);?></td>
                            <td><?php echo esc_html($pinfo->s_fname." ".$pinfo->s_lname); ?> </td>
                            <td><?php echo esc_html($pinfo->user_email);?></td>
                            <td align="center">
                                <div class="wpsp-action-col">
                                    <a href="javascript:void(0)" title="View" data-pop="ViewModal" data-id="<?php echo esc_attr(intval($pinfo->parent_wp_usr_id));?>" class="ViewParent wpsp-popclick">
                                        <i class="icon dashicons dashicons-visibility wpsp-view-icon"></i></a>
                                    <a href="<?php echo esc_url(wpsp_admin_url().'sch-student&id='.esc_attr(intval($pinfo->wp_usr_id)).'&edit=true#parent-field-lists');?>" title="Edit"><i class="icon dashicons dashicons-edit wpsp-edit-icon"></i></a>
                                </div>
                            </td>
                        </tr> <?php
				}
				?> </tbody>
                    <tfoot>
                        <tr>
                            <th><?php echo apply_filters( 'wpsp_parent_name_list_detail', esc_html__( 'Parent Name', 'wpschoolpress' )); ?></th>
                            <th><?php echo esc_html("Student Name","wpschoolpress");?></th>
                            <th><?php echo apply_filters( 'wpsp_parent_email_list_detail', esc_html__( 'Parent Email ID', 'wpschoolpress' )); ?></th>
                            <th align="center"><?php echo esc_html("Action","wpschoolpress");?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div> <?php } ?> </div>
</div>