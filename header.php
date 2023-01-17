<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<!--    <title>Dashboard | Local Delevery</title>-->

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="hoodslyhub-user-dashboard">
	<header class="dashboard__header">
		<nav class="navbar navbar-expand">
			<div class="container-fluid">
				<div class="navbar-header">
					<a href="#" class="navbar-toggle">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
                    <a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>">
                        <!--                        <img src="assets/images/logo.svg" class="img-fluid" alt="logo">-->
						<?php
						printf(
							'<img width="157px" src="%s" class="img-fluid" alt="%s">',
							esc_url( get_theme_file_uri( 'assets/images/logo.svg' ) ),
							esc_html( get_bloginfo( 'name' ) )
						);
						?>
                    </a>
				</div>
				<?php
					$current_user = wp_get_current_user();
					$roles        = (array) $current_user->roles;
				if ( $roles[0] != 'transportation' || $roles[0] != 'warehouse' ) {
					$args = array(
						'posts_per_page' => 10,
						'post_type'      => 'order_communication',
						'meta_query'     => array(
							array(
								'key'     => 'poke_two_role',
								'value'   => 'yes',
								'compare' => 'LIKE',
							),
						),
					);
				} else {
					$args = array(
						'posts_per_page' => 10,
						'post_type'      => 'order_communication',
						'meta_query'     => array(
							array(
								'key'     => 'poke_two_role',
								'value'   => 'yes',
								'compare' => 'NOT LIKE',
							),
						),
					);
				}


					$no_query               = new WP_Query( $args );
					$notification_count     = get_user_meta( get_current_user_id(), 'notification_count', true );
					$has_notification_class = '';
				if ( $notification_count > 0 ) {
					$has_notification_class = 'show_color';
				}
				?>
				<div class="navbar-collapse" >
					<ul class="navbar-nav ml-auto">
						<li><a href="#" class="icon-search" data-toggle="modal" data-target="#wrhhubSearch"></a></li>
						<li><a href="#" class="icon-paste"></a></li>
						<li><a href="#" class="icon-email-open"></a></li>
						<li class="dropdown">
							<a href="#" role="button" class="icon-notifications save_notification_count <?php echo $has_notification_class; ?>" data-toggle="dropdown"></a>
							<ul class="order_notification_list dropdown-menu notification_scroll" id="order_notification_list">
							<?php
							$order_link = get_template_link( 't_order-details.php' );
							if ( $no_query->have_posts() ) {
								while ( $no_query->have_posts() ) {
									$no_query->the_post();
									$agent_replied = get_post_meta( get_the_ID(), 'agent_replied', true );
									$order_id      = get_post_meta( get_the_ID(), 'order_id', true );
									$post_id       = get_post_meta( get_the_ID(), 'post_id', true );
									$user          = get_userdata( $agent_replied );
									if ( ! empty( $user->data->user_login ) ) {
										echo '<li><a href="#">' . $user->data->user_login . '</a> mentioned you to following this order <a href="' . esc_url(
											add_query_arg(
												array(
													'post_id'  => $post_id,
													'order_id' => $order_id,
												),
												$order_link
											)
										) . '#comments_history">' . $order_id . '</a> <span class="comment_date"><i>' . get_the_date( 'Y-m-d h:i:sa' ) . '</span></li><hr>';
									} else {
										$stock_out_time = get_post_meta( get_the_ID(), 'stock_out_time', true );
										$stock_out_time = ! empty( $stock_out_time ) ? $stock_out_time : get_the_date( 'Y-m-d h:i:sa' );
										echo '<li>' . get_the_content() . ' <span class="comment_date"><i>' . $stock_out_time . '</i></span></li><hr>';
									}
								}
							}

							//update_user_meta(get_current_user_id() ,'notification_count', $no_query->post_count);

							?>
							
							<div id="more_posts">Load More</div>
							
							</ul>
							<?php $notification_count = get_user_meta( get_current_user_id(), 'notification_count', true ); ?>
						</li>
						<span class="badge notification_number"><?php echo $notification_count; ?></span>
					</ul>
					
				</div>
			</div>
		</nav><!-- /nav -->
	</header><!-- /header -->
	<div class="modal fade" id="wrhhubSearch" tabindex="-1" role="dialog" aria-labelledby="hoodslyhubModelCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="hoodslyhubModalLongTitle"><?php esc_html_e( 'Search Order By Order ID', 'hoodslyhub' ); ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="search-wrapper">
						<form class="search-form">
							<input type="search" name="hub-search" class="search-input" id="search" placeholder="Write Order ID" autocomplete="off">
							<!--<input type="submit" class="search-submit" value="" disabled="">-->
						</form>
					</div>
					<div class="selected_order">
						<table class="table selected_order_items">
							<thead>
								<h4>Move Orders</h4>
								<select name="bulk_move_request" id="" class="form-control bulk_move_request">
									<option selected>Select</option>
									<option value="move_to_production_que">Move To Production Que</option>
									<option value="request_ventilation">Request Ventilation</option>
								</select>
								<tr>
									<th scope="col">Order#</th>
									<th scope="col">Status</th>
									<th scope="col">Estimated Shipping Date</th>
								</tr>
							</thead>
							<tbody class="order_items">
							</tbody>
						</table>
					</div>
					<div class="hub_searchresult_wrapper">
						<div class="hub-searchresult" id="hub_searchresult"></div>
						<div
								class="hidden-loader"
								id="hidden-loader"
								data-ajaxurl="<?php echo esc_url( site_url() ) . '/wp-admin/admin-ajax.php'; ?>"
						>
							<div class="hidden-loader__spin"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="header-gutter"></div>
	<?php get_template_part( 'template-parts/sidebar', 'menu' ); ?>
