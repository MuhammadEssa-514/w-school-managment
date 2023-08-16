<?php
if (!defined( 'ABSPATH' ) )exit('No Such File');
wpsp_header();
	if( is_user_logged_in() ) {
		global $current_user, $wpdb;
		$current_user_role=$current_user->roles[0];
		if($current_user_role=='administrator' || $current_user_role=='teacher')
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
		?>
		<div class="wpsp-card">
			<div class="wpsp-card-body">
				<table id="transport_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
					<thead>
						<tr>
							<th class="nosort">#</th>
							<th><?php esc_html_e( 'Vehicle Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Number', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Phone', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Route Fees', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Route', 'wpschoolpress' ); ?> </th>
							<?php if($current_user_role=='administrator'){?>
							<th class="nosort" align="center"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
							<?php }?>
						</tr>
					</thead>
					<tbody>
					<?php
					$trans_table=$wpdb->prefix."wpsp_transport";
					$wpsp_trans =$wpdb->get_results("select * from $trans_table");
					$sno=1;
					foreach ($wpsp_trans as $wpsp_tran){ ?>
						<tr>
							<td><?php echo  esc_html($sno);?></td>
							<td><?php echo  esc_html($wpsp_tran->bus_name); ?></td>
							<td><?php echo  esc_html($wpsp_tran->bus_no);?> </td>
							<td><?php echo  esc_html($wpsp_tran->driver_name); ?></td>
							<td><?php echo  esc_html($wpsp_tran->phone_no);?></td>
							<td><?php echo  esc_html($wpsp_tran->route_fees);?></td>
							<td><?php echo  esc_html($wpsp_tran->bus_route);?></td>
							<?php if($current_user_role=='administrator'){?>
							<td align="center">
								<div class="wpsp-action-col">
									<a href="javascript:;" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>" class="ViewTrans  wpsp-popclick" data-pop="ViewModal" title="View">
									<i class="icon wpsp-view wpsp-view-icon"></i></a>
									<a href="javascript:;" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>" class="EditTrans wpsp-popclick" data-pop="ViewModal" title="Edit">
									<i class="icon wpsp-edit wpsp-edit-icon"></i></a>
									<a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>" >
	                                <i class="icon wpsp-trash wpsp-delete-icon" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>"></i></a>
								</div>
							</td>
							<?php }?>
						</tr>
					<?php
						$sno++;
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th class="nosort">#</th>
							<th><?php esc_html_e( 'Vehicle Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Number', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Phone', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Route Fees', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Route', 'wpschoolpress' ); ?> </th>
							<?php if($current_user_role=='administrator'){?>
							<th class="nosort" align="center"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
							<?php }?>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<!--Info Modal-->
		<div class="wpsp-popupMain" id="ViewModal">
			<div class="wpsp-overlayer"></div>
			<div class="wpsp-popBody">
				<div class="wpsp-popInner">
					<a href="javascript:;" class="wpsp-closePopup"></a>
					<div id="ViewModalContent" class="wpsp-text-left"></div>
				</div>
			</div>
		</div>
		<?php
			wpsp_body_end();
			wpsp_footer();
		}
		else if($current_user_role=='parent' || $current_user_role=='student' )
		{
			wpsp_topbar();
			wpsp_sidebar();
			wpsp_body_start();
			?>
			<div class="wpsp-card">
			<div class="wpsp-card-body">
				<table id="transport_table" class="wpsp-table" cellspacing="0" width="100%" style="width:100%">
					<thead>
						<tr>
							<th class="nosort">#</th>
							<th><?php esc_html_e( 'Vehicle Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Number', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Phone', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Route Fees', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Route', 'wpschoolpress' ); ?> </th>
							<?php if($current_user_role=='administrator'){?>
							<th class="nosort" align="center"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
							<?php }?>
						</tr>
					</thead>
					<tbody>
					<?php
					$trans_table=$wpdb->prefix."wpsp_transport";
					$wpsp_trans =$wpdb->get_results("select * from $trans_table");
					$sno=1;
					foreach ($wpsp_trans as $wpsp_tran){ ?>
						<tr>
							<td><?php echo  esc_html($sno);?></td>
							<td><?php echo  esc_html($wpsp_tran->bus_name); ?></td>
							<td><?php echo  esc_html($wpsp_tran->bus_no);?> </td>
							<td><?php echo  esc_html($wpsp_tran->driver_name); ?></td>
							<td><?php echo  esc_html($wpsp_tran->phone_no);?></td>
							<td><?php echo  esc_html($wpsp_tran->route_fees);?></td>
							<td><?php echo  esc_html($wpsp_tran->bus_route);?></td>
							<?php if($current_user_role=='administrator'){?>
							<td align="center">
								<div class="wpsp-action-col">
									<a href="javascript:;" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>" class="ViewTrans  wpsp-popclick" data-pop="ViewModal" title="View">
									<i class="icon wpsp-view wpsp-view-icon"></i></a>
									<a href="javascript:;" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>" class="EditTrans wpsp-popclick" data-pop="ViewModal" title="Edit">
									<i class="icon wpsp-edit wpsp-edit-icon"></i></a>
									<a href="javascript:;" id="d_teacher" class="wpsp-popclick" data-pop="DeleteModal" title="Delete" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>" >
	                                <i class="icon wpsp-trash wpsp-delete-icon" data-id="<?php echo esc_attr(intval($wpsp_tran->id));?>"></i></a>
								</div>
							</td>
							<?php }?>
						</tr>
					<?php
						$sno++;
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th class="nosort">#</th>
							<th><?php esc_html_e( 'Vehicle Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Number', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Name', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Driver Phone', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Route Fees', 'wpschoolpress' ); ?></th>
							<th><?php esc_html_e( 'Vehicle Route', 'wpschoolpress' ); ?> </th>
							<?php if($current_user_role=='administrator'){?>
							<th class="nosort" align="center"><?php esc_html_e( 'Action', 'wpschoolpress' ); ?></th>
							<?php }?>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<!--Info Modal-->
		<div class="wpsp-popupMain" id="ViewModal">
			<div class="wpsp-overlayer"></div>
			<div class="wpsp-popBody">
				<div class="wpsp-popInner">
					<a href="javascript:;" class="wpsp-closePopup"></a>
					<div id="ViewModalContent" class="wpsp-text-left"></div>
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
