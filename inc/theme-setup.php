<?php

if ( ! function_exists( 'wrhhub_setup' ) ) {

	function wrhhub_setup() {
		/** Make theme available for translation. */
		load_theme_textdomain( 'toss', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);


		/** This theme uses wp_nav_menu() in one location. */
		register_nav_menus( array(
			'sidebarMenu' => esc_html__( 'Sidebar menu', 'hoodslyhub' ),
		) );

	}

	if ( ! file_exists( get_template_directory() . '/inc/wp_bootstrap_navwalker.php' ) ) {
		// File does not exist... return an error.
		return new WP_Error( 'class-wp-bootstrap-navwalker-missing', __( 'It appears the class-wp-bootstrap-navwalker.php file may be missing.', 'hoodslyhub' ) );
	} else {
		// File exists... require it.
		require get_template_directory() . '/inc/wp_bootstrap_navwalker.php';
	}
	if ( ! wp_next_scheduled( 'wrhhub_on_hold_order_move_to_incoming_order_event_hook' ) ) {
		$alert_time_next = '08:00:00';
		wp_schedule_event( strtotime( $alert_time_next ), 'daily', 'wrhhub_on_hold_order_move_to_incoming_order_event_hook' );
	}
}
add_action( 'after_setup_theme', 'wrhhub_setup' );