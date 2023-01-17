<div class="dashboard__vertical-navbar" id="simplebar">
	<nav class="vertical-navbar">
		<?php wp_nav_menu(
			array(
				'depth'          => 2,
				'menu_id'        => '',
				'container'      => false,
				'theme_location' => 'sidebarMenu',
				'menu'           => 'Sidebar Menu',
				'menu_class'     => 'navbar-nav',
				'fallback_cb'    => 'wp_bootstrap_navwalker::fallback',
				'walker'         => new wp_bootstrap_navwalker(),
			)
		); ?>

		<!--<a href="#" class="user-meta mt-auto">
			<figure class="media">
				<?php /*printf( '<img src="%s" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/user.jpg' ) ), esc_html( get_bloginfo( 'name' ) ) ); */ ?>
			</figure>

			<div class="text">
				<div class="name">Mitchell Williamson</div>
				<div class="user-id">Customer ID#01223</div>
			</div>
		</a>-->
		<div href="#" class="user-meta mt-auto">
			<?php
			$user = wp_get_current_user();
			if ( $user ) :
				$user_image              = get_avatar_url( $user->ID );
				$first_name              = strtoupper( mb_substr( $user->first_name, 0, 1 ) );
				$last_name               = strtoupper( mb_substr( $user->last_name, 0, 1 ) );
				$first_name_first_letter = isset( $first_name ) && ! empty( $first_name ) ? $first_name : '';
				$last_name_first_letter  = isset( $last_name ) && ! empty( $last_name ) ? $last_name : '';
				$text_image              = $first_name_first_letter . $last_name_first_letter;
				$has_avatar              = HoodslyHubHelper::has_gravatar( $user->ID );
				?>
				<?php if ( $has_avatar ) : ?>
				<figure class="media">
					<?php printf( '<img src="%s" alt="%s">', esc_url( $user_image ), esc_html( get_bloginfo( 'name' ) ) ); ?>
				</figure>
					<?php
			else :
				;
				?>
				<figure class="media">
					<p><?php echo esc_html( $text_image ); ?></p>
				</figure>
			<?php endif; ?>
			<?php endif; ?>
			<div class="text">
				<?php
				global $current_user;
				wp_get_current_user();
				?>
				<?php
				if ( is_user_logged_in() ) {
					?>
					<div class="name"><?php echo esc_html( $current_user->display_name ); ?></div>
					<?php
					$user = get_userdata( $current_user->ID );
					// Get all the user roles as an array.
					$user_roles = $user->roles;
					// Check if the role you're interested in, is present in the array.
					?>
						<div class="user-id">User (<?php echo $current_user->display_name; ?>) ID #<?php echo intval( $current_user->ID ); ?></div>
					<?php if ( array_intersect( array( 'administrator' ), $user->roles ) ) : ?>
						<div>
							<a class="user-id" href="<?php echo esc_url( get_dashboard_url() ); ?>">
								<?php echo esc_html__( 'WP Dashboard', 'hoodslyhub' ); ?>
							</a>
						</div>
					<?php endif; ?>
					<?php
				} else {
					wp_loginout(); }
				?>

				<div>
					<a class="user-id" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
						<?php echo esc_html__( 'Logout', 'hoodslyhub' ); ?>
					</a>
				</div>
			</div>
		</div>
	</nav>
</div>
