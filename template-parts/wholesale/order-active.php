<div class="col-xl-12">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header" style="display: flex;">
				<h6 class="title"><?php esc_html_e( 'Incoming Orders', 'wrhhub' ); ?></h6>
				<div class="bulk_edit">
					<button type="submit" class="btn btn-warning"><?php esc_html_e( 'Bulk Edit', 'wrhhub' ); ?></button>
				</div>
				<div class="order_change_bulk">
					<select class="form-control select_status" name="" id="">
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
					<th scope="col"><?php esc_html_e( 'PO Number', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Status', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Source', 'wrhhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Is Priority?', 'wrhhub' ); ?></th>
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
							array(
								'key'     => 'origin',
								'value'   => 'USCD',
								'compare' => 'LIKE',
							),
							array(
								'key'     => 'order_status',
								'value'   => 'Pre-Assembly',
								'compare' => 'NOT LIKE',
							),
						),
					),
				);
				$all_orders             = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {
					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$bill_of_landing_id          = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
						$shipping_add                = get_post_meta( get_the_ID(), 'shipping', true );
						$shipping                    = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
						$first_name                  = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
						$last_name                   = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
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
						$damage_item                 = get_post_meta( get_the_ID(), 'damage_item', true );
						$hood_replace                = get_post_meta( get_the_ID(), 'hood_replace', true );
						$f_shelf_replace             = get_post_meta( get_the_ID(), 'f_shelf_replace', true );
						$hall_tree_replace           = get_post_meta( get_the_ID(), 'hall_tree_replace', true );
						$no_replace                  = get_post_meta( get_the_ID(), 'no_replace', true );
						$bol_link                    = get_post_meta( get_the_ID(), 'bol_pdf', true );
						$shipping_file_link          = get_post_meta( get_the_ID(), 'shipping_label', true );
						$backgroundg_color           = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
						$bol_regenerated             = get_post_meta( get_the_ID(), 'bol_regenerated', true );
						$checked                     = ( 'yes' === $is_priority ) ? 'checked' : '';
						$disabled                    = '';
						if ( 'rush_my_order' === $is_priority ) {
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

						$req_stock_quantity = get_post_meta( get_the_ID(), 'req_stock_quantity', true );
						$size_attr          = get_post_meta( get_the_ID(), 'size_attr', true );
						//$po_number          = get_post_meta( get_the_ID(), 'size_attr', true );
						?>
								
								<tr style="background-color: #4747471a;">
									<td data-title="Order Id">
										<input type="checkbox" class="bulk_check" value="test" data-ordersource="<?php echo $origin; ?>" data-orderid="<?php echo esc_html( $order_id ); ?>"
											   data-postid="<?php echo get_the_ID();?>
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
									<td data-title="PO Number">PO435245624563</td>
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
									<td data-title="Shipping Address">
										<?php
										echo esc_html( $address_1 ) . ' ' . esc_html( $city ) . ' ' . esc_html( $state ) . ' ' . esc_html( $postcode );
										?>
									</td>
									<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right">
										<button class="btn btn-violet"><a
													href="<?php echo esc_url( $bol_link ); ?>"><?php esc_html_e( 'View', 'wrhhub' ); ?><span
														class="<?php echo ! empty( $bol_regenerated ) && 'yes' === $bol_regenerated ? 'red_circle' : ''; ?>"></span></a>
										</button>
									</td>
									<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right">
										<button class="btn btn-violet"><a
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
											<?php if ( current_user_can( 'administrator' ) ) : ?>
												<li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order"
													   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_delete_order_nonce' ) ); ?>"><?php echo esc_html__( 'Delete', 'wrhhub' ); ?></a>
												</li>
											<?php endif ?>
										</ul>
									</td>
								</tr>
								<tr class="notes-collapse">
									<td colspan="12">
										<div class="notes-collapse__body collapse" id="notes-<?php echo esc_html( $order_id ); ?>">
											<div class="row">
												<div class="col-xl-12 col-lg-12 d-flex">
													<div class="notes-collapse__body-item order_pre_section">
														<div class="product_style_info" style="height:100% !important;">
															<div class="syle_finish_sec" >
																<h4 class="style_head"><?php esc_html_e( 'Hood Style', 'wrhhub' ); ?></h4>
																<?php
																$i = 1;
																foreach ( $line_items['line_items'] as $key => $value ) {
																	echo '<h5>Item '.$i.'</h5>';
																	echo '<p>USCD Sku: '.esc_html( $value['uscd_item_id'] ).'</p>';
																	echo '<p>Wood Species: '.esc_html( $value['wood_species']['value'] ).'</p>';
																	echo '<p>Hood Style: ' . esc_html( $value['hood_style']['value'] ) . '</p>';
																	echo '<p>Apron Style: ' . esc_html( $value['apron_style']['value'] ) . '</p>';
																	echo '<p>Chimney Style: ' . esc_html( $value['chimney_style']['value'] ) . '</p>';
																	echo '<p>Hood Width: ' . esc_html( $value['hood_width']['value'] ) . '</p>';
																	echo '<p>Hood Height: ' . esc_html( $value['hood_height']['value'] ) . '</p>';
																	echo '<p>Hood Depth: ' . esc_html( $value['hood_depth']['value'] ) . '</p>';
																	$i++;
																}
																?>
																
															</div>
															<div class="syle_finish_sec">
																<h4 class="style_head"><?php esc_html_e( 'Hood Finish', 'wrhhub' ); ?></h4>
																<?php
																$i = 1;
																foreach ( $line_items['line_items'] as $key => $value ) {
																	echo '<h5>Item '.$i.'</h5>';
																	echo '<p>Hood Finish Grade: '.esc_html( $value['hood_finish_grade']['value'] ).'</p>';
																	echo '<p>Chimney Color: '.esc_html( $value['chimney_color']['value'] ).'</p>';
																	echo '<p>Apron Color: '.esc_html( $value['apron_color']['value'] ).'</p>';
																	$i++;
																}
																?>
															</div>
														</div>
														<div class="product_style_info">
															<div class="syle_finish_sec">
																<h4 class="style_head"><?php esc_html_e( 'Ventilation Information', 'wrhhub' ); ?></h4>
																<?php
																$i = 1;
																foreach ( $line_items['line_items'] as $key => $value ) {
																	echo '<h5>Item '.$i.'</h5>';
																	echo '<p>Liner: '.esc_html( $value['liner']['value'] ).'</p>';
																	echo '<p>Blower: '.esc_html( $value['blower']['value'] ).'</p>';
																	echo '<p>Duct Kit: '.esc_html( $value['duct_kit']['value'] ).'</p>';
																	$i++;
																}
																?>
																
															</div>
															<div class="syle_finish_sec">
																<h4 class="style_head"><?php esc_html_e( 'Accessories', 'wrhhub' ); ?></h4>
															</div>
														</div>

													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<?php
							
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
