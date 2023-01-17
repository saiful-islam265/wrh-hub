<div class="col-xl-4">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title"><?php esc_html_e( 'Orders With ventilation', 'hoodslyhub' ); ?></h6>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body" id="wrh-quick-vent-list">

			<table class="table has--custom-color">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Order', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Status', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Status', 'hoodslyhub' ); ?></th>
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
							'key'     => 'product_cat',
							'value'   => 'quick-shipping',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'tradewinds_quickship',
							'value'   => 'no',
							'compare' => 'NOT LIKE',
						),
						array(
							'key'     => 'action',
							'value'   => 'Picked',
							'compare' => 'LIKE',
						),
					),
				);
				$vent_orders            = new WP_Query( $args );
				if ( $vent_orders->have_posts() ) {
					while ( $vent_orders->have_posts() ) {
						$vent_orders->the_post();
						$line_items        = get_post_meta( get_the_ID(), 'line_items', true );
						$order_id          = get_post_meta( get_the_ID(), 'order_id', true );
						$first_name        = get_post_meta( get_the_ID(), 'first_name', true );
						$last_name         = get_post_meta( get_the_ID(), 'last_name', true );
						$vent_status       = get_post_meta( get_the_ID(), 'action', true );
						$vent_status       = ! empty( $vent_status ) ? $vent_status : 'Waiting';
						$order_status      = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$ccm_status        = get_post_meta( get_the_ID(), 'status', true );
						$origin            = get_post_meta( get_the_ID(), 'origin', true );
						$origin            = get_post_meta( get_the_ID(), 'origin', true );
						$assign_shop       = trim( get_post_meta( get_the_ID(), 'shop', true ) );
						$shop              = ( isset( $assign_shop ) && ! empty( $assign_shop ) ) ? $assign_shop : 'Not Assigned Yet';
						$backgroundg_color = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
						$status_color      = ( 'Received' === $vent_status ) ? 'style=background-color:#A8DCD7' : ( ( 'Picked' === $vent_status ) ? 'style=background-color:#DCD8A8' : ( ( 'Delivered' === $vent_status ) ? 'style=background-color:#BEA8DC' : 'style=background-color:#F09D9D' ) );
						?>
						<tr style="background-color: #0E9CEE1a;">
							<td data-title="Order Id"><?php the_title(); ?></td>
							<td data-title="Order Status">
								<button class="btn btn-violet"<?php echo esc_attr( $backgroundg_color ); ?>>
								<?php echo esc_html( $order_status ); ?>
								</button>
							</td>
							<td data-title="Status" class="staus-dropdown dropdown">
								<button role="button" class="btn btn-waiting"<?php echo esc_attr( $status_color ); ?> data-toggle="dropdown">
									<?php echo esc_html( $vent_status ); ?>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
								<?php
								if ( is_user_logged_in() ) {
									$user  = wp_get_current_user();
									$roles = (array) $user->roles;
									if ( 'warehouse' === $roles[0] || 'administrator' === $roles[0] ) {
										?>
									<li>
										<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo intval( $order_id ); ?>" class="quick-ship-wrh-picked" data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_ship_wrh_nonce' ) ); ?>">
											<?php esc_html_e( 'Picked', 'hoodslyhub' ); ?>
										</a>
									</li>
										<?php
									}
								}

								if ( is_user_logged_in() ) {
									$user  = wp_get_current_user();
									$roles = (array) $user->roles;
									if ( 'transportation' === $roles[0] || 'administrator' === $roles[0] ) {
										?>
										<li>
											<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo intval( $order_id ); ?>" class="quick-ship-wrh-delivered" data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_ship_wrh_nonce' ) ); ?>"><?php esc_html_e( 'Delivered', 'hoodslyhub' ); ?></a>
										</li>
											<?php
									}
								}
								?>
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
			<div class="hub-pagination" id="wrh_quick_vent_list_paginate" data-max_num_pages="<?php echo $vent_orders->max_num_pages; ?>">
				<?php
				shop_pagination( $hub_paged, $vent_orders->max_num_pages ); // Pagination Function
				?>
			</div>
		</div>
	</div>
</div>
