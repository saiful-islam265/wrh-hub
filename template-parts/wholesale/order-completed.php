<div class="col-xl-12">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title"><?php esc_html_e( 'Production Que', 'wrhhub' ); ?></h6>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="completed-order-list">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col">Order Id</th>
					<th scope="col">Customer Info</th>
					<th scope="col">Order Status</th>
					<th scope="col">Shop</th>
					<th scope="col">Estimated Shipping Date</th>
					<th scope="col">Order Source</th>
					<th scope="col">Items</th>
					<th scope="col">Files</th>
					<th scope="col">Actions</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$hub_paged              = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$default_posts_per_page = get_option( 'posts_per_page' );
				$args                   = array(
					'post_type'      => 'wrh_order',
					'posts_per_page' => $default_posts_per_page,
					'paged'          => $hub_paged,
					'orderby'        => 'title',
					'meta_query'     => array(
						array(
							'key'     => 'order_status',
							'value'   => 'Delivered',
							'compare' => 'LIKE',
						),
					),
				);
				$all_orders             = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {
					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
						$order_link              = get_template_link( 't_order-details.php' );
						$order_id                = get_post_meta( get_the_ID(), 'order_id', true );
						$es_shipping_date        = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
						$shipping_date           = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
						$estimated_shipping_date = $shipping_date ?? '';
						$first_name              = get_post_meta( get_the_ID(), 'first_name', true );
						$last_name               = get_post_meta( get_the_ID(), 'last_name', true );
						$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$origin                  = get_post_meta( get_the_ID(), 'origin', true );
						$domain_parts            = explode( '.', $origin );
						$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
						$current_date            = gmdate( 'm/d/Y H:i:s', time() );
						$date1                   = strtotime( $estimated_shipping_date );
						$date2                   = strtotime( $current_date );
						$date_difference         = $date1 - $date2;
						$array                   = in_array( $order_status, array( 'In Production', 'Pre Assembly', 'Assembly', 'Sanding', 'Finishing' ), true );

						$result = round( $date_difference / ( 60 * 60 * 24 ) );
						?>
				<tr style="background-color: rgba(0,255,25,0.1);">
					<td data-title="Order Id"><a href="
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
														"><?php the_title(); ?></a></td>
					<td data-title="Customer Info"><?php echo $first_name . ' ' . $last_name; ?></td>
					<td data-title="Order Status">
						<button class="btn btn-bluesky" <?php echo esc_attr( $backgroundg_color ); ?>><?php echo $order_status; ?></button>
					</td>
							<?php if ( 'wrh' == get_post_type() ) : ?>
						<td data-title="Shop" data-toggle="tooltip"
							title='<h6 class="title"><?php echo esc_html__( 'WRH', 'hoodslyhub' ); ?></h6>'>
							<figure class="media-shop">
								<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
							</figure>
						</td>
					<?php elseif ( 'wilkes' == get_post_type() ) : ?>
						<td data-title="Shop" data-toggle="tooltip"
							title='<h6 class="title"><?php echo esc_html__( 'Wilkes', 'hoodslyhub' ); ?></h6>'>
							<figure class="media-shop">
								<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
							</figure>
						</td>
					<?php else : ?>
						<td data-title="Shop" data-toggle="tooltip"
							title='<h6 class="title"><?php echo esc_html__( 'Not Assigned Yet', 'hoodslyhub' ); ?></h6>'>
							<figure class="media-shop">
								<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), get_bloginfo( 'name' ) ); ?>
							</figure>
						</td>
					<?php endif; ?>
					<td data-title="Estimated Shipping Date"><?php echo esc_html( $estimated_shipping_date ); ?> (<?php echo intval( $result ) . 'Days'; ?>)</td>
					<td data-title="Order Source"><?php echo esc_html( ucfirst( $domain_parts[0] ) ); ?></td>
					<td data-title="Items" id="ordered_items"><button type="button" class="btn" data-toggle="modal" data-target="#test_<?php echo esc_html( $order_id ); ?>"><?php echo isset( $line_items['line_items'] ) ? count( $line_items['line_items'] ) : ''; ?></button></td>
					<div class="modal fade" id="test_<?php echo esc_html( $order_id ); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									...
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right"
						title='<h6 class="title">Order Files</h6><ul class="tooltip-dropdown"><li>BOL</li><li>Shipping Label</li><li>Proof Of Drop Off</li> <li>Damage  Photos</li></ul>'>
						Files
					</td>
					<td data-title="Actions" class="action-dropdown dropdown">
						<div role="button" class="icon-dots" data-toggle="dropdown"><span></span><span></span><span></span></div>
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
							);
							?>
											">View</a></li>
							<?php if ( current_user_can( 'administrator' ) ) : ?>
								<li><a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order"
									   data-nonce="<?php echo wp_create_nonce( 'hoodslyhub_delete_order_nonce' ); ?>">Delete</a></li>
							<?php endif ?>
						</ul>
					</td>
				</tr>
							<?php
					}
				} else {
					?>
					<tr style="background-color: #4747471a;">
						<td colspan="100%" class="text-left"><?php esc_html_e( 'There is no order yet', 'hoodslyhub' ); ?></td>
					</tr>
					<?php
				}
				wp_reset_postdata();
				?>
				</tbody>
			</table>
			<div class="hub-pagination" id="completedPaginate" data-max_num_pages="<?php echo $all_orders->max_num_pages; ?>">
				<?php
				shop_pagination( $hub_paged, $all_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
