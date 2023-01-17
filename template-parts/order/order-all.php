<div class="col-xl-12">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header" style="display: flex;">
				<h6 class="title"><?php esc_html_e( 'Incoming Orders', 'wrhhub' ); ?></h6>
				<div class="bulk_edit">
					<button type="submit" class="btn btn-warning"><?php esc_html_e( 'Bulk Edit', 'wrhhub' ); ?></button>
				</div>
				<div class="order_change_bulk">
					<select class="form-control select_status" name="" id="select_status">
						<option><?php esc_html_e( 'Order Status', 'wrhhub' ); ?></option>
						<option value="Pending"><?php esc_html_e( 'Pending', 'wrhhub' ); ?></option>
						<option value="CNC"><?php esc_html_e( 'CNC', 'wrhhub' ); ?></option>
						<option value="Pre-Assembly"><?php esc_html_e( 'Pre-Assembly', 'wrhhub' ); ?></option>
						<option value="Assembly"><?php esc_html_e( 'Assembly', 'wrhhub' ); ?></option>
						<option value="Final Assembly"><?php esc_html_e( 'Final Assembly', 'wrhhub' ); ?></option>
						<option value="Final Sanding"><?php esc_html_e( 'Final Sanding', 'wrhhub' ); ?></option>
						<option value="Finishing"><?php esc_html_e( 'Finishing', 'wrhhub' ); ?></option>
						<option value="Ready To Ship"><?php esc_html_e( 'Ready To Ship', 'wrhhub' ); ?></option>
						<option value="Staged To Ship"><?php esc_html_e( 'Staged To Ship', 'wrhhub' ); ?></option>
						<option value="Packaged"><?php esc_html_e( 'Packaged', 'wrhhub' ); ?></option>
					</select>
				</div>
				<div class="bulk_action_btn_section">
					<button type="submit"
							class="btn btn-danger bulk_download_bol"><?php esc_html_e( 'Print BOL & Shipping Label', 'wrhhub' ); ?></button>
				</div>
				<div class="bulk_action_btn_section">
					<button type="submit" class="btn btn-danger"><?php esc_html_e( 'Send To Almoxy', 'wrhhub' ); ?></button>
				</div>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li class="dropdown">
						<a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php esc_html_e( 'Create', 'wrhhub' ); ?></a>

						<ul class="dropdown-menu">
							<li><a href="#"><?php esc_html_e( 'New Order', 'wrhhub' ); ?></a></li>
							<li><a href="#"><?php esc_html_e( 'Create Invoice', 'wrhhub' ); ?></a></li>
						</ul>
					</li>
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="wrh-order-list">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Order Id', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Status', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Source', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Is Priority?', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Ship Method', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Shipping Address:', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Bill Of Lading', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Shipping Label', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Items', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Actions', 'wrhhub' ); ?></th>
				</tr>
				</thead>
				<div class="container">
					<div class="row">
						<div class="hy"></div>
					</div>
				</div>
				<tbody>
				<?php
				$hub_paged              = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$default_posts_per_page = get_option( 'posts_per_page' );
				$args                   = array(
					'post_type'      => array( 'wrh_order', 'quick_stock_request' ),
					'posts_per_page' => $default_posts_per_page,
					'paged'          => $hub_paged,
					'orderby'        => 'ID',
					'order'          => 'DESC',
					'meta_query'     => array(
						array(
							'key'     => 'custom_color_match',
							'value'   => '0',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'completion_date',
							'value'   => 'none',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'origin',
							'value'   => 'USCD',
							'compare' => 'NOT LIKE',
						),
						array(
							'key'     => 'order_status',
							'value'   => array( 'CNC', 'On Hold' ),
							'compare' => 'NOT IN',
						),
						array(
							'key'     => 'shop_claim',
							'value'   => array( 'yes', 'no' ),
							'compare' => 'IN',
						),
						array(
							'key'     => 'accessory_order_damage',
							'value'   => 'no',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'production_que',
							'compare' => 'NOT EXISTS',
						),
					),
				);
				$all_orders             = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {
					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$bill_of_landing_id          = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
						$shipping_add                = get_post_meta( get_the_ID(), 'shipping', true );
						$billing_add                 = get_post_meta( get_the_ID(), 'billing', true );
						$shipping                    = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
						$billing                     = ( isset( $billing_add ) && is_array( $billing_add ) ) ? $billing_add : array();
						$first_name                  = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
						$last_name                   = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
						$phone                       = ( isset( $billing['phone'] ) && ! empty( $billing['phone'] ) ) ? $billing['phone'] : '';
						$email                       = ( isset( $billing['email'] ) && ! empty( $billing['email'] ) ) ? $billing['email'] : '';
						$address_1                   = ( isset( $shipping['address_1'] ) && ! empty( $shipping['address_1'] ) ) ? $shipping['address_1'] : '';
						$address_2                   = ( isset( $shipping['address_2'] ) && ! empty( $shipping['address_2'] ) ) ? $shipping['address_2'] : '';
						$city                        = ( isset( $shipping['city'] ) && ! empty( $shipping['city'] ) ) ? $shipping['city'] : '';
						$state                       = ( isset( $shipping['state'] ) && ! empty( $shipping['state'] ) ) ? $shipping['state'] : '';
						$postcode                    = ( isset( $shipping['postcode'] ) && ! empty( $shipping['postcode'] ) ) ? $shipping['postcode'] : '';
						$order_status                = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$es_shipping_date            = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
						$shipping_date               = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
						$estimated_shipping_date     = $shipping_date ?? '';
						$origin                      = get_post_meta( get_the_ID(), 'origin', true );
						$shipping_lines_arr          = get_post_meta( get_the_ID(), 'shipping_lines', true );
						$shipping_lines              = isset( $shipping_lines_arr ) && is_array( $shipping_lines_arr ) ? $shipping_lines_arr : array();
						$shipping_lines_method_title = ( isset( $shipping_lines['method_title'] ) && ! empty( $shipping_lines['method_title'] ) ) ? $shipping_lines['method_title'] : '';
						$domain_parts                = explode( '.', $origin );
						$order_link                  = get_template_link( 't_order-details.php' );
						$order_id                    = get_post_meta( get_the_ID(), 'order_id', true );
						$line_items                  = get_post_meta( get_the_ID(), 'line_items', true );
						$is_priority                 = get_post_meta( get_the_ID(), 'rush_manufacturing', true );
						$is_priority_damage_claim    = get_post_meta( get_the_ID(), 'is_priority_damage_claim', true );
						$order_hold_is_priority      = get_post_meta( get_the_ID(), 'order_hold_is_priority', true );
						$damage_item                 = get_post_meta( get_the_ID(), 'damage_item', true );
						$hood_replace                = get_post_meta( get_the_ID(), 'hood_replace', true );
						$f_shelf_replace             = get_post_meta( get_the_ID(), 'f_shelf_replace', true );
						$hall_tree_replace           = get_post_meta( get_the_ID(), 'hall_tree_replace', true );
						$no_replace                  = get_post_meta( get_the_ID(), 'no_replace', true );
						$bol_link                    = get_post_meta( get_the_ID(), 'bol_pdf', true );
						$shipping_file_link          = get_post_meta( get_the_ID(), 'shipping_label', true );
						$backgroundg_color           = ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#98A8F8' : ( ( 'Assembly' === $order_status ) ? 'style=background-color:#5DA7DB' : ( ( 'Pre-Assembly' === $order_status ) ? 'style=background-color:#81C6E8' : ( ( 'Final Assembly' === $order_status ) ? 'style=background-color:#7DE5ED' : ( ( 'Final Sanding' === $order_status ) ? 'style=background-color:#FFCACA' : ( ( 'Finishing' === $order_status ) ? 'style=background-color:#47B5FF' : ( ( 'Ready To Ship' === $order_status ) ? 'style=background-color:#FF884B' : ( ( 'Packaged' === $order_status ) ? 'style=background-color:#FF731D' : '' ) ) ) ) ) ) );
						$bol_regenerated             = get_post_meta( get_the_ID(), 'bol_regenerated', true );
						$disabled                    = '';
						if ( 'rush_my_order' === $is_priority || 'yes' === $order_hold_is_priority || 'yes' === $is_priority_damage_claim ) {
							$is_priority = '';
						} else {
							$is_priority = 'text-decoration-line: line-through';
							$disabled    = 'disabled';
						}
						$completion_date = trim( get_post_meta( get_the_ID(), 'completion_date', true ) );
						$current_date    = gmdate( 'F j, Y' );
						$date1           = strtotime( $completion_date );
						$date2           = strtotime( $current_date );
						$date_difference = $date1 - $date2;
						$result          = round( $date_difference / ( 60 * 60 * 24 ) );
						if ( 'quick_stock_request' === get_post_type() ) {
							$req_stock_quantity = get_post_meta( get_the_ID(), 'req_stock_quantity', true );
							$size_attr          = get_post_meta( get_the_ID(), 'size_attr', true );
							?>
								<tr style="background-color: #ff2f2fc2; color:#ffffff00">
									<td data-title="Order Id">
										<a href="#" style="color:#fff">HYP - <?php the_date( 'dmY' ); ?></a>
									</td>
									<td>
										HypeMill
									</td>
									<td data-title="Status" class="staus-dropdown dropdown">
										<input type="hidden" class="size_attr" name="size_attr" id="" value='<?php echo esc_html( $size_attr ); ?>'>
										<button role="button"
												class="btn btn-waiting quick-ship-wrh-ready_pick"<?php echo esc_attr( $backgroundg_color ); ?>
												data-variation_id="<?php echo the_title(); ?>"
												data-req_stock="<?php echo intval( $req_stock_quantity ); ?>"
												data-size_attr='<?php echo $size_attr; ?>' class="quick-ship-wrh-ready_pick"
												data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_ship_wrh_nonce' ) ); ?>">
										<?php esc_html_e( 'Ready for pickup', 'hoodslyhub' ); ?>
										</button>
									</td>
									<td>HypeMill</td>
									<td data-title="Order Id">
										<p style="color:#fff">Stock Requested - <?php echo esc_html( $req_stock_quantity ); ?></p>
									</td>
									<td data-title="Order Id">
										<p style="color:#fff"><?php echo esc_html( $size_attr ); ?></p>
									</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<?php
						} else {
							?>
								<tr style="background-color: #4747471a;">
									<td data-title="Order Id">
										<input type="checkbox" class="bulk_check" value="test" data-orderid="<?php echo esc_html( $order_id ); ?>"
											   data-postid="<?php echo get_the_ID(); ?>
									" data-orderurl="
								<?php
									echo esc_url(
										add_query_arg(
											array(
												'post_id'  => get_the_ID(),
												'order_id' => $order_id,
											),
											$order_link
										)
									);
								?>
														"/>
										<a href="
											<?php
											echo esc_url(
												add_query_arg(
													array(
														'post_id'  => get_the_ID(),
														'order_id' => $order_id,
													),
													$order_link
												)
											)
											?>
													"><?php the_title(); ?></a>
									</td>
									<td data-title="Order Status">

										<button class="btn btn-violet" <?php echo esc_attr( $backgroundg_color ); ?>>
											<?php echo esc_html( $order_status ); ?>
										</button>
									</td>
									<td data-title="Estimated Shipping Date"><?php echo esc_html( $origin ); ?></td>
									<td data-title="Is Priority?">
										<button class="btn btn-danger <?php echo esc_attr( $disabled ); ?>"
												style="<?php echo esc_attr( $is_priority ); ?>">Priority
										</button>
									</td>
									<td data-title="Ship Method"><?php echo esc_html( $shipping_lines_method_title ); ?></td>
									<td data-title="Shipping Address">
										<?php
										echo esc_html( $address_1 ) . ' ' . esc_html( $city ) . ' ' . esc_html( $state ) . ' ' . esc_html( $postcode );
										?>
									</td>
									<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right">
										<button class="btn btn-violet" style="background-color:#5F9DF7;"><a style="color:#ffffff"
													href="<?php echo esc_url( $bol_link ); ?>"><?php esc_html_e( 'View', 'wrhhub' ); ?><span
														class="<?php echo ! empty( $bol_regenerated ) && 'yes' === $bol_regenerated ? 'red_circle' : ''; ?>"></span></a>
										</button>
									</td>
									<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right">
										<button class="btn btn-violet" style="background-color:#5F9DF7;"><a style="color:#ffffff"
													href="<?php echo esc_url( $shipping_file_link ); ?>"><?php esc_html_e( 'View', 'wrhhub' ); ?></a>
										</button>
									</td>
									<td data-title="Order Notes">
										<button
												class="btn btn-border" type="button" data-toggle="collapse"
												data-target="#notes-<?php echo esc_html( $order_id ); ?>" aria-expanded="false"
												aria-controls="notes-<?php echo esc_html( $order_id ); ?>"><?php esc_html_e( 'Item Detail', 'wrhhub' ); ?>
										</button>
									</td>
									<td data-title="Actions" class="action-dropdown dropdown">
										<div role="button" class="icon-dots" data-toggle="dropdown">
											<span></span><span></span><span></span></div>
										<ul class="dropdown-menu dropdown-menu-right">
											<li><a href="
													<?php
													echo esc_url(
														add_query_arg(
															array(
																'post_id'  => get_the_ID(),
																'order_id' => $order_id,
															),
															$order_link
														)
													)
													?>
															">View</a></li>

												<li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order"
													   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_delete_order_nonce' ) ); ?>"><?php echo esc_html__( 'Delete', 'wrhhub' ); ?></a>
												</li>
											<li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" data-toggle="modal" data-target="#wrh_packaged_upload_proof_modal_
																					 <?php
																						echo esc_html(
																							$order_id
																						);
																						?>
												"><?php echo esc_html__( 'Upload Proof', 'wrhhub' ); ?></a>
											</li>
										</ul>
										<div class="wrh_packaged_upload_proof_wrapper modal fade" id="wrh_packaged_upload_proof_modal_<?php echo esc_html( $order_id ); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
											<div class="modal-dialog modal-dialog-centered" role="document">
												<div class="modal-content" id="modal-content">
													<div class="modal-header">
													</div>
													<div class="modal-body wrh_packaged_upload_proof_modal_body">
														<h2 class="mb-3 mt-3 font-weight-bold text-center"><?php echo esc_html__( 'Proof Drop Off Image upload', 'wrhhub' ); ?></h2>
														<form id="wrh_packaged_upload_proof_action" class="wrh_packaged_upload_proof_action" method="post" data-autosubmit="false" autocomplete="off" enctype="multipart/form-data" data-orderid="<?php echo esc_html( $order_id ); ?>">
															<figure class="media drop_off delivered_option">
																<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" id="Layer_1" width="150" height="150" x="0" y="0" style="enable-background:new 0 0 752.3 536.2" version="1.1" viewBox="0 0 752.3 536.2">
  <style>
	  .st0{fill:#f0f0f0}.st1{fill:#f2f2f2}.st2{fill:#fff}.st5{fill:#aa4098}.st7{fill:#ccc}.st8{fill:#3f3d56}
  </style>
																	<path d="M175.2 234.1C80.2 140.9 135.5 55.3 269 42l.3 2.5c-131.4 13.3-185.7 96.2-92.3 188l-1.8 1.6zm368.3 133.4c-104.3.6-235.3-36.9-319.4-94.2l1.4-2.1c87.8 59.8 227 98 334.5 93.4l.1 2.5c-5.5.3-11 .4-16.6.4zm109.2-15.7-.8-2.4c213.3-88.3-50.6-263-183.9-287.1l.6-2.4c136 24.2 400.5 202.5 184.1 291.9zm-391.1 16.7C168 327 95.9 273.2 58.5 216.9l2.1-1.4c37.1 55.9 108.8 109.4 202 150.7l-1 2.3zm329 71.9c-9.7 0-19.5-.2-29.5-.6l.1-2.5c74.5 3.2 140.2-5.7 190.1-25.8l.9 2.3c-43.4 17.5-98.8 26.6-161.6 26.6zM536.3 44.2C436.6 11.3 320.2-4.6 228.7 5.4l-.3-2.5c91.6-10 208.7 5.8 308.6 39l-.7 2.3z" class="st0"/>
																	<path d="M116.7 237.8C21.8 144.6 76.9 59.1 210.4 45.6l.3 2.5C79.3 61.4 25 144.3 118.4 236.1l-1.7 1.7zM485 371.2c-104.3.6-235.3-36.9-319.4-94.2l1.4-2.1c87.8 59.8 227 98 334.5 93.4l.1 2.5c-5.5.3-11 .4-16.6.4zm109.2-15.6-.8-2.4c213.3-88.3-50.6-263-183.9-287.1l.6-2.4c136 24.2 400.5 202.5 184.1 291.9zm-391.1 16.6C109.5 330.7 37.4 276.9 0 220.6l2.1-1.4c37.1 55.9 108.8 109.4 202 150.7l-1 2.3zm329 72c-9.7 0-19.5-.2-29.5-.6l.1-2.5c74.5 3.2 140.2-5.7 190.1-25.8l.9 2.3c-43.4 17.5-98.8 26.6-161.6 26.6zM477.8 47.9C378.1 15 261.7-.9 170.2 9.1l-.2-2.5c91.6-10 208.7 5.8 308.6 39l-.8 2.3z" class="st0"/>
																	<path d="M42.4 171.2c17.9-20.1 46.4-32.3 72.3-25-73.5 49.7-26 126.5-103.9 98 4.9-26.4 13.7-53 31.6-73z" class="st1"/>
																	<path d="M114.6 146.8c-25.5 6.5-50.8 22.1-63.2 46-6.3 22.2 11.8 36-22.5 44-6 1.4-12.2 2.8-17.1 6.8-.6.5-1.3-.5-.7-.9 8.5-6.9 20.3-6 29.7-11.1 16.4-7.2 4.3-25.2 9-37.8 11.7-25.3 38.5-41.4 64.7-48.2.7-.2.9 1 .1 1.2z" class="st2"/>
																	<path d="M63.5 175.1c-5.2-5.8-5.4-14.5-.5-20.6.5-.6 1.4.1.9.7-9.1 10.5 3.5 21.3-.4 19.9zm-14.2 29.3c7.7.5 15.3-1.9 21.3-6.7.6-.5 1.3.5.7.9-6.2 5-14.1 7.5-22.1 7-.8-.1-.6-1.3.1-1.2zM91.5 155c1.5 4.4 10.3 4.3 7.1 5.7-2.2 1-11.1-6-7.1-5.7z" class="st2"/>
																	<path d="M151.4 229.5c-28.9-.9-58.1 8.6-80.7 26.8-7.3 5.8-14.6 12.9-23.7 15l-36-24.9c-.1-.3-1.5-1-1.7-1.2.5-.4.9-.9 1.4-1.2.1-.1.3-.2.3-.3l4.2-3.6c35.3-33.9 100.5-54.3 136.2-10.6z" class="st1"/>
																	<path d="M151 229.9c-24.4-10.1-53.9-12.9-78.2-1.2-18.4 13.8-12.2 35.8-44.5 21.6-5.6-2.5-11.4-5.1-17.7-4.9-.8 0-.8-1.2 0-1.2 10.9-.4 19.8 7.4 30.4 9 17.4 4.1 18.5-17.5 29.9-24.7 24.5-13.2 55.7-9.9 80.7.4.7.3.1 1.3-.6 1z" class="st2"/>
																	<path d="M93.1 221.7c-.6-7.8 4.4-14.8 12-16.7.7-.2 1.1 1 .3 1.1-13.5 3-9.9 18.8-12.3 15.6zm-28.9 14.8c5.8 5 13.3 7.7 21 7.4.8 0 .8 1.2 0 1.2-8 .3-15.8-2.5-21.9-7.8-.5-.4.3-1.3.9-.8zm63.4-14c-.4 3 .8 6 3.1 7.9.6.5-.3 1.3-.8.8-2.5-2.2-3.8-5.5-3.4-8.8.1-.8 1.1-.8 1.1.1z" class="st2"/>
																	<path d="M743.3 335.6c-10.1-24.9-32.9-46-59.7-47.8 52.5 71.5-18.1 128.1 65 127.2 4.2-26.5 4.8-54.5-5.3-79.4z" class="st1"/>
																	<path d="M683.5 288.3c21.9 14.8 40.4 37.9 44 64.6-1.4 23-23.1 30 6.5 49 5.2 3.3 10.6 6.7 13.8 12.1.4.7 1.4 0 1-.6-5.7-9.4-17.1-12.5-24.2-20.4-12.9-12.4 4.5-25.2 4.3-38.7-2.5-27.7-22.3-51.9-44.6-67.2-.9-.2-1.4.8-.8 1.2z" class="st2"/>
																	<path d="M722.2 332.2c6.8-3.7 9.9-11.9 7.4-19.2-.3-.7-1.4-.4-1.1.4 4.9 13.2-10.7 18.9-6.3 18.8zm3.4 32.3c-7.4-2.1-13.7-6.9-17.8-13.5-.4-.7-1.4 0-1 .6 4.2 6.8 10.8 11.8 18.5 14 .8.3 1.1-.9.3-1.1zm-23-60.7c-2.3 2-5.4 2.6-8.3 1.8-.7-.2-1 .9-.2 1.2 1.7 1.7 12.7-2 8.5-3z" class="st2"/>
																	<path d="M621.1 353.8c27.6 8.9 51.8 27.7 67.1 52.4 5 7.9 9.4 17 17.2 22.1l42.3-11.3c.1-.1.2-.1.3-.2l1.7-.4c-.2-.6-.7-1.2-1-1.7 0 0 0-.1-.1-.1-.8-1.7-1.9-3.4-2.8-5.1-21.9-43.7-76.6-84.8-124.7-55.7z" class="st1"/>
																	<path d="M621.3 354.4c26.4-1.4 55.1 5.9 74 25.1 12.6 19.2-.2 38 34.7 35.3 6.1-.4 12.5-1 18.3 1.3.7.3 1.1-.8.4-1.1-10.2-4.1-21.2.3-31.6-1.7-17.8-2-11.5-22.8-19.9-33.3-13.9-18.1-57.8-31.7-75.9-25.6z" class="st2"/>
																	<path d="M678.6 366.1c3.2-7.1.8-15.5-5.7-19.8-.6-.4-1.3.5-.7 1 11.9 7.6 2.8 21.4 6.4 18.8zm22.2 23.7c-7.2 2.8-15.1 2.8-22.3-.1-.7-.3-1.1.8-.4 1.1 7.5 2.9 15.7 2.9 23.2 0 .7-.2.2-1.3-.5-1zm-55-34.6c-.7 3-2.8 5.4-5.6 6.4-1.2 4.3 8.9-4.9 6.3-6.8-.2 0-.5.1-.7.4z" class="st2"/>
																	<path d="M211.2 124.2c13-.2 13 20.2 0 20-13 .2-13-20.2 0-20z" style="fill:#fd6584"/>
																	<path d="M572.6 239.2c1.9-12.8 22-9.6 19.7 3.2-1.8 12.9-22 9.6-19.7-3.2z" style="fill:#5d9cf9"/>
																	<path d="M210.8 12.2c13-.2 13 20.2 0 20-13 .2-13-20.2 0-20z" class="st5"/>
																	<path d="M510.9 130.8c6-30.7 51.8-22.6 46.9 8.3-5.9 30.6-51.8 22.6-46.9-8.3z" style="fill:#ffb8b8"/>
																	<path id="a16eae09-ccfb-4b79-a9f9-10731758882a-996" d="M49.9 536.2c1.2-1.2 654 2.3 653.8-1.2 1.8-1.2-661.3-4.5-653.8 1.2z" class="st7"/>
																	<path d="M507.9 139.4H505c-3.3-39.6 17.7-121.7-45.1-123H295.1c-24.9 0-45.1 20.2-45.1 45.1v427.1c0 24.9 20.2 45.1 45.1 45.1 287.5 2.7 194.6 28.8 210-338.9h2.8v-55.4z" class="st8"/>
																	<path d="M495.5 61.8v426.5c0 18.6-15.1 33.6-33.6 33.6H295.5c-20 1.9-35.4-23.8-33-33.7V61.3c.3-18.3 15.3-33 33.6-33h20.1c-4.4 10.3 3.7 22.2 14.8 22h94.5c11.1.2 19.2-11.9 14.8-22 26.1-1.9 53.6 1.3 55.2 33.5z" class="st1"/>
																	<path d="M275 68.9h51.4v2.3H275zm78.2 0h51.4v2.3h-51.4zm78.3 0h51.4v2.3h-51.4z" class="st2"/>
																	<path d="M245.2 98.5h9.2V148h-9.2z" class="st8"/>
																	<path d="M333.8 30.6c6-.1 6 9.3 0 9.2-6 .1-6-9.3 0-9.2z" class="st2"/>
																	<path d="M245.2 153.8h9.2v28.8h-9.2zm0 34.5h9.2v28.8h-9.2z" class="st8"/>
																	<path d="M275 482h51.4v2.3H275zm78.2 0h51.4v2.3h-51.4zm78.3 0h51.4v2.3h-51.4z" class="st2"/>
																	<path d="M461.6 305.9H294c-2.4 0-4.3-1.9-4.3-4.3s1.9-4.3 4.3-4.3h167.6c5.6-.1 5.6 8.7 0 8.6zm0 30H294c-2.4 0-4.3-1.9-4.3-4.3s1.9-4.3 4.3-4.3h167.6c5.6-.1 5.6 8.7 0 8.6zm0 29.9H294c-2.4 0-4.3-1.9-4.3-4.3s1.9-4.3 4.3-4.3h167.6c5.6-.1 5.6 8.7 0 8.6z" class="st7"/>
																	<path d="M454.3 278.2H303.5c-6.1 0-11-4.9-11-11v-140c0-6.1 4.9-11 11-11h150.7c6.1 0 11 4.9 11 11v140.1c0 6-4.9 10.9-10.9 10.9z" style="fill:#e6e6e6"/>
																	<path d="M422.5 270.8H310.9c-6.1 0-11-4.9-11-11V134.6c0-6.1 4.9-11 11-11h135.9c6.1 0 11 4.9 11 11v101c0 19.4-15.8 35.2-35.3 35.2z" class="st2"/>
																	<circle cx="378.9" cy="197.2" r="49.2" class="st5"/>
																	<path d="m399.6 194.7-16.4-20c-1.7-2.1-4.7-2.4-6.8-.7-.2.2-.4.3-.6.5l-17.6 20c-1.8 2-1.6 5.1.4 6.8 2 1.8 5.1 1.6 6.8-.4l9.1-10.3v25.9c0 2.7 2.2 4.8 4.8 4.8s4.8-2.2 4.8-4.8v-25.2l7.8 9.5c1.7 2.1 4.7 2.4 6.8.7 2.3-1.6 2.6-4.7.9-6.8z" class="st2"/>
																	<path d="M460.6 454.7H295c-2.9 0-5.2-2.3-5.2-5.2v-28.3c0-2.9 2.3-5.2 5.2-5.2h165.6c2.9 0 5.2 2.3 5.2 5.2v28.3c0 2.8-2.3 5.2-5.2 5.2z" class="st5"/>
</svg>
															</figure>
															<div class="delivered_drop_off_image_upload drop_off delivered_option">
																<div class="product-info mb-5">
																	<div class="text">
																		<h6 class="mb-4"><?php echo esc_html__( 'Order Information', 'wrhhub' ); ?></h6>
																		<ul class="list-style">
																			<li><strong><?php esc_html_e( 'Order ID', 'wrhhub' ); ?></strong>
																				<span>: #<?php echo esc_html( $order_id ); ?> </span>
																			</li>
																			<li><strong><?php esc_html_e( 'Customer Name', 'wrhhub' ); ?></strong>
																				<span>: <?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?> </span>
																			</li>
																			<li><strong><?php esc_html_e( 'Customer Email', 'wrhhub' ); ?></strong>
																				<span>: <?php echo esc_html( $email ); ?> </span>
																			</li>
																			<li><strong><?php esc_html_e( 'Customer Phone', 'wrhhub' ); ?></strong>
																				<span>: <?php echo esc_html( $phone ); ?> </span>
																			</li>
																		</ul>
																	</div>
																</div>
																<h5 class="mb-3"><?php echo esc_html__( 'Please Upload the Drop Off Image', 'wrhhub' ); ?></h5>
																<input type="file" name="file" capture="" accept="image/*" id="drop_off_image_upload-<?php echo esc_html( $order_id ); ?>" class="drop_off_image_upload"/>
															</div>
															<input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>
															<input type="hidden" name="order_id" value="<?php echo esc_html( $order_id ); ?>"/>
															<input type="hidden" name="action" value="wrh_packaged_upload_proof_action_process"/>
															<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'wrh_packaged_upload_proof_action_process_nonce' ) ); ?>"/>

															<div class="modal-footer mt-4 mb-4">
																<button type="submit" class="btn btn-primary"><?php echo esc_html__( 'Submit', 'wrhhub' ); ?></button>
																<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Close', 'wrhhub' ); ?></button>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr class="notes-collapse">
									<td colspan="12">
										<div class="notes-collapse__body collapse" id="notes-<?php echo esc_html( $order_id ); ?>">
											<div class="row">
												<div class="col-xl-12 col-lg-12 d-flex">
													<div class="notes-collapse__body-item order_pre_section">
														<?php
														$curved_array  = array(
															'Curved With No Strapping',
															'Curved With Strapping',
															'Curved With Brass Strapping',
															'Curved With Stainless Steel Strapping',
														);
														$taperd_array  = array( 'Tapered Straight', 'Tapered Shiplap', 'Tapered With Strapping' );
														$sloped_array  = array( 'Sloped No Strapping', 'Sloped Strapping' );
														$angled_array  = array(
															'Angled No Strapping',
															'Angled With Strapping',
															'Angled With Walnut Band',
														);
														$customer_note = get_post_meta( get_the_ID(), 'customer_note', true );
														foreach ( $line_items['line_items'] as $key => $s_item ) {
															$product_name   = explode( ' ', trim( $s_item['product_name'] ) )[0];
															$trim_options   = explode( ' ', trim( $s_item['trim_options']['value'] ) )[0];
															$size           = preg_match( '/([0-9]+)/', $s_item['size']['key'], $height_width );
															$increase_depth = preg_match( '/([0-9]+)\.([0-9]+)/', $s_item['increase_depth']['value'], $depth );
															$reduce_height  = preg_match( '~(?|([^"]*)"|\'([^\']*)\')~', $s_item['reduce_height']['value'], $reduce );
															$crown_molding  = explode( ' ', trim( $s_item['crown_molding']['value'] ) )[0];
															$extend_chimney = explode( ' ', trim( $s_item['extend_chimney']['value'] ) )[0];
															$solid_button   = explode( ' ', trim( $s_item['solid_button']['value'] ) )[0];
															$solid_button   = 'Yes' === $solid_button ? 'Solid Button' : 'Z-Line';
															$hoods_color    = $s_item['color']['value'];
															// echo '<p>' . esc_html( $trim_options ) . '</p>';
															// echo '<p>Hood Width: ' . esc_html( $height_width[0] ) . '</p>';
															// echo '<p>Hood Height: ' . esc_html( $height_width[1] ) . '</p>';
															// echo '<p>Hood Depth: ' . esc_html( isset($depth[0]) ? $depth[0] : '' ) . '</p>';
														}

														if ( in_array( $line_items['line_items'][0]['product_name'], $curved_array, true ) ) {
															$hood_style = 'Charleston';
														} elseif ( in_array( $line_items['line_items'][0]['product_name'], $taperd_array, true ) ) {
															$hood_style = 'Manchester';
														} elseif ( 'Box With Trim' === $line_items['line_items'][0]['product_name'] ) {
															$hood_style = 'Belfast';
														} elseif ( in_array( $line_items['line_items'][0]['product_name'], $sloped_array, true ) ) {
															$hood_style = 'Venice';
														} elseif ( in_array( $line_items['line_items'][0]['product_name'], $angled_array, true ) ) {
															$hood_style = 'London';
														} else {
															$hood_style = '';
														}
														?>
														<div class="product_style_info">
															<div class="syle_finish_sec">
																<h4 class="style_head"><?php esc_html_e( 'Hood Style', 'wrhhub' ); ?></h4>
																<p><?php esc_html_e( 'Wood Species: Maple', 'wrhhub' ); ?></p>
																<p><?php esc_html_e( 'Hood Style:', 'wrhhub' ); ?><?php echo esc_html( $hood_style ); ?></p>
															<?php
															foreach ( $line_items['line_items'] as $key => $value ) {
																echo '<p>Apron Style: ' . esc_html( $value['trim_options']['value'] ) . '</p>';
															}
															?>
																<p><?php esc_html_e( 'Chimney Style:', 'wrhhub' ); ?><?php echo 'No Strapping'; ?></p>
																<?php
																foreach ( $line_items['line_items'] as $key => $value ) {
																	preg_match_all( '!\d+!', $value['size']['value'], $matches );
																	echo '<p>' . esc_html__( 'Hood Width: ' . $matches[0][0] . '', 'wrhhub' ) . '</p>';
																	echo '<p>' . esc_html__( 'Hood Height: ' . $matches[0][1] . '', 'wrhhub' ) . '</p>';
																}
																?>
																<?php
																foreach ( $line_items['line_items'] as $key => $value ) {
																	preg_match_all( '!\d+!', $value['size']['value'], $matches );
																	echo '<p>' . esc_html__( 'Hood Depth: ' . $value['increase_depth']['value'] . '', 'wrhhub' ) . '</p>';
																}
																?>
															</div>
															<div class="syle_finish_sec">
																<h4 class="style_head"><?php esc_html_e( 'Hood Finish', 'wrhhub' ); ?></h4>
																<p><?php esc_html_e( 'Hood Finish Grade: Raw', 'wrhhub' ); ?></p>
																<p><?php esc_html_e( 'Chimney Color:', 'wrhhub' ); ?><?php echo 'Raw'; ?></p>
																<p><?php esc_html_e( 'Apron Color:', 'wrhhub' ); ?><?php echo 'Raw'; ?></p>
																<h4 class="style_head"><?php esc_html_e( 'What needs to be replaced', 'wrhhub' ); ?></h4>
																<?php
																if ( 'Wood Hoods' === $damage_item || 'Island Wood Hoods' === $damage_item ) :
																	?>
																	<p><?php echo esc_html( $hood_replace ); ?></p>
																<?php elseif ( 'Floating Shelves' === $damage_item ) : ?>
																	<p><?php echo esc_html( $f_shelf_replace ); ?></p>
																<?php elseif ( 'Hall Trees' === $damage_item ) : ?>
																	<p><?php echo esc_html( $hall_tree_replace ); ?></p>
																<?php else : ?>
																	<p><?php echo esc_html( $no_replace ); ?></p>
																<?php endif; ?>
															</div>
														</div>
														<div class="product_style_info">
															<div class="syle_finish_sec">
																<h4 class="style_head"><?php esc_html_e( 'Ventilation Information', 'wrhhub' ); ?></h4>
																<?php
																foreach ( $line_items['line_items'] as $key => $s_item ) {
																	echo '<p>Liner: ' . esc_html( $s_item['tradewinds_sku'] ) . '</p>';
																}
																?>
																<p><?php esc_html_e( 'Duct Kit:', 'wrhhub' ); ?><?php echo '5BL-39-400-DCK'; ?></p>
																<p><?php esc_html_e( 'Recircuiting Vent Slots:', 'wrhhub' ); ?><?php echo 'Yes'; ?></p>
																<h4 class="style_head"><?php esc_html_e( 'Modifications', 'wrhhub' ); ?></h4>
																<p><?php esc_html_e( 'Reduced Height: ' . $reduce_height . '', 'wrhhub' ); ?></p>
																<p><?php esc_html_e( 'Molding/Strapping:', 'wrhhub' ); ?><?php echo 'All Molding Loose'; ?></p>
															</div>
															<div class="syle_finish_sec">
																<h4 class="style_head"><?php esc_html_e( 'Accessories', 'wrhhub' ); ?></h4>
																<p><?php esc_html_e( 'Crown Molding: Crown Molding Loose', 'wrhhub' ); ?></p>
																<h4 class="style_head"><?php esc_html_e( 'Addiotional Notes', 'wrhhub' ); ?></h4>
																<p><?php echo esc_html( $customer_note ); ?></p>
															</div>
														</div>

													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<?php
						}
						// } // end  if
					} // end while
				} // end if
				wp_reset_postdata();
				?>
				</tbody>
			</table>
			<div class="hub-pagination" id="wrhPaginate" data-max_num_pages="<?php echo intval( $all_orders->max_num_pages ); ?>">
				<?php
				shop_pagination( $hub_paged, $all_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
