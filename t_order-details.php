<?php
/*
Template Name: Orders Details
*/
$order_id     = null;
$post_id      = null;
$edit         = null;
$shippingEdit = null;
if ( isset( $_GET['order_id'] ) && $_GET['order_id'] ) {
	$order_id = esc_html( $_GET['order_id'] );
	$post_id  = intval( $_GET['post_id'] );
}
if ( isset( $_GET['view'] ) && $_GET['view'] ) {
	$edit = $_GET['view'];
}

if ( isset( $_GET['shipping'] ) && $_GET['shipping'] ) {
	$shippingEdit = $_GET['shipping'];
}
$order_link = get_template_link( 't_order.php' );
if ( $order_link && $order_id == null ) {
	header( 'HTTP/1.1 301 Moved Permanently' );
	header( 'Location: ' . $order_link );
	exit();
}
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-10">
				<div class="col-xl-8 last-none">

					<div class="row lr-10">
						<div class="col-xl-12 col-lg-12">
							<div class="dashboard-page__order-detail">
								<div class="dashboard-page__order-detail-header">
									<div class="navbar-header">
										<?php if ( 'edit' === $edit ) : ?>
											<h6 class="title">Edit Order #<?php echo esc_html( $order_id ); ?> Details</h6>
										<?php else : ?>
											<h6 class="title">Order #<?php echo esc_html( $order_id ); ?> Details</h6>
										<?php endif; ?>
									</div>

									<div class="navbar-collapse">
										<ul class="navbar-nav ml-auto">
											<li><a href="#" class="icon-setting-sliders"></a></li>
										</ul>
									</div>
								</div>
								<?php if ( $edit === 'edit' ) : ?>
									<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
										<form method="post" action="">
											<?php
											$line_items = get_post_meta( $post_id, 'line_items', true );

											foreach ( $line_items['line_items'] as $line_item ) :
												?>
												<div class="product-info">
													<figure class="media">
														<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( $line_item['product_img_url'] ), esc_html( $line_item['product_name'] ) ); ?>
													</figure>
													<div class="text">
														<ul class="list-style mb-3 edit-product">
															<?php $meta_data_arr = $line_item['order_meta']; ?>
															<?php
															if ( is_array( $meta_data_arr ) ) {
																foreach ( $meta_data_arr as $value ) {
																	$meta_value = str_replace(
																		array( '<p>', '</p>' ),
																		array(
																			'',
																			'',
																		),
																		html_entity_decode( $value['display_value'] )
																	);
																	?>
																	<li>
																		<strong><?php echo $value['display_key']; ?></strong>
																		<span>:
														<select name="product-type" class="order-select d-none" id="product-type">
															<option value="<?php echo $value['key']; ?>"><?php echo $meta_value; ?></option>

														</select>
													</span>
																	</li>
																	<?php
																}
															}
															?>

														</ul>
													</div>
												</div>
											<?php endforeach; ?>
											<div class="total-price">Total: $<?php echo $line_items['order_total']; ?></div>
											<div style="display: flex;justify-content: end;margin-top: 10px;">
												<button class="btn btn-bluedark" style="background: #AA4098; color: #fff">Update</button>
											</div>
										</form>
									</div>
								<?php else : ?>
									<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
										<?php
										$line_items        = get_post_meta( $post_id, 'line_items', true );
										$damage_item       = get_post_meta( $post_id, 'damage_item', true );
										$hood_replace      = get_post_meta( $post_id, 'hood_replace', true );
										$f_shelf_replace   = get_post_meta( $post_id, 'f_shelf_replace', true );
										$hall_tree_replace = get_post_meta( $post_id, 'hall_tree_replace', true );
										$no_replace        = get_post_meta( $post_id, 'no_replace', true );
										$miscellaneous     = get_post_meta( $post_id, 'miscellaneous', true );
										$origin            = get_post_meta( $post_id, 'origin', true );

										foreach ( $line_items['line_items'] as $line_item ) :
											?>
											<div class="product-info">
												<figure class="media">
													<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( $line_item['product_img_url'] ), esc_html( $line_item['product_name'] ) ); ?>
												</figure>
												<div class="text">
													<ul class="list-style">
														<?php if ( 'USCD' === $origin ) { ?>
															<li>
																<strong>Product Name</strong>
																<span>: USCD Order</span>
															</li>
														<?php } else { ?>
														<li>
															<strong>Product Name</strong>
															<span>: <?php echo esc_html( $line_item['product_name'] ); ?></span>
														</li>
														<?php } ?>
														<li>
															<strong>Product ID</strong>
															<span>: #<?php echo intval( $line_item['product_id'] ); ?></span>
														</li>
														<?php if ( isset($line_item['uscd_item_id']) ? isset($line_item['uscd_item_id']) : array()  ) : ?>
															<?php if ( $line_item['uscd_item_id'] ) : ?>
																<li><strong><?php esc_html_e( 'USCD Sku', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['uscd_item_id'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['wood_species']) ? isset($line_item['wood_species']) : array()  ) : ?>
															<?php if ( $line_item['wood_species'] ) : ?>
																<li><strong><?php esc_html_e( 'Wood Species', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['wood_species']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['hood_style']) ? isset($line_item['hood_style']) : array()  ) : ?>
															<?php if ( $line_item['hood_style'] ) : ?>
																<li><strong><?php esc_html_e( 'Hood Style', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['hood_style']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['apron_style']) ? isset($line_item['apron_style']) : array()  ) : ?>
															<?php if ( $line_item['apron_style'] ) : ?>
																<li><strong><?php esc_html_e( 'Apron Style', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['apron_style']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['chimney_style']) ? isset($line_item['chimney_style']) : array()  ) : ?>
															<?php if ( $line_item['chimney_style'] ) : ?>
																<li><strong><?php esc_html_e( 'Chimney Style', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['chimney_style']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['hood_width']) ? isset($line_item['hood_width']) : array()  ) : ?>
															<?php if ( $line_item['hood_width'] ) : ?>
																<li><strong><?php esc_html_e( 'Hood Width', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['hood_width']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['hood_height']) ? isset($line_item['hood_height']) : array()  ) : ?>
															<?php if ( $line_item['hood_height'] ) : ?>
																<li><strong><?php esc_html_e( 'Hood Height', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['hood_height']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['hood_depth']) ? isset($line_item['hood_depth']) : array()  ) : ?>
															<?php if ( $line_item['hood_depth'] ) : ?>
																<li><strong><?php esc_html_e( 'Hood Depth', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['hood_depth']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['hood_finish_grade']) ? isset($line_item['hood_finish_grade']) : array()  ) : ?>
															<?php if ( $line_item['hood_finish_grade'] ) : ?>
																<li><strong><?php esc_html_e( 'Hood Finish Grade', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['hood_finish_grade']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['chimney_color']) ? isset($line_item['chimney_color']) : array()  ) : ?>
															<?php if ( $line_item['chimney_color'] ) : ?>
																<li><strong><?php esc_html_e( 'Chimney Color', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['chimney_color']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['apron_color']) ? isset($line_item['apron_color']) : array()  ) : ?>
															<?php if ( $line_item['apron_color'] ) : ?>
																<li><strong><?php esc_html_e( 'Apron Color', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['apron_color']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['liner']) ? isset($line_item['liner']) : array()  ) : ?>
															<?php if ( $line_item['liner'] ) : ?>
																<li><strong><?php esc_html_e( 'Liner', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['liner']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['blower']) ? isset($line_item['blower']) : array()  ) : ?>
															<?php if ( $line_item['blower'] ) : ?>
																<li><strong><?php esc_html_e( 'Blower', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['blower']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>
														<?php if ( isset($line_item['duct_kit']) ? isset($line_item['duct_kit']) : array()  ) : ?>
															<?php if ( $line_item['duct_kit'] ) : ?>
																<li><strong><?php esc_html_e( 'Duct Kit', 'hoodslyhub' ); ?></strong>
																	<span>: <?php echo esc_html( $line_item['duct_kit']['value'] ); ?> </span>
																</li>
															<?php endif; ?>
														<?php endif; ?>

														<?php if ( $line_item['color']['value'] && $line_item['color']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Color', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( $line_item['color']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['size']['value'] && $line_item['size']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Size', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['size']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['trim_options']['value'] && $line_item['trim_options']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Trim Options', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['trim_options']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['remove_trim']['value'] && $line_item['remove_trim']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'How Would You Like Your Trim?', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['remove_trim']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['crown_molding']['value'] && $line_item['crown_molding']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Crown Molding', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['crown_molding']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['increase_depth']['value'] && $line_item['increase_depth']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Increase Depth', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['increase_depth']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['reduce_height']['value'] && $line_item['reduce_height']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Reduce Height', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['reduce_height']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['extend_chimney']['value'] && $line_item['extend_chimney']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Extend Your Chimney', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['extend_chimney']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['solid_button']['value'] && $line_item['solid_button']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Add A Solid Bottom', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['solid_button']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['rush_my_order']['value'] && $line_item['rush_my_order']['key'] !== 'empty' ) : ?>
														<li><strong><?php esc_html_e( 'Rush Manufacturing', 'hoodslyhub' ); ?></strong>
															<span>: <?php echo( $line_item['rush_my_order']['value'] ); ?> </span>
														</li>
														<?php endif; ?>
														<?php
														if ( 'Wood Hoods' === $damage_item || 'Island Wood Hoods' === $damage_item || 'Quick Shipping' === $damage_item ) :
															?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $hood_replace ) ); ?> </span>
															</li>
														<?php elseif ( 'Floating Shelves' === $damage_item ) : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $f_shelf_replace ) ); ?> </span>
															</li>
														<?php elseif ( 'Hall Trees' === $damage_item ) : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $hall_tree_replace ) ); ?> </span>
															</li>
														<?php else : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $no_replace ) ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( 'miscellaneous' === $hood_replace || 'miscellaneous' === $f_shelf_replace || 'miscellaneous' === $hall_tree_replace ) : ?>
															<li><strong><?php esc_html_e( 'Miscellaneous', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( $miscellaneous ); ?> </span>
															</li>
														<?php endif; ?>

													</ul>
												</div>
											</div>
										<?php endforeach; ?>
										<div class="total-price">Total: $<?php echo $line_items['order_total']; ?></div>
									</div>
								<?php endif; ?>
							</div>
							<?php
								$edit_order_items = get_post_meta( $post_id, 'edited_line_items', true );
							if ( ! empty( $edit_order_items ) ) {
								?>
									<div class="dashboard-page__order-detail">
								<div class="dashboard-page__order-detail-header">
									<div class="navbar-header">
									<?php if ( 'edit' === $edit ) : ?>
											<h6 class="title">Edit Order #<?php echo esc_html( $order_id ); ?> Details</h6>
										<?php else : ?>
											<h6 class="title">Order #<?php echo esc_html( $order_id ); ?> Details</h6>
											<button type="submit" class="btn btn-success approve_new_changes">Approve New Chnages</button>
											<button type="submit" class="btn btn-warning deny_new_changes" data-postid="<?php echo $post_id; ?>" data-orderid="<?php echo $order_id; ?>">Deny New Changes</button>
										<?php endif; ?>
									</div>

									<div class="navbar-collapse">
										<ul class="navbar-nav ml-auto">
											<li><a href="#" class="icon-setting-sliders"></a></li>
										</ul>
									</div>
								</div>
								<?php if ( $edit === 'edit' ) : ?>
									<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
										<form method="post" action="">
											<?php
											$line_items = get_post_meta( $post_id, 'line_items', true );

											foreach ( $edit_order_items['line_items'] as $line_item ) :
												?>
												<div class="product-info">
													<figure class="media">
														<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( $line_item['product_img_url'] ), esc_html( $line_item['product_name'] ) ); ?>
													</figure>
													<div class="text">
														<ul class="list-style mb-3 edit-product">
															<?php $meta_data_arr = $line_item['order_meta']; ?>
															<?php
															if ( is_array( $meta_data_arr ) ) {
																foreach ( $meta_data_arr as $value ) {
																	$meta_value = str_replace(
																		array( '<p>', '</p>' ),
																		array(
																			'',
																			'',
																		),
																		html_entity_decode( $value['display_value'] )
																	);
																	?>
																	<li>
																		<strong><?php echo $value['display_key']; ?></strong>
																		<span>:
														<select name="product-type" class="order-select d-none" id="product-type">
															<option value="<?php echo $value['key']; ?>"><?php echo $meta_value; ?></option>

														</select>
													</span>
																	</li>
																	<?php
																}
															}
															?>

														</ul>
													</div>
												</div>
											<?php endforeach; ?>
											<div class="total-price">Total: $<?php echo $edit_order_items['order_total']; ?></div>
											<div style="display: flex;justify-content: end;margin-top: 10px;">
												<button class="btn btn-bluedark" style="background: #AA4098; color: #fff">Update</button>
											</div>
										</form>
									</div>
								<?php else : ?>
									<div class="dashboard-page__order-detail-body dashboard-page__order-detail-body-2">
										<?php
										$edit_order_items  = get_post_meta( $post_id, 'edited_line_items', true );
										$damage_item       = get_post_meta( $post_id, 'damage_item', true );
										$hood_replace      = get_post_meta( $post_id, 'hood_replace', true );
										$f_shelf_replace   = get_post_meta( $post_id, 'f_shelf_replace', true );
										$hall_tree_replace = get_post_meta( $post_id, 'hall_tree_replace', true );
										$no_replace        = get_post_meta( $post_id, 'no_replace', true );
										$miscellaneous     = get_post_meta( $post_id, 'miscellaneous', true );

										foreach ( $edit_order_items['line_items'] as $line_item ) :
											?>
											<div class="product-info">
												<figure class="media">
													<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( $line_item['product_img_url'] ), esc_html( $line_item['product_name'] ) ); ?>
												</figure>
												<div class="text">
													<ul class="list-style">
														<li>
															<strong>Product Name</strong>
															<span>: <?php echo esc_html( $line_item['product_name'] ); ?></span>
														</li>
														<li>
															<strong>Product ID</strong>
															<span>: #<?php echo intval( $line_item['product_id'] ); ?></span>
														</li>
														<?php if ( $line_item['color']['value'] && $line_item['color']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Color', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( $line_item['color']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['size']['value'] && $line_item['size']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Size', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['size']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['trim_options']['value'] && $line_item['trim_options']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Trim Options', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['trim_options']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['remove_trim']['value'] && $line_item['remove_trim']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'How Would You Like Your Trim?', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['remove_trim']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['crown_molding']['value'] && $line_item['crown_molding']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Crown Molding', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['crown_molding']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['increase_depth']['value'] && $line_item['increase_depth']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Increase Depth', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['increase_depth']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['reduce_height']['value'] && $line_item['reduce_height']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Reduce Height', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['reduce_height']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['extend_chimney']['value'] && $line_item['extend_chimney']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Extend Your Chimney', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['extend_chimney']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['solid_button']['value'] && $line_item['solid_button']['key'] !== 'empty' ) : ?>
															<li><strong><?php esc_html_e( 'Add A Solid Bottom', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo( $line_item['solid_button']['value'] ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( $line_item['rush_my_order']['value'] && $line_item['rush_my_order']['key'] !== 'empty' ) : ?>
														<li><strong><?php esc_html_e( 'Rush Manufacturing', 'hoodslyhub' ); ?></strong>
															<span>: <?php echo( $line_item['rush_my_order']['value'] ); ?> </span>
														</li>
														<?php endif; ?>
														<?php
														if ( 'Wood Hoods' === $damage_item || 'Island Wood Hoods' === $damage_item || 'Quick Shipping' === $damage_item ) :
															?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $hood_replace ) ); ?> </span>
															</li>
														<?php elseif ( 'Floating Shelves' === $damage_item ) : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $f_shelf_replace ) ); ?> </span>
															</li>
														<?php elseif ( 'Hall Trees' === $damage_item ) : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $hall_tree_replace ) ); ?> </span>
															</li>
														<?php else : ?>
															<li><strong><?php esc_html_e( 'What needs to be replaced', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( ucfirst( $no_replace ) ); ?> </span>
															</li>
														<?php endif; ?>
														<?php if ( 'miscellaneous' === $hood_replace || 'miscellaneous' === $f_shelf_replace || 'miscellaneous' === $hall_tree_replace ) : ?>
															<li><strong><?php esc_html_e( 'Miscellaneous', 'hoodslyhub' ); ?></strong>
																<span>: <?php echo esc_html( $miscellaneous ); ?> </span>
															</li>
														<?php endif; ?>

													</ul>
												</div>
											</div>
										<?php endforeach; ?>
										<div class="total-price" style="color: white;">Total: $<?php echo $edit_order_items['order_total']; ?></div>
									</div>
								<?php endif; ?>
							</div>
								<?php
							}
							?>
						</div>
						<?php
						$billing    = get_post_meta( $post_id, 'billing', true );
						$first_name = ( isset( $billing['first_name'] ) && ! empty( $billing['first_name'] ) ) ? $billing['first_name'] : '';
						$last_name  = ( isset( $billing['last_name'] ) && ! empty( $billing['last_name'] ) ) ? $billing['last_name'] : '';
						$phone      = ( isset( $billing['phone'] ) && ! empty( $billing['phone'] ) ) ? $billing['phone'] : '';
						$email      = ( isset( $billing['email'] ) && ! empty( $billing['email'] ) ) ? $billing['email'] : '';
						$address_1  = ( isset( $billing['address_1'] ) && ! empty( $billing['address_1'] ) ) ? $billing['address_1'] : '';
						$address_2  = ( isset( $billing['address_2'] ) && ! empty( $billing['address_2'] ) ) ? $billing['address_2'] : '';
						$city       = ( isset( $billing['city'] ) && ! empty( $billing['city'] ) ) ? $billing['city'] : '';
						$state      = ( isset( $billing['first_name'] ) && ! empty( $billing['first_name'] ) ) ? $billing['first_name'] : '';
						$state      = ( isset( $billing['state'] ) && ! empty( $billing['state'] ) ) ? $billing['state'] : '';
						$postcode   = ( isset( $billing['postcode'] ) && ! empty( $billing['postcode'] ) ) ? $billing['postcode'] : '';
						?>
						<div class="col-xl-6 col-lg-12">
							<div class="dashboard-page__shipping-address">
								<div class="dashboard-page__shipping-address-header">
									<div class="navbar-header">
										<h6 class="title"><?php esc_attr_e( 'Shipping Address', 'hoodslyhub' ); ?></h6>
									</div>
								</div>
								<?php
								if ( isset( $_POST['save_bol_shipping'] ) && ! empty( $_POST ) ) {

									$name_arr    = explode( ' ', $_POST['shipping-name'] );
									$firstname   = isset( $name_arr[0] ) ? $name_arr[0] : '';
									$lastname    = isset( $name_arr[1] ) ? $name_arr[1] : '';
									$address_arr = array(
										'first_name' => $firstname,
										'last_name'  => $lastname,
										'phone'      => $_POST['shipping-phone'],
										'email'      => $_POST['shipping-email'],
										'address_1'  => $_POST['shipping-address-one'],
										'address_2'  => $_POST['shipping-address-two'],
										'city'       => $_POST['shipping-city'],
										'state'      => $_POST['shipping-state'],
										'postcode'   => $_POST['shipping-postcode'],
										'country'    => 'US',
									);
									update_post_meta( $post_id, 'shipping', $address_arr );
								}
								$shipping_add = get_post_meta( $post_id, 'shipping', true );
								$shipping     = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
								if ( $shipping ) :
									$s_first_name = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
									$s_last_name  = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
									$s_address_1  = ( isset( $shipping['address_1'] ) && ! empty( $shipping['address_1'] ) ) ? $shipping['address_1'] : '';
									$s_address_2  = ( isset( $shipping['address_2'] ) && ! empty( $shipping['address_2'] ) ) ? $shipping['address_2'] : '';
									$s_city       = ( isset( $shipping['city'] ) && ! empty( $shipping['city'] ) ) ? $shipping['city'] : '';
									$s_state      = ( isset( $shipping['state'] ) && ! empty( $shipping['state'] ) ) ? $shipping['state'] : '';
									$s_postcode   = ( isset( $shipping['postcode'] ) && ! empty( $shipping['postcode'] ) ) ? $shipping['postcode'] : '';
									?>
									<?php if ( 'shipping_edit' === $shippingEdit ) { ?>
									<form action="" method="POST" name="save_ashipping_bol" id="save_ashipping_bol">
										<div class="dashboard-page__shipping-address-body">
											<input class="form-control" type="text" name="shipping-name" id="shipping-name" placeholder="Name" value="<?php echo esc_html( $s_first_name ) . ' ' . esc_html( $s_last_name ); ?>">
											<input class="form-control" type="number" name="shipping-phone" id="shipping-phone" placeholder="Phone" value="<?php echo esc_html( $phone ); ?>">
											<input class="form-control" type="text" name="shipping-email" id="shipping-email" placeholder="Email" value="<?php echo esc_html( $email ); ?>">
											<input class="form-control" type="text" name="shipping-address-one" id="shipping-address-one" placeholder="Address Line One" value="<?php echo esc_html( $s_address_1 ); ?>">
											<input class="form-control" type="text" name="shipping-address-two" id="shipping-address-two" placeholder="Address Line Two" value="<?php echo esc_html( $s_address_2 ); ?>">
											<input class="form-control" type="text" name="shipping-city" id="shipping-city" placeholder="Shipping City" value="<?php echo esc_html( $s_city ); ?>">
											<input class="form-control" type="text" name="shipping-state" id="shipping-state" placeholder="Shipping State" value="<?php echo esc_html( $s_state ); ?>">
											<input class="form-control" type="text" name="shipping-postcode" id="shipping-postcode" placeholder="Shipping Postcode" value="<?php echo esc_html( $s_postcode ); ?>">
											<button class="btn btn-submit" name="save_bol_shipping">
											<?php
											esc_html_e( 'Save Change', 'hoodslyhub' );
											?>
												</button>
										</div>
									</form>
								<?php } else { ?>
									<div class="dashboard-page__shipping-address-body">
										<div class="name"><?php echo esc_html( $s_first_name ) . ' ' . esc_html( $s_last_name ); ?></div>
										<div class="phone"><?php echo esc_html( $phone ); ?></div>
										<div class="email"><a
													href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
										</div>
										<div class="address"><?php echo esc_html( $s_address_1 ); ?> <br><?php echo esc_html( $s_city ); ?>
											<br> <?php echo esc_html( $s_state ); ?> <br><?php echo esc_html( $s_postcode ); ?>
										</div>
									</div>
								<?php } ?>
								<?php else : ?>
									<div class="dashboard-page__shipping-address-body">
										<div class="name"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></div>
										<div class="phone"><?php echo esc_html( $phone ); ?></div>
										<div class="email"><a
													href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
										</div>
										<div class="address"><?php echo esc_html( $address_1 ); ?> <br><?php echo esc_html( $city ); ?>
											<br> <?php echo esc_html( $state ); ?> <br><?php echo esc_html( $postcode ); ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-xl-6 col-lg-12">
							<div class="dashboard-page__shipping-address">
								<div class="dashboard-page__shipping-address-header">
									<div class="navbar-header">
										<h6 class="title"><?php esc_attr_e( 'Billing Address', 'hoodslyhub' ); ?></h6>
									</div>
								</div>

								<div class="dashboard-page__shipping-address-body">
									<div class="name"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></div>
									<div class="phone"><?php echo esc_html( $phone ); ?></div>
									<div class="email"><a href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a>
									</div>
									<div class="address"><?php echo esc_html( $address_1 ); ?> <br><?php echo esc_html( $city ); ?>
										<br> <?php echo esc_html( $state ); ?> <br><?php echo esc_html( $postcode ); ?>
									</div>
								</div>
							</div>
						</div>

						<?php get_template_part( 'template-parts/order', 'communication' ); ?>
					</div>
				</div>
				<div class="col-xl-4">
					<div class="dashboard-page__order">
						<div class="dashboard-page__order-header">
							<div class="navbar-header">
								<h6 class="title">Actions</h6>
							</div>

							<div class="navbar-collapse">
								<ul class="navbar-nav ml-auto">
									<li><a href="#" class="icon-setting-sliders"></a></li>
								</ul>
							</div>
						</div>

						<div class="dashboard-page__order-body">
							<ul class="action-list list-unstyled">
								<?php
								global $wp;
								$origin            = get_post_meta( $post_id, 'origin', true );
								$order_id          = get_post_meta( $post_id, 'order_id', true );
								$billing           = get_post_meta( $post_id, 'billing', true );
								$email             = ( isset( $billing['email'] ) && ! empty( $billing['email'] ) ) ? $billing['email'] : '';
								$tracking_number   = get_post_meta( $post_id, '_aftership_tracking_number', true );
								$tracking_provider = get_post_meta( $post_id, '_aftership_tracking_provider_name', true );
								//$order_edit_link    = get_template_link( 't_order-edit.php' );
								$current_url = home_url( $wp->request );
								$edit_url    = add_query_arg(
									array(
										'view'     => 'edit',
										'post_id'  => $post_id,
										'order_id' => $order_id,
									),
									$current_url
								);
								//$shipping_edit_link = get_template_link( 't_shipping-edit.php' );
								$shipping_edit_url      = add_query_arg(
									array(
										'shipping' => 'shipping_edit',
										'post_id'  => $post_id,
										'order_id' => $order_id,
									),
									$current_url
								);
								$is_tradewinds_selected = get_post_meta( $post_id, 'is_tradewinds_selected', true );
								if ( $is_tradewinds_selected !== 'yes' ) {
									?>
									<li>
										<a href="" data-orderid="<?php echo $order_id; ?>" data-postid="<?php echo $post_id; ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'request_ventilation_nonce' ) ); ?>" class="btn-action request_vent"><?php esc_html_e( 'Request Ventilation', 'hoodslyhub' ); ?></a>
									</li>
							<?php } else { ?>
								<li>
									<button type="button" class="btn btn-info" disabled="disabled">Already in ventilation list</button>
								</li>
							<?php } ?>

							</ul>
						</div>

					</div>


					<div class="dashboard-page__order">
						<div class="dashboard-page__order-header">
							<div class="navbar-header">
								<h6 class="title"><?php echo esc_html__( 'Order History', 'hoodslyhub' ); ?></h6>
							</div>

							<div class="navbar-collapse">
								<ul class="navbar-nav ml-auto">
									<li><a href="#" class="icon-setting-sliders"></a></li>
								</ul>
							</div>
						</div>
						<div class="dashboard-page__order-body">

							<div id="history-simplebar">
								<ul class="order-history-list list-unstyled">
								<?php
									$order_summerys = get_post_meta( $post_id, 'order_summery', true );
								?>
									<?php foreach ( $order_summerys as $order_summery ) : ?>

										<li>
											<span class="text"><?php echo esc_html( $order_summery['summery'] ); ?></span>
											<?php $historyDate = date( 'F jS g:i a', strtotime( $order_summery['date'] ) ); ?>
											<span class="date"><?php echo $historyDate; ?></span>
										</li>
									<?php endforeach; ?>
								</ul>

							</div>
						</div>
					</div>
					
					<?php
					$order_status            = get_post_meta( $post_id, 'order_status', true );
					$order_date              = get_post_meta( $post_id, 'order_date', true );
					$bill_of_landing_id      = intval( get_post_meta( $post_id, 'bill_of_landing_id', true ) );
					$orderDate               = date( 'F jS g:i a', strtotime( $order_date ) );
					$origin                  = get_post_meta( $post_id, 'origin', true );
					$domain_parts            = explode( '.', $origin );
					$es_shipping_date        = get_post_meta( $post_id, 'estimated_shipping_date', true );
					$shipping_date           = date( 'F jS', strtotime( $es_shipping_date ) );
					$estimated_shipping_date = $shipping_date ?? '';
					$bol_link                = 'http://staging.wrhhub.com' . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
					$shipping_file_link      = 'http://staging.wrhhub.com' . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
					$image_id                = get_post_meta( $post_id, '_thumbnail_id', true );
					$image_src               = wp_get_attachment_image_src( $image_id, 'full' );
					$image_src               = ( isset( $image_src[0] ) && ! empty( $image_src[0] ) ) ? $image_src[0] : ''
					?>
					<div class="dashboard-page__order">
						<div class="dashboard-page__order-header">
							<div class="navbar-header">
								<h6 class="title"><?php esc_attr_e( 'Order Summary', 'hoodslyhub' ); ?></h6>
							</div>

							<div class="navbar-collapse">
								<ul class="navbar-nav ml-auto">
									<li class="btn-assemble"><a href="#"><?php echo esc_html( ucfirst( $order_status ) ); ?></a></li>
									<li><a href="#" class="icon-setting-sliders"></a></li>
								</ul>
							</div>
						</div>
						<div class="dashboard-page__order-body">
							<div class="order-summary">
								<?php if ( 'wrh' == get_post_type( $post_id ) ) : ?>
									<div class="order-summary__shop">
										<figure class="media">
											<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
										</figure>

										<div class="text"><?php esc_html_e( 'Shop: ', 'hoodslyhub' ); ?><?php echo esc_html__( 'WRH', 'hoodslyhub' ); ?></div>
									</div>
								<?php elseif ( 'wilkes' == get_post_type( $post_id ) ) : ?>
									<div class="order-summary__shop">
										<figure class="media">
											<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
										</figure>

										<div class="text"><?php esc_html_e( 'Shop: ', 'hoodslyhub' ); ?><?php echo esc_html__( 'Wilkes', 'hoodslyhub' ); ?></div>
									</div>
								<?php else : ?>
									<div class="order-summary__shop">
										<figure class="media">
											<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
										</figure>

										<div class="text"><?php esc_html_e( 'Shop: ', 'hoodslyhub' ); ?><?php echo esc_html__( 'Not Assigned Yet', 'hoodslyhub' ); ?></div>
									</div>
								<?php endif; ?>
								<ul class="order-summary__list">
									<li><i class="icon-line-chart"></i>Order Created: <?php echo esc_html( $orderDate ); ?></li>
									<li><i class="icon-line-chart"></i>Order Source: <?php echo esc_html( ucfirst( $domain_parts[0] ) ); ?>
									</li>
									<li><i class="icon-line-chart"></i>Estimated Shipping
										Date: <?php echo esc_html( $estimated_shipping_date ); ?></li>
									<li><i class="icon-line-chart"></i>BOL: <a style="color: #000; padding-left: 5px"
																			   href="<?php echo esc_url( $bol_link ); ?>"
																			   target="_blank"><?php echo esc_html__( 'View File', 'hoodslyhub' ); ?></a>
									</li>
									<li><i class="icon-line-chart"></i>Shipping Label: <a style="color: #000; padding-left: 5px"
																						  href="<?php echo esc_url( $shipping_file_link ); ?>"
																						  target="_blank"><?php echo esc_html__( 'View File', 'hoodslyhub' ); ?></a>
									</li>
									<?php if ( $image_src ) : ?>
									<li><i class="icon-line-chart"></i><?php esc_html_e( 'Proof Of Drop Off:', 'hoodslyhub' ); ?>
										<a class="gallery-popup-item" style="color: #000; padding-left: 5px" href="<?php echo esc_url( $image_src ); ?>" target="_blank"><?php echo esc_html__( 'View Image', 'hoodslyhub' ); ?></a>
									</li>
									<?php endif; ?>
									<li><i class="icon-line-chart"></i>Packing List: {packing_file}</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
get_footer();
