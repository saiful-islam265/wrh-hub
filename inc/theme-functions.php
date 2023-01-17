<?php
function add_comment_projects() {
	$post_id           = $_POST['post_id'];
	$comment_details[] = $_POST['save_note'];
	$comment_meta      = get_post_meta( $post_id, 'comment_meta', true );
	//update_post_meta($post_id, 'user_mentioned', $comment_details);
	$current_user  = wp_get_current_user();
	$order_notes[] = array(
		'user_mail'  => $current_user->user_email,
		'order_note' => $comment_details,
		'user_name'  => $current_user->user_login,
		'note_time'  => date( 'h:i: A' ),
	);
	update_post_meta( $post_id, 'comment_meta', array_merge( $comment_meta, $order_notes ) );
	$mentioned_user_array = array();
	foreach ( $comment_meta as $comment ) {
		preg_match_all( '/data-item-id="(.*?)"/', $comment['order_note'][0], $matches );
		$mentioned_user_array = array_merge( $matches[1], $mentioned_user_array );
	}
	update_post_meta( $post_id, 'user_mentioned', $mentioned_user_array );
	die( $comment_details );
}

add_action( 'wp_ajax_add_comment', 'add_comment_projects' );

/*** get permalink by template name */
function get_template_link( $temp ) {
	$link  = null;
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template',
			'meta_value' => $temp,
		)
	);
	if ( isset( $pages[0] ) ) {
		$link = get_page_link( $pages[0]->ID );
	}

	return $link;
}

// Theme icon field
if ( class_exists( 'acf_field' ) ) {
	class acf_field_theme_icon extends acf_field {

		function __construct() {
			// vars
			$this->name     = 'theme_icons_field';
			$this->label    = __( 'Theme Icons', 'hoodslyhub' );
			$this->category = __( 'Choice', 'hoodslyhub' );
			parent::__construct();
		}


		function render_field( $field ) {

			$choices = array(
				'icon-file'            => '<i class="icon-file"></i> File',
				'icon-help'            => '<i class="icon-help"></i> Help',
				'icon-home'            => '<i class="icon-home"></i> Home',
				'icon-line-chart'      => '<i class="icon-line-chart"></i> Line Chart',
				'icon-message'         => '<i class="icon-message"></i> Message',
				'icon-notifications'   => '<i class="icon-notifications"></i> Notifications',
				'icon-paste'           => '<i class="icon-paste"></i> Paste',
				'icon-setting'         => '<i class="icon-setting"></i> Setting',
				'icon-setting-sliders' => '<i class="icon-setting-sliders"></i> Setting Sliders',
				'icon-users'           => '<i class="icon-users"></i> Users',
				'icon-angle-down'      => '<i class="icon-angle-down"></i> Angle Down',
				'icon-angle-left'      => '<i class="icon-angle-left"></i> Angle Left',
				'icon-angle-right'     => '<i class="icon-angle-right"></i> Angle Right',
				'icon-angle-up'        => '<i class="icon-angle-up"></i> Angle Up',
				'icon-calendar'        => '<i class="icon-calendar"></i> Calendar',
				'icon-caret-down'      => '<i class="icon-caret-down"></i> Caret Down',
				'icon-caret-up'        => '<i class="icon-caret-up"></i> Caret Up',
				'icon-close'           => '<i class="icon-close"></i> Close',
				'icon-email'           => '<i class="icon-email"></i> Email',
				'icon-email-open'      => '<i class="icon-email-open"></i> Email Open',
			);

			$field['choices']    = $choices;
			$field['type']       = 'select';
			$field['ui']         = 1;
			$field['allow_null'] = 0;
			$field['ajax']       = 0;
			$field['multiple']   = 0;

			echo "<div class='acf-field acf-field-select acf-field-select-icon' 
           data-name='{$field['label']}' 
           data-type='select' 
           data-key='{$field['key']}'>";

			acf_render_field( $field );

			echo '</div>';
		}

		function format_value( $value ) {

			if ( ! $value || empty( $value ) ) {
				return false;
			}

			return $value;
		}
	}

	new acf_field_theme_icon();
}


/**
 * Order Delete Method
 */
function hoodslyhub_delete_order() {
	$permission = check_ajax_referer( 'hoodslyhub_delete_order_nonce', 'nonce', false );
	if ( $permission == false ) {
		wp_die();
	} else {
		wp_delete_post( $_REQUEST['id'] );
	}
}

add_action( 'wp_ajax_hoodslyhub_delete_order', 'hoodslyhub_delete_order' );



add_action( 'wp_ajax_hoodslyhub_damage_claim_email_sent', 'hoodslyhub_damage_claim_email_sent' );
/**
 * @param $phpmailer
 * setup_phpmailer_init Method
 */
if ( defined( 'WP_DEBUG' ) ) {

	function setup_phpmailer_init( $phpmailer ) {
		if ( defined( 'IM_DEV' ) ) {
			$username = 'c591d54f59a358';
			$password = '0a4c901caf70a3';
		} else {
			$username = 'c591d54f59a358';
			$password = '0a4c901caf70a3';
		}
		$phpmailer->Host       = 'smtp.mailtrap.io'; // for example, smtp.mailtrap.io
		$phpmailer->Port       = 2525; // set the appropriate port: 465, 2525, etc.
		$phpmailer->Username   = $username; // your SMTP username
		$phpmailer->Password   = $password; // your SMTP password
		$phpmailer->SMTPAuth   = true;
		$phpmailer->SMTPSecure = 'tls'; // preferable but optional
		$phpmailer->IsSMTP();
	}

	add_action( 'phpmailer_init', 'setup_phpmailer_init' );
}

add_action( 'wp_ajax_add_ventilation_tracking', 'add_ventilation_tracking' );
function add_ventilation_tracking() {
	$email = $_POST['vent_email'];
	$to    = array(
		$email,
	);

	$headers = 'From: Shipping Date <support@hoodslyhub.com>' . "\r\n";

	$subject = 'Damage Claim for #';
	if ( defined( 'WP_DEBUG' ) ) {
		$message = 'Damage Claim URL:<a href="http:///damage-claim">Damage Claim</a>';
	} else {
		$message = 'Damage Claim URL:<a href="https:///damage-claim">Damage Claim</a>';
	}
	wp_mail( $to, $subject, $message, $headers );
	$post_id       = $_POST['post_id'];
	$order_summery = get_post_meta( $post_id, 'order_summery', true );
	$dvent_date    = current_time( 'mysql' );
	update_post_meta(
		$post_id,
		'order_summery',
		array_merge(
			$order_summery,
			array(
				array(
					'summery' => 'Ventilation Tracking has been submitted',
					'date'    => $dvent_date,
				),
			)
		)
	);
}

/*
 * Order Hold request form Hub
 */
function order_on_hold_from_hub( $request ) {
	$api_secret = get_option( 'hoodslyhub_api_credentials' );
	$hash       = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
		global $wpdb;
		$response_data = json_decode( $request->get_body(), true );
		$order_id      = $response_data['order_id'];
		$myposts       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '%s'", '%' . $wpdb->esc_like( $order_id ) . '%' ) );
		$post_id       = $myposts[0]->ID;
		update_post_meta( $post_id, 'order_status', 'Order Hold' );

		$hold_date           = current_time( 'mysql' );
		$order_summery       = get_post_meta( $post_id, 'order_summery', true );
		$order_summery       = ( isset( $order_summery ) && is_array( $order_summery ) ) ? $order_summery : array();
		$_summery            = array(
			array(
				'summery' => 'Order Hold By HoodslyHub',
				'date'    => $hold_date,
			),
		);
		$order_summery_array = array_merge( $order_summery, $_summery );
		update_post_meta( $post_id, 'order_summery', $order_summery_array );
	}
}


/**
 * Damage CLiam request form Hub
 * @param $response
 *
 * @return void
 */
function shop_damage_claim_from_hub( $response ) {
	$api_secret = get_option( 'hoodslyhub_api_credentials' );
	$hash       = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
		global $wpdb;
		$body_data          = $response->get_body();
		$arr                = json_decode( $body_data, true );
		$order_id           = $arr['order_id'];
		$replacement_option = $arr['replacement_option'];
		$post_id            = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_name = %d AND post_type = %s",
				$order_id,
				'wrh_order',
			)
		);
		update_post_meta( $post_id, 'shop_claim_type', $arr['shop_claim_type'] );
		update_post_meta( $post_id, 'shop_claim', $arr['shop_claim'] );
		update_post_meta( $post_id, 'claim_value', $arr['claim_value'] );
		update_post_meta( $post_id, 'damage_item', $arr['damage_item'] );
		update_post_meta( $post_id, 'refund_description', $arr['refund_description'] );
		update_post_meta( $post_id, 'refund_value', $arr['refund_value'] );
		update_post_meta( $post_id, 'shop_claim_details', $arr['shop_claim_details'] );

		update_post_meta( $post_id, 'damage_claim_id', $arr['damage_claim_id'] );
		update_post_meta( $post_id, 'damage_type', $arr['damage_type'] );
		update_post_meta( $post_id, 'damage_details', $arr['damage_details'] );
		update_post_meta( $post_id, 'damage_claim_filling_date', $arr['damage_claim_filling_date'] );
		update_post_meta( $post_id, 'damage_proof_submit_date', $arr['damage_proof_submit_date'] );
		update_post_meta( $post_id, 'damage_proof_submit_date', $arr['damage_proof_submit_date'] );

		foreach ( $arr['image_src'] as $key => $value ) {
			if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
				$options    = array(
					'ssl' => array(
						'verify_peer'      => false,
						'verify_peer_name' => false,
					),
				);
				$image_data = file_get_contents( $value[0], false, stream_context_create( $options ) );
			} else {
				$image_data = file_get_contents( $value[0] );
			}
			$image_name               = basename( $value[0] );
			$upload                   = wp_upload_bits( $image_name, null, $image_data );
			$arr['image_src'][ $key ] = $upload['url'];

		}
		update_post_meta( $post_id, 'damage_image_src', $arr['image_src'] );

		if ( 'Yes' === $replacement_option ) {

			$hood_replace      = ( isset( $arr['hood_replace'] ) && ! empty( $arr['hood_replace'] ) ) ? $arr['hood_replace'] : 'N/A';
			$f_shelf_replace   = ( isset( $arr['f_shelf_replace'] ) && ! empty( $arr['f_shelf_replace'] ) ) ? $arr['f_shelf_replace'] : 'N/A';
			$hall_tree_replace = ( isset( $arr['hall_tree_replace'] ) && ! empty( $arr['hall_tree_replace'] ) ) ? $arr['hall_tree_replace'] : 'N/A';

			update_post_meta( $post_id, 'hood_replace', $hood_replace );
			update_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
			update_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );

			$get_hood_replace      = get_post_meta( $post_id, 'hood_replace', true );
			$get_f_shelf_replace   = get_post_meta( $post_id, 'f_shelf_replace', true );
			$get_hall_tree_replace = get_post_meta( $post_id, 'hall_tree_replace', true );

			if ( 'miscellaneous' === $get_hood_replace || 'miscellaneous' === $get_f_shelf_replace || 'miscellaneous' === $get_hall_tree_replace ) {
				$miscellaneous = ( isset( $arr['miscellaneous'] ) && ! empty( $arr['miscellaneous'] ) ) ? $arr['miscellaneous'] : 'N/A';
				update_post_meta( $post_id, 'miscellaneous', $miscellaneous );
			}
		} else {
			$no_replace = $arr['no_replace'];
			update_post_meta( $post_id, 'no_replace', $no_replace );
		}


		$bill_of_landing_id        = intval( get_post_meta( $post_id, 'bill_of_landing_id', true ) );
		$shipping                  = get_post_meta( $post_id, 'shipping', true );
		$billing                   = get_post_meta( $post_id, 'billing', true );
		$customer_note             = get_post_meta( $post_id, 'customer_note', true );
		$order_desc                = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %d AND post_type = %s", $post_id, 'wrh_order' ) );
		$order_status              = trim( get_post_meta( $post_id, 'order_status', true ) );
		$order_date                = get_post_meta( $post_id, 'order_date', true );
		$es_shipping_date          = get_post_meta( $post_id, 'estimated_shipping_date', true );
		$origin                    = get_post_meta( $post_id, 'origin', true );
		$meta_data_arr             = get_post_meta( $post_id, 'meta_data_arr', true );
		$product_names             = get_post_meta( $post_id, 'product_name', true );
		$product_cat               = get_post_meta( $post_id, 'product_cat', true );
		$product_cat_name          = get_post_meta( $post_id, 'product_cat_name', true );
		$tradewinds_quickship      = get_post_meta( $post_id, 'tradewinds_quickship', true );
		$product_sku               = get_post_meta( $post_id, 'item_sku', true );
		$custom_color_match        = get_post_meta( $post_id, 'custom_color_match', true );
		$shipping_lines            = get_post_meta( $post_id, 'shipping_lines', true );
		$order_id                  = get_post_meta( $post_id, 'order_id', true );
		$line_items                = get_post_meta( $post_id, 'line_items', true );
		$bol_pdf                   = get_post_meta( $post_id, 'bol_pdf', true );
		$shipping_label            = get_post_meta( $post_id, 'shipping_label', true );
		$samples_status            = get_post_meta( $post_id, 'shipping_label', true );
		$is_tradewinds_selected    = get_post_meta( $post_id, 'is_tradewinds_selected', true );
		$completion_date           = get_post_meta( $post_id, 'completion_date', true );
		$bol_regenerated           = get_post_meta( $post_id, 'bol_regenerated', true );
		$damage_claim_id           = get_post_meta( $post_id, 'damage_claim_id', true );
		$damage_item               = get_post_meta( $post_id, 'damage_item', true );
		$damage_type               = get_post_meta( $post_id, 'damage_type', true );
		$damage_details            = get_post_meta( $post_id, 'damage_details', true );
		$damage_claim_filling_date = get_post_meta( $post_id, 'damage_claim_filling_date', true );
		$damage_proof_submit_date  = get_post_meta( $post_id, 'damage_proof_submit_date', true );
		$claim_value               = get_post_meta( $post_id, 'claim_value', true );
		$hood_replace              = get_post_meta( $post_id, 'hood_replace', true );
		$f_shelf_replace           = get_post_meta( $post_id, 'f_shelf_replace', true );
		$hall_tree_replace         = get_post_meta( $post_id, 'hall_tree_replace', true );
		$no_replace                = get_post_meta( $post_id, 'no_replace', true );
		$damage_image_src          = get_post_meta( $post_id, 'damage_image_src', true );
		$miscellaneous          = get_post_meta( $post_id, 'miscellaneous', true );
		$shop_claim          = get_post_meta( $post_id, 'shop_claim', true );

		if ( 'Entire Hood' === $hood_replace || 'Entire Hood' === $f_shelf_replace || 'Entire Hood' === $hall_tree_replace || 'Chimney' === $hood_replace || 'Chimney' === $f_shelf_replace || 'Chimney' === $hall_tree_replace ||'Apron' === $hood_replace || 'Apron' === $f_shelf_replace || 'Apron' === $hall_tree_replace) {

			/**
			 *
			 */
			$wrh_order = array(
				'post_title'   => 'REP-' . $order_id,
				'post_content' => $order_desc,
				'post_status'  => 'publish',
				'post_date'    => current_time( 'mysql' ),
				'post_type'    => 'wrh_order',
			);

			$post_id = wp_insert_post( $wrh_order );
			/**
			 * Saving order data as meta to all_orders cpt
			 */

			add_post_meta( $post_id, 'estimated_shipping_date', $es_shipping_date );
			add_post_meta( $post_id, 'customer_note', $customer_note );
			add_post_meta( $post_id, 'origin', $origin );
			add_post_meta( $post_id, 'order_date', $order_date );
			add_post_meta( $post_id, 'order_id', 'REP-' . $order_id );
			add_post_meta( $post_id, 'meta_data_arr', $meta_data_arr );
			add_post_meta( $post_id, 'product_name', $product_names );
			add_post_meta( $post_id, 'product_cat', $product_cat );
			add_post_meta( $post_id, 'product_cat_name', $product_cat_name );
			add_post_meta( $post_id, 'tradewinds_quickship', $tradewinds_quickship );
			add_post_meta( $post_id, 'item_sku', $product_sku );
			add_post_meta( $post_id, 'custom_color_match', $custom_color_match );
			add_post_meta( $post_id, 'billing', $billing );
			add_post_meta( $post_id, 'shipping', $shipping );
			add_post_meta( $post_id, 'bill_of_landing_id', $bill_of_landing_id );
			add_post_meta( $post_id, 'shipping_lines', $shipping_lines );
			add_post_meta( $post_id, 'line_items', $line_items );
			add_post_meta( $post_id, 'bol_pdf', $bol_pdf );
			add_post_meta( $post_id, 'shipping_label', $shipping_label );
			add_post_meta( $post_id, 'samples_status', $samples_status );
			add_post_meta( $post_id, 'is_tradewinds_selected', $is_tradewinds_selected );
			add_post_meta( $post_id, 'completion_date', $completion_date );
			add_post_meta( $post_id, 'bol_regenerated', $bol_regenerated );
			add_post_meta( $post_id, 'is_priority_damage_claim', 'yes' );
			add_post_meta( $post_id, 'damage_claim_id', $damage_claim_id );
			add_post_meta( $post_id, 'damage_item', $damage_item );
			add_post_meta( $post_id, 'damage_type', $damage_type );
			add_post_meta( $post_id, 'damage_details', $damage_details );
			add_post_meta( $post_id, 'damage_claim_filling_date', $damage_claim_filling_date );
			add_post_meta( $post_id, 'damage_proof_submit_date', $damage_proof_submit_date );
			add_post_meta( $post_id, 'damage_image_src', $damage_image_src );
			add_post_meta( $post_id, 'claim_value', $claim_value );
			add_post_meta( $post_id, 'hood_replace', $hood_replace );
			add_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
			add_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );
			add_post_meta( $post_id, 'no_replace', $no_replace );
			add_post_meta( $post_id, 'miscellaneous', $miscellaneous );
			add_post_meta( $post_id, 'order_status', 'Pending' );
			update_post_meta( $post_id, 'shop_claim', 'yes' );
			update_post_meta($post_id,'accessory_order_damage','no');
		}else{
			/**
			 *
			 */
			$wrh_order = array(
				'post_title'   => 'REP-' . $order_id,
				'post_content' => $order_desc,
				'post_status'  => 'publish',
				'post_date'    => current_time( 'mysql' ),
				'post_type'    => 'wrh_order',
			);

			$post_id = wp_insert_post( $wrh_order );
			/**
			 * Saving order data as meta to all_orders cpt
			 */

			add_post_meta( $post_id, 'estimated_shipping_date', $es_shipping_date );
			add_post_meta( $post_id, 'customer_note', $customer_note );
			add_post_meta( $post_id, 'origin', $origin );
			add_post_meta( $post_id, 'order_date', $order_date );
			add_post_meta( $post_id, 'order_id', 'REP-' . $order_id );
			add_post_meta( $post_id, 'meta_data_arr', $meta_data_arr );
			add_post_meta( $post_id, 'product_name', $product_names );
			add_post_meta( $post_id, 'product_cat', $product_cat );
			add_post_meta( $post_id, 'product_cat_name', $product_cat_name );
			add_post_meta( $post_id, 'tradewinds_quickship', $tradewinds_quickship );
			add_post_meta( $post_id, 'item_sku', $product_sku );
			add_post_meta( $post_id, 'custom_color_match', $custom_color_match );
			add_post_meta( $post_id, 'billing', $billing );
			add_post_meta( $post_id, 'shipping', $shipping );
			add_post_meta( $post_id, 'bill_of_landing_id', $bill_of_landing_id );
			add_post_meta( $post_id, 'shipping_lines', $shipping_lines );
			add_post_meta( $post_id, 'line_items', $line_items );
			add_post_meta( $post_id, 'bol_pdf', $bol_pdf );
			add_post_meta( $post_id, 'shipping_label', $shipping_label );
			add_post_meta( $post_id, 'samples_status', $samples_status );
			add_post_meta( $post_id, 'is_tradewinds_selected', $is_tradewinds_selected );
			add_post_meta( $post_id, 'completion_date', $completion_date );
			add_post_meta( $post_id, 'bol_regenerated', $bol_regenerated );
			add_post_meta( $post_id, 'is_priority_damage_claim', 'yes' );
			add_post_meta( $post_id, 'damage_claim_id', $damage_claim_id );
			add_post_meta( $post_id, 'damage_item', $damage_item );
			add_post_meta( $post_id, 'damage_type', $damage_type );
			add_post_meta( $post_id, 'damage_details', $damage_details );
			add_post_meta( $post_id, 'damage_claim_filling_date', $damage_claim_filling_date );
			add_post_meta( $post_id, 'damage_proof_submit_date', $damage_proof_submit_date );
			add_post_meta( $post_id, 'damage_image_src', $damage_image_src );
			add_post_meta( $post_id, 'claim_value', $claim_value );
			add_post_meta( $post_id, 'hood_replace', $hood_replace );
			add_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
			add_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );
			add_post_meta( $post_id, 'no_replace', $no_replace );
			add_post_meta( $post_id, 'miscellaneous', $miscellaneous );
			add_post_meta( $post_id, 'order_status', 'Pending' );
			update_post_meta( $post_id, 'shop_claim', 'no' );
            update_post_meta($post_id,'accessory_order_damage','yes');
        }
	}
}

/**
 *
 * Registering rest route for fluent crm
 *
 * @return void
 */
function ventilation_process( $request ) {
	$api_secret = get_option( 'hoodslyhub_api_credentials' );
	$hash       = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $hash == $_SERVER['HTTP_API_SIGNATURE'] ) {
		global $wpdb;
		$response_data = json_decode( $request->get_body(), true );
		$order_id      = $response_data['order_title'];
		$myposts       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title LIKE '%s'", '%' . $wpdb->esc_like( $order_id ) . '%' ) );
		$post_id       = $myposts[0]->ID;
		if ( $response_data['vent_action'] == 'picked' ) {
			update_post_meta( $post_id, 'action', 'Picked' );
		} elseif ( $response_data['vent_action'] == 'delivered' ) {
			update_post_meta( $post_id, 'action', 'Delivered' );
		}
	}
}

/**
 * WRH Custom color match status action - received
 */
function wrh_ccm_received_action() {
	$permission = check_ajax_referer( 'wrh_received_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		update_post_meta( $post_id, 'samples_status', 'Received' );

		//Data Send to Hub
		/*$order_status                    = get_post_meta( $post_id, 'order_status', true );
		$order_id                        = get_post_meta( $post_id, 'order_id', true );
		$hub_data                        = wp_json_encode(
			array(
				'order_status' => $order_status,
				'order_id'     => $order_id,
			)
		);
		$api_endpoint                    = get_option( 'hoodslyhub_api_settings' );
		$ccm_order_status_update_request = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'ccm_order_status_update_request' === $value['end_point_type'] ) {
				$ccm_order_status_update_request = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		$data          = wp_remote_post(
			$ccm_order_status_update_request,
			array(
				'body'    => $hub_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);*/
		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Received custom color match samples' );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Received custom color match sample.',
			)
		);
	}
}
add_action( 'wp_ajax_wrh_ccm_received_action', 'wrh_ccm_received_action' );

/**
 * WRH Custom color match samples Send To Be Matched - received
 *
 * @return void
 */
function wrh_ccm_send_to_be_matched_action() {
	$permission = check_ajax_referer( 'ccm_sent_to_be_matched_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Send To Be Matched Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		update_post_meta( $post_id, 'samples_status', 'Send To Be Matched' );

		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Sample has been sent to be matched' );

		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Sample has been sent to be matched',
			)
		);
	}
}
add_action( 'wp_ajax_wrh_ccm_send_to_be_matched_action', 'wrh_ccm_send_to_be_matched_action' );

/**
 * WRH Custom color match samples matched action
 *
 * @return void
 */
function wrh_ccm_matched_action() {
	$permission = check_ajax_referer( 'ccm_matched_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Samples Matched Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		update_post_meta( $post_id, 'samples_status', 'Matched' );
		update_post_meta( $post_id, 'custom_color_match', '0' );
		update_post_meta( $post_id, 'completion_date', 'none' );
		update_post_meta( $post_id, 'order_status', 'Pending' );

		//Data Send to Hub
		$order_status                    = get_post_meta( $post_id, 'order_status', true );
		$order_id                        = get_post_meta( $post_id, 'order_id', true );
		$hub_data                        = wp_json_encode(
			array(
				'order_status' => $order_status,
				'order_id'     => $order_id,
				'shop'         => 'WRH',
			)
		);
		$api_endpoint                    = get_option( 'hoodslyhub_api_settings' );
		$ccm_order_status_update_request = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'ccm_order_status_update_request' === $value['end_point_type'] ) {
				$ccm_order_status_update_request = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		$data          = wp_remote_post(
			$ccm_order_status_update_request,
			array(
				'body'    => $hub_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);
		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Sample Successfully Matched' );
		HoodslyHubHelper::add_order_history( $post_id, 'Order Pending' );

		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Sample Successfully Matched',
			)
		);
	}
}
add_action( 'wp_ajax_wrh_ccm_matched_action', 'wrh_ccm_matched_action' );

/**
 * Custom color match order from hub - API request
 */
function ccm_order_received_from_hub( $response ) {
	$api_secret = get_option( 'hoodslyhub_api_credentials' );
	$hash       = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );

	if ( $_SERVER['HTTP_API_SIGNATURE'] === $hash ) {
		global $wpdb;
		$body_data         = $response->get_body();
		$arr               = json_decode( $body_data, true );
		$order_id          = $arr['order_id'];
		$ld_samples_status = $arr['ld_samples_status'];
		$order_status      = $arr['order_status'];
		$wrh_post_id       = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_name = %d AND post_type = %s",
				$order_id,
				'wrh_order',
			)
		);
		update_post_meta( $wrh_post_id, 'custom_color_match_status', 'Delivered' );
		update_post_meta( $wrh_post_id, 'order_status', $order_status );
		update_post_meta( $wrh_post_id, 'samples_status', $ld_samples_status );
	}
}

/**
 * WRH On hold Order to incoming (Pending)
 */
function wrh_order_pending_to_production() {
	$permission = check_ajax_referer( 'wrh_order_pending_to_production_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id  = $_REQUEST['post_id'];
		$order_id = $_REQUEST['order_id'];
		update_post_meta( $post_id, 'order_status', 'Pending' );
		update_post_meta( $post_id, 'completion_date', 'none' );
		update_post_meta( $post_id, 'order_hold_is_priority', 'yes' );
		//Data Send to Hub
		$order_status           = get_post_meta( $post_id, 'order_status', true );
		$hub_data               = wp_json_encode(
			array(
				'order_status' => $order_status,
				'order_id'     => $order_id,
			)
		);
		$api_endpoint           = get_option( 'hoodslyhub_api_settings' );
		$order_status_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'order_status_end_point' === $value['end_point_type'] ) {
				$order_status_end_point = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		$data          = wp_remote_post(
			$order_status_end_point,
			array(
				'body'    => $hub_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);
		/*Order History updated*/
		HoodslyHubHelper::add_order_history( $post_id, 'Order Received & Start to Production' );

		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Order Received & Start to Production.',
			)
		);
	}
}
add_action( 'wp_ajax_wrh_order_pending_to_production', 'wrh_order_pending_to_production' );

/**
 * request ventilation
 */
function request_ventilation() {
	$permission = check_ajax_referer( 'request_ventilation_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$vent_request_data = wp_json_encode(
			array(
				'order_id' => $_POST['orderid'],
			)
		);

		$api_endpoint                  = get_option( 'hoodslyhub_api_settings' );
		$ventilation_request_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'ventilation_request_end_point' === $value['end_point_type'] ) {
				$ventilation_request_end_point = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		$data          = wp_remote_post(
			$ventilation_request_end_point,
			array(
				'body'    => $vent_request_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);
		update_post_meta( $_POST['postid'], 'is_tradewinds_selected', 'yes' );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Received custom color match sample.',
			)
		);
	}
}
add_action( 'wp_ajax_request_ventilation', 'request_ventilation' );

/**
 * Quick Shipping Option status change
 */
function quick_ship_order_action_status() {
	$permission = check_ajax_referer( 'quick_ship_wrh_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = $_REQUEST['post_id'];
		$orderid = $_REQUEST['orderid'];
		//$setting_api = new hoodslyhub_Settings();
		//$req_url     = $setting_api->get_option( 'quickship_status_endpoint', 'HoodslyHub_quickship_setting', 'text' );
		$api_endpoint               = get_option( 'hoodslyhub_api_settings' );
		$quickship_status_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'quickship_status_end_point' === $value['end_point_type'] ) {
				$quickship_status_end_point = $value['end_point_url'];
			}
		}
		if ( trim( $_POST['action_type'] ) === 'Picked' ) {
			update_post_meta( $post_id, 'action', 'Picked' );
			$notifcation   = wp_insert_post(
				array(
					'post_title'   => intval( $orderid ),
					'post_type'    => 'order_communication',
					'post_content' => 'Order ' . $orderid . ' has been picked',
					'post_status'  => 'publish',
				)
			);
			$wrh_data      = wp_json_encode(
				array(
					'order_title'   => $orderid,
					'sample_action' => 'picked',
				)
			);
			$api_secret    = get_option( 'hoodslyhub_api_credentials' );
			$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
			wp_remote_post(
				$quickship_status_end_point,
				array(
					'body'    => $wrh_data,
					'headers' => array(
						'content-type'  => 'application/json',
						'Api-Signature' => $api_signature,
					),
				)
			);
			update_post_meta( $notifcation, 'send_to_warehouse', 'poke_warehouse' );
		} elseif ( trim( $_POST['action_type'] ) === 'Delivered' ) {
			update_post_meta( $post_id, 'action', 'Delivered' );
			$wrh_data      = wp_json_encode(
				array(
					'order_title'   => $orderid,
					'sample_action' => 'delivered',
				)
			);
			$api_secret    = get_option( 'hoodslyhub_api_credentials' );
			$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
			wp_remote_post(
				$quickship_status_end_point,
				array(
					'body'    => $wrh_data,
					'headers' => array(
						'content-type'  => 'application/json',
						'Api-Signature' => $api_signature,
					),
				)
			);
		}

		/*Order History updated*/
		$vent_picked_date          = current_time( 'mysql' );
		$order_summery_vent_picked = array(
			array(
				'summery' => 'Order Picked',
				'date'    => $vent_picked_date,
			),
		);
		$order_summery             = get_post_meta( $post_id, 'order_summery', true );
		$order_summery_hoodsly     = $order_summery;
		if ( ! empty( $order_summery_hoodsly ) && is_array( $order_summery_hoodsly ) ) {
			$order_summery_array = array_merge( $order_summery_hoodsly, $order_summery_vent_picked );
		} else {
			$order_summery_array = $order_summery_vent_picked;
		}

		update_post_meta( $post_id, 'order_summery', $order_summery_array );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Ventilation Order Picked.',
			)
		);
	}
}

add_action( 'wp_ajax_quick_ship_order_action_status', 'quick_ship_order_action_status' );

/**
 * Accessory order mark completed
 */
function accessory_order_action_status() {
	$permission = check_ajax_referer( 'accessory_order_completed', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = $_REQUEST['post_id'];
		$orderid = $_REQUEST['orderid'];
		//$setting_api = new hoodslyhub_Settings();
		//$req_url     = $setting_api->get_option( 'quickship_status_endpoint', 'HoodslyHub_quickship_setting', 'text' );
		$api_endpoint               = get_option( 'hoodslyhub_api_settings' );
		$quickship_status_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'quickship_status_end_point' === $value['end_point_type'] ) {
				$quickship_status_end_point = $value['end_point_url'];
			}
		}
		if ( trim( $_POST['action_type'] ) === 'Mark Order Complete' ) {
			update_post_meta( $post_id, 'action', 'Completed' );
			$wrh_data      = wp_json_encode(
				array(
					'order_title'   => $orderid,
					'sample_action' => 'Completed',
				)
			);
			$api_secret    = get_option( 'hoodslyhub_api_credentials' );
			$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
			wp_remote_post(
				$quickship_status_end_point,
				array(
					'body'    => $wrh_data,
					'headers' => array(
						'content-type'  => 'application/json',
						'Api-Signature' => $api_signature,
					),
				)
			);
		} elseif ( trim( $_POST['action_type'] ) === 'Print' ) {
			update_post_meta( $post_id, 'action', 'In Production' );
			$wrh_data      = wp_json_encode(
				array(
					'order_title'   => $orderid,
					'sample_action' => 'In Production',
				)
			);
			$api_secret    = get_option( 'hoodslyhub_api_credentials' );
			$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
			wp_remote_post(
				$quickship_status_end_point,
				array(
					'body'    => $wrh_data,
					'headers' => array(
						'content-type'  => 'application/json',
						'Api-Signature' => $api_signature,
					),
				)
			);
		}

		/*Order History updated*/
		$vent_picked_date          = current_time( 'mysql' );
		$order_summery_vent_picked = array(
			array(
				'summery' => 'Order Picked',
				'date'    => $vent_picked_date,
			),
		);
		$order_summery             = get_post_meta( $post_id, 'order_summery', true );
		$order_summery_hoodsly     = $order_summery;
		if ( ! empty( $order_summery_hoodsly ) && is_array( $order_summery_hoodsly ) ) {
			$order_summery_array = array_merge( $order_summery_hoodsly, $order_summery_vent_picked );
		} else {
			$order_summery_array = $order_summery_vent_picked;
		}

		update_post_meta( $post_id, 'order_summery', $order_summery_array );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Ventilation Order Picked.',
			)
		);
	}
}

add_action( 'wp_ajax_accessory_order_action', 'accessory_order_action_status' );
/**
 * Send notification  to CSM as stock equal 0
 *
 * @return void
 */
function poke_wrh_if_zero() {
	$notifcation  = wp_insert_post(
		array(
			'post_title'   => 'variation_stock_out',
			'post_type'    => 'order_communication',
			'post_content' => 'Quick Ship Product Variations (' . $_POST['variation_id'] . ') stock out',
			'post_status'  => 'publish',
		)
	);
	$current_time = current_time( 'mysql' );
	update_post_meta( $notifcation, 'stock_out_time', $current_time );
}

add_action( 'wp_ajax_poke_wrh_if_zero', 'poke_wrh_if_zero' );

/**
 * Added Cron Schedules
 */
add_filter(
	'cron_schedules',
	function ( $schedules ) {
		$schedules['after_two_days']   = array(
			'interval' => 60 * 60 * 24 * 2,
			'display'  => __( 'After Two Days' ),
		);
		$schedules['after_three_days'] = array(
			'interval' => 60 * 60 * 24 * 3,
			'display'  => __( 'After Three Days' ),
		);
		$schedules['one_minute']       = array(
			'interval' => 60,
			'display'  => __( 'Every One Minute' ),
		);

		return $schedules;
	}
);

add_action(
	'init',
	function () {
		// add_action( 'notice_if_status_not_shipped_1', 'execute_after_two_days' );
		//add_action( 'notice_if_status_not_delivered', 'execute_after_three_days' );
		if ( ! wp_next_scheduled( 'notice_if_status_not_delivered' ) ) {
			//wp_schedule_event( time(), 'one_minute', 'notice_if_status_not_shipped_1' );
			//wp_schedule_event( time(), 'one_minute', 'notice_if_status_not_delivered' );
		}
	}
);

function execute_after_three_days() {
	$args       = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => - 1,
		'orderby'        => 'ID',
		'meta_query'     => array(
			array(
				'key'     => 'product_cat',
				'value'   => 'quick-shipping',
				'compare' => 'LIKE',
			),
		),
	);
	$all_orders = new WP_Query( $args );
	if ( $all_orders->have_posts() ) {
		while ( $all_orders->have_posts() ) {
			$all_orders->the_post();
			$order_status = get_post_meta( get_the_ID(), 'samples_status', true );
			$order_id     = get_post_meta( get_the_ID(), 'order_id', true );

			if ( $order_status != 'Delivered' ) {
				$notifcation  = wp_insert_post(
					array(
						'post_title'   => $order_id,
						'post_type'    => 'order_communication',
						'post_content' => 'Your #' . $order_id . ' is not delivered yet',
						'post_status'  => 'publish',
					)
				);
				$current_time = current_time( 'mysql' );
				update_post_meta( $notifcation, 'stock_out_time', $current_time );
				update_post_meta( $notifcation, 'poke_two_role', 'yes' );
				// wp_clear_scheduled_hook('notice_if_status_not_shipped');
				$args  = array(
					'role' => 'warehouse',
					'role' => 'transportation',
				);
				$users = get_users( $args );
				foreach ( $users as $user ) {
					$notification_count_meta = get_user_meta( $user->data->ID, 'notification_count' );
					$notification_count      = isset( $notification_count_meta ) ? $notification_count_meta : array( 0 => '0' );
					$notification_count[0]  += 1;
					update_user_meta( $user->data->ID, 'notification_count', $notification_count[0] );
				}
			}
		}
	}
}

function execute_after_two_days() {
	$args       = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => - 1,
		'orderby'        => 'ID',
		'meta_query'     => array(
			array(
				'key'     => 'product_cat',
				'value'   => 'quick-shipping',
				'compare' => 'LIKE',
			),
		),
	);
	$all_orders = new WP_Query( $args );
	if ( $all_orders->have_posts() ) {
		while ( $all_orders->have_posts() ) {
			$all_orders->the_post();
			$order_status = get_post_meta( get_the_ID(), 'order_status', true );
			$order_id     = get_post_meta( get_the_ID(), 'order_id', true );
			if ( $order_status != 'Shipped' ) {
				$notifcation  = wp_insert_post(
					array(
						'post_title'   => $order_id,
						'post_type'    => 'order_communication',
						'post_content' => 'Your #' . $order_id . ' is not shipped yet',
						'post_status'  => 'publish',
					)
				);
				$current_time = current_time( 'mysql' );
				update_post_meta( $notifcation, 'stock_out_time', $current_time );
				// wp_clear_scheduled_hook('notice_if_status_not_shipped');
				$args  = array(
					'role' => 'warehouse',
					'role' => 'transportation',
				);
				$users = get_users( $args );
				foreach ( $users as $user ) {
					$notification_count_meta = get_user_meta( $user->data->ID, 'notification_count' );
					$notification_count      = isset( $notification_count_meta ) ? $notification_count_meta : array( 0 => '0' );
					$notification_count[0]  += 1;
					update_user_meta( $user->data->ID, 'notification_count', $notification_count[0] );
				}
			}
		}
	}
}

/**
 * Added custom user role
 *
 * @return void
 */
function hoodslyhub__update_custom_roles() {
	add_role( 'warehouse', 'Warehouse', get_role( 'editor' )->capabilities );
	add_role( 'transportation', 'Transportation', get_role( 'editor' )->capabilities );
	add_role( 'driver', 'Driver', get_role( 'editor' )->capabilities );
}

add_action( 'init', 'hoodslyhub__update_custom_roles' );

function load_more_post_ajax() {
	$ppp  = ( isset( $_POST['ppp'] ) ) ? $_POST['ppp'] : 3;
	$page = ( isset( $_POST['pageNumber'] ) ) ? $_POST['pageNumber'] : 0;

	header( 'Content-Type: text/html' );

	$args = array(
		'post_type'      => 'order_communication',
		'posts_per_page' => $ppp,
		'paged'          => $page,
	);
	$loop = new WP_Query( $args );
	$out  = '';

	if ( $loop->have_posts() ) :
		while ( $loop->have_posts() ) :
			$loop->the_post();
			$stock_out_time = get_post_meta( get_the_ID(), 'stock_out_time', true );
			$stock_out_time = ! empty( $stock_out_time ) ? $stock_out_time : get_the_date( 'Y-m-d h:i:sa' );
			$out           .= '
		<li>' . get_the_content() . ' <span class="comment_date"><i>' . $stock_out_time . '</i></span></li><hr>';

		endwhile;
	endif;
	wp_reset_postdata();
	die( $out );
}

add_action( 'wp_ajax_load_more_post_ajax', 'load_more_post_ajax' );

/**
 * Update order status bulk
 *
 * @return void
 */

function update_order_status_bulk() {
	if ( ! empty( $_POST['orderid_array'] ) ) {
		$postid_array  = filter_input( INPUT_POST, 'postid_array', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$orderid_array = filter_input( INPUT_POST, 'orderid_array', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
		$ordersource   = filter_input( INPUT_POST, 'origin', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$status_label  = '';
		$imageUrl      = esc_url( get_theme_file_uri( 'assets/images/drop_of_proof_off.png' ) );
		foreach ( $postid_array as $value ) {
			$status_label = filter_input( INPUT_POST, 'status_label', FILTER_SANITIZE_STRING );
			if ( 'CNC' === $status_label ) {
				update_post_meta( $value, 'production_que', 'yes' );
			}
			update_post_meta( $value, 'order_status', $status_label );
			$_thumbnail_id = get_post_meta( $value, '_thumbnail_id', $value );
			if ( 'Packaged' === $status_label && empty( $_thumbnail_id ) ) {
				wp_send_json(
					array(
						'error'    => true,
						'msg'      => 'Please upload drop of proof off image. Please see the example avobe',
						'imageurl' => $imageUrl,
					)
				);
				wp_die();
			}
		}
		//if ('move_to_production_que' === $status_label && ){
		//
		//}
		$wrh_data                    = wp_json_encode(
			array(
				'order_id'     => $orderid_array,
				'order_status' => $status_label,
				'origin'       => $ordersource[0],
			)
		);
		$api_endpoint                = get_option( 'hoodslyhub_api_settings' );
		$bulk_order_status_end_point = '';
		foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
			if ( 'bulk_status_update_end_point' === $value['end_point_type'] ) {
				$bulk_order_status_end_point = $value['end_point_url'];
			}
		}
		$api_secret    = get_option( 'hoodslyhub_api_credentials' );
		$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
		wp_remote_post(
			$bulk_order_status_end_point,
			array(
				'body'    => $wrh_data,
				'headers' => array(
					'content-type'  => 'application/json',
					'Api-Signature' => $api_signature,
				),
			)
		);
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Order Status Updated Successfully.',
			)
		);
	} else {
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'Bulk Updating Status Failed...Please try again',
			)
		);
	}
}

add_action( 'wp_ajax_update_order_status_bulk', 'update_order_status_bulk' );


/*
 * Hub pagination Fuction
 */
if ( ! function_exists( 'shop_pagination' ) ) :

	function shop_pagination( $paged = '', $max_page = '' ) {
		$big = 999999999; // need an unlikely integer
		if ( ! $paged ) {
			$paged = get_query_var( 'paged' );
		}

		if ( ! $max_page ) {
			global $wp_query;
			$max_page = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		}

		echo paginate_links(
			array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, $paged ),
				'total'     => $max_page,
				'prev_next' => true,
				'prev_text' => '<span class="pagination paginate-prev"><span><i class="icon-angle-left"></i></span></span> ',
				'next_text' => '<span class="pagination paginate-next ml-auto"><span><i class="icon-angle-right"></i></span></span>',
			)
		);
	}
endif;

/*
 * Hub pagination Fuction
 */
add_action( 'wp_ajax_pagination_ajax', 'wrh_pagination_ajax' );
function wrh_pagination_ajax( $paged = '', $max_page = '' ) {

	$hub_paged = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
	$max_page  = ( isset( $_POST['max_page'] ) ) ? $_POST['max_page'] : 1;
	$big       = 999999999; // need an unlikely integer

	echo paginate_links(
		array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max( 1, $hub_paged ),
			'total'     => $max_page,
			'prev_next' => true,
			'prev_text' => '<span class="pagination paginate-prev"><span><i class="icon-angle-left"></i></span></span> ',
			'next_text' => '<span class="pagination paginate-next ml-auto"><span><i class="icon-angle-right"></i></span></span>',
		)
	);
	die();
}

/*
 * WRH order table pagination
 */
add_action( 'wp_ajax_wrh_order_table_pagination', 'hub_wrh_order_table_pagination' );
function hub_wrh_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
				'value'   => array( 'cnc', 'On Hold' ),
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
			$backgroundg_color           = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
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
}

/*
 * Pending order table pagination
 */
add_action( 'wp_ajax_pending_order_table_pagination', 'hub_pending_order_table_pagination' );
function hub_pending_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
	$default_posts_per_page = get_option( 'posts_per_page' );
	$args                   = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => $default_posts_per_page,
		'paged'          => $hub_paged,
		'orderby'        => 'title',
		'meta_query'     => array(
			array(
				'key'     => 'order_status',
				'value'   => 'On Hold',
				'compare' => 'LIKE',
			),
		),
	);
	$pending_orders         = new WP_Query( $args );
	if ( $pending_orders->have_posts() ) {
		while ( $pending_orders->have_posts() ) {
			$pending_orders->the_post();
			$order_id          = get_post_meta( get_the_ID(), 'order_id', true );
			$first_name        = get_post_meta( get_the_ID(), 'first_name', true );
			$last_name         = get_post_meta( get_the_ID(), 'last_name', true );
			$order_status      = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
			$order_date        = trim( get_post_meta( get_the_ID(), 'order_date', true ) );
			$completion_date   = trim( get_post_meta( get_the_ID(), 'completion_date', true ) );
			$date_placed       = gmdate( 'F jS Y', strtotime( $order_date ) );
			$samples_status    = trim( get_post_meta( get_the_ID(), 'samples_status', true ) );
			$origin            = get_post_meta( get_the_ID(), 'origin', true );
			$backgroundg_color = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$status_color      = ( 'Received' === $samples_status ) ? 'style=background-color:#A8DCD7' : ( ( 'Picked Up' === $samples_status ) ? 'style=background-color:#DCD8A8' : ( ( 'Delivered' === $samples_status ) ? 'style=background-color:#BEA8DC' : 'style=background-color:#F09D9D' ) );
			?>
			<tr style="background-color: #0E9CEE1a;">
				<td data-title="Order Id">#<?php the_title(); ?></td>
				<td data-title="Order Date">
					<?php echo esc_html( $completion_date ); ?>
				</td>
				<td data-title="Status" class="staus-dropdown dropdown">
					<button role="button" class="btn btn-waiting"<?php echo esc_attr( $status_color ); ?> data-toggle="dropdown">
						<?php echo esc_html( $order_status ); ?>
					</button>
					<?php if ( current_user_can( 'administrator' ) ) : ?>
						<?php if ( 'On Hold' === $order_status ) : ?>
							<ul class="dropdown-menu dropdown-menu-right">
							<li>
								<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo intval( $order_id ); ?>"
								   class="wrh-pending-status-action"
								   data-nonce="<?php echo esc_attr( wp_create_nonce( 'wrh_order_pending_to_production_nonce' ) ); ?>">
									<?php esc_html_e( 'In Production', 'hoodslyhub' ); ?>
								</a>
							</li>
						<?php endif; ?>
						</ul>
					<?php endif ?>
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
}

/*
 * Completed order table pagination
 */
add_action( 'wp_ajax_completed_order_table_pagination', 'hub_completed_order_table_pagination' );
function hub_completed_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
	$default_posts_per_page = get_option( 'posts_per_page' );
	$args                   = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => $default_posts_per_page,
		'paged'          => $hub_paged,
		'orderby'        => 'title',
		'meta_query'     => array(
			array(
				'key'     => 'order_status',
				'value'   => 'cnc',
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
	}
	wp_reset_postdata();
}

/*
 * CCM order table pagination
 */
add_action( 'wp_ajax_ccm_order_table_pagination', 'hub_ccm_order_table_pagination' );
function hub_ccm_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
	$default_posts_per_page = get_option( 'posts_per_page' );
	$args                   = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => $default_posts_per_page,
		'paged'          => $hub_paged,
		'orderby'        => 'title',
		'meta_query'     => array(
			array(
				'key'     => 'custom_color_match',
				'value'   => '1',
				'compare' => 'LIKE',
			),

		),
	);
	$custom_color_orders = new WP_Query( $args );
	if ( $custom_color_orders->have_posts() ) {
		while ( $custom_color_orders->have_posts() ) {
			$custom_color_orders->the_post();
			$order_status   = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
			$order_date     = trim( get_post_meta( get_the_ID(), 'order_date', true ) );
			$date_placed    = gmdate( 'F jS Y', strtotime( $order_date ) );
			$samples_status = trim( get_post_meta( get_the_ID(), 'samples_status', true ) );
			$status_color   = ( 'Received' === $samples_status ) ? 'style=background-color:#A8DCD7' : ( ( 'Picked Up' === $samples_status ) ? 'style=background-color:#DCD8A8' : ( ( 'Delivered' === $samples_status ) ? 'style=background-color:#BEA8DC' : 'style=background-color:#F09D9D' ) );
			?>
			<?php if ( 'Delivered' !== $samples_status ) : ?>
				<tr style="background-color: #0E9CEE1a;">
					<td data-title="Order Id">#<?php the_title(); ?></td>
					<td data-title="Order Date">
						<?php echo esc_html( $date_placed ); ?>
					</td>
					<td data-title="Status" class="staus-dropdown dropdown">
						<button role="button" class="btn btn-waiting"<?php echo esc_attr( $status_color ); ?> data-toggle="dropdown">
							<?php echo esc_html( $samples_status ); ?>
						</button>
						<?php if ( current_user_can( 'administrator' ) ) : ?>
							<?php if ( 'Waiting' === $samples_status ) : ?>
								<ul class="dropdown-menu dropdown-menu-right">
								<li>
									<a href="#" data-postid="<?php echo get_the_ID(); ?>" class="wrh-ccm-received"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'wrh_received_nonce' ) ); ?>">
										<?php esc_html_e( 'Received', 'hoodslyhub' ); ?>
									</a>
								</li>
							<?php endif; ?>
							</ul>
						<?php endif ?>
					</td>
				</tr>
				<?php
			endif;
		}
	} else {
		?>
		<tr style="background-color: #4747471a;">
			<td colspan="100%" class="text-left"><?php esc_html_e( 'There is no order yet', 'hoodslyhub' ); ?></td>
		</tr>
		<?php
	}
	wp_reset_postdata();
}

/*
 * Vent  order table pagination
 */
add_action( 'wp_ajax_vent_order_table_pagination', 'hub_vent_order_table_pagination' );
function hub_vent_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
	$default_posts_per_page = get_option( 'posts_per_page' );
	$args                   = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => $default_posts_per_page,
		'paged'          => $hub_paged,
		'orderby'        => 'title',
		'meta_query'     => array(
			array(
				'key'     => 'is_tradewinds_selected',
				'value'   => 'yes',
				'compare' => 'LIKE',
			),
		),
	);
	$all_orders             = new WP_Query( $args );
	if ( $all_orders->have_posts() ) {
		while ( $all_orders->have_posts() ) {
			$all_orders->the_post();
			$shipping_add            = get_post_meta( get_the_ID(), 'shipping', true );
			$shipping                = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
			$first_name              = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
			$last_name               = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
			$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
			$assign_shop             = trim( get_post_meta( get_the_ID(), 'shop', true ) );
			$shop                    = ( isset( $assign_shop ) && ! empty( $assign_shop ) ) ? $assign_shop : 'Not Assigned Yet';
			$es_shipping_date        = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
			$shipping_date           = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
			$estimated_shipping_date = $shipping_date ?? '';
			$origin                  = get_post_meta( get_the_ID(), 'origin', true );
			$domain_parts            = explode( '.', $origin );
			$order_link              = get_template_link( 't_order-details.php' );
			$order_id                = get_post_meta( get_the_ID(), 'order_id', true );
			$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
			$current_date            = gmdate( 'm/d/Y H:i:s', time() );
			$date1                   = strtotime( $estimated_shipping_date );
			$date2                   = strtotime( $current_date );
			$date_difference         = $date1 - $date2;
			$bill_of_landing_id      = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
			$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
			$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
			$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$result                  = round( $date_difference / ( 60 * 60 * 24 ) );
			?>
			<tr style="background-color: #4747471a;">
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
												)
												?>
													"><?php the_title(); ?></a>
				<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
				<td data-title="Order Status">
					<button class="btn btn-violet"<?php echo esc_attr( $backgroundg_color ); ?>>
						<?php echo esc_html( $order_status ); ?>
					</button>
				</td>
				<td data-title="Shop" data-toggle="tooltip"
					title='<h6 class="title"><?php echo esc_html( $shop ); ?></h6>'>
					<figure class="media-shop">
						<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), esc_html( get_bloginfo( 'name' ) ) ); ?>
					</figure>
				</td>
				<td data-title="Estimated Shipping Date">
					<?php echo esc_html( $estimated_shipping_date ) . ' (' . esc_html( $result ) . ' Days)'; ?>
				</td>
				<td data-title="Order Source">
					<?php
					echo esc_html(
						ucfirst(
							$domain_parts[0]
						)
					)
					?>
				</td>
				<td data-title="Items" id="ordered_items">
					<?php
					foreach ( $line_items['line_items'] as $key => $value ) {
						if ( isset( $value['tradewinds_sku'] ) ) :
							echo $value['tradewinds_sku'];
						endif;
					}
					?>
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
															"><?php esc_html_e( 'View', 'hoodslyhub' ); ?></a></li>
						<?php if ( current_user_can( 'administrator' ) ) : ?>
							<li>
								<a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order"
								   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_delete_order_nonce' ) ); ?>">
									<?php esc_html_e( 'Delete', 'hoodslyhub' ); ?>
								</a>
							</li>
						<?php endif ?>
						<?php if ( 'In Production' === $order_status ) : ?>
							<?php if ( current_user_can( 'administrator' ) ) : ?>
								<li>
									<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo esc_html( $order_id ); ?>"
									   class="hoodslyhub-order-hold"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_order_hold_nonce' ) ); ?>">
										<?php esc_html_e( 'Order Hold', 'hoodslyhub' ); ?>
									</a>
								</li>
							<?php endif ?>
						<?php endif; ?>
					</ul>
				</td>
			</tr>
			<?php
		} // end while
	} // end if
	wp_reset_postdata();
}

/*
 * Vent completed order table pagination
 */
add_action( 'wp_ajax_vent_completed_order_table_pagination', 'hub_vent_completed_order_table_pagination' );
function hub_vent_completed_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
	$default_posts_per_page = get_option( 'posts_per_page' );
	$args                   = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => $default_posts_per_page,
		'paged'          => $hub_paged,
		'orderby'        => 'title',
		'meta_query'     => array(
			array(
				'key'     => 'is_tradewinds_selected',
				'value'   => 'yes',
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'action',
				'value'   => 'Delivered',
				'compare' => 'LIKE',
			),
		),
	);
	$all_orders             = new WP_Query( $args );
	if ( $all_orders->have_posts() ) {
		while ( $all_orders->have_posts() ) {
			$all_orders->the_post();
			$shipping_add            = get_post_meta( get_the_ID(), 'shipping', true );
			$shipping                = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
			$first_name              = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
			$last_name               = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
			$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
			$assign_shop             = trim( get_post_meta( get_the_ID(), 'shop', true ) );
			$shop                    = ( isset( $assign_shop ) && ! empty( $assign_shop ) ) ? $assign_shop : 'Not Assigned Yet';
			$es_shipping_date        = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
			$shipping_date           = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
			$estimated_shipping_date = $shipping_date ?? '';
			$origin                  = get_post_meta( get_the_ID(), 'origin', true );
			$domain_parts            = explode( '.', $origin );
			$order_link              = get_template_link( 't_order-details.php' );
			$order_id                = get_post_meta( get_the_ID(), 'order_id', true );
			$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
			$current_date            = gmdate( 'm/d/Y H:i:s', time() );
			$date1                   = strtotime( $estimated_shipping_date );
			$date2                   = strtotime( $current_date );
			$date_difference         = $date1 - $date2;
			$bill_of_landing_id      = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
			$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
			$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
			$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$result                  = round( $date_difference / ( 60 * 60 * 24 ) );
			?>
			<tr style="background-color: #4747471a;">
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
								)
								?>
									"><?php the_title(); ?></a>
				<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
				<td data-title="Order Status">
					<button class="btn btn-violet"<?php echo esc_attr( $backgroundg_color ); ?>>
						<?php echo esc_html( $order_status ); ?>
					</button>
				</td>
				<td data-title="Shop" data-toggle="tooltip"
					title='<h6 class="title"><?php echo esc_html( $shop ); ?></h6>'>
					<figure class="media-shop">
						<?php printf( '<img src="%s" class="img-fluid" alt="%s">', esc_url( get_theme_file_uri( 'assets/images/ryan.jpg' ) ), esc_html( get_bloginfo( 'name' ) ) ); ?>
					</figure>
				</td>
				<td data-title="Estimated Shipping Date">
					<?php echo esc_html( $estimated_shipping_date ) . ' (' . esc_html( $result ) . ' Days)'; ?>
				</td>
				<td data-title="Order Source">
					<?php
					echo esc_html(
						ucfirst(
							$domain_parts[0]
						)
					)
					?>
				</td>
				<td data-title="Items" id="ordered_items">
					<button type="button" class="btn" data-toggle="modal"
							data-target="#test_<?php echo esc_html( $order_id ); ?>">
						<?php echo isset( $line_items['line_items'] ) ? count( $line_items['line_items'] ) : ''; ?>
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
											"><?php esc_html_e( 'View', 'hoodslyhub' ); ?></a></li>
						<?php if ( current_user_can( 'administrator' ) ) : ?>
							<li>
								<a href="#" data-orderid="<?php echo get_the_ID(); ?>" class="hoodslyhub-delete-order"
								   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_delete_order_nonce' ) ); ?>">
									<?php esc_html_e( 'Delete', 'hoodslyhub' ); ?>
								</a>
							</li>
						<?php endif ?>
						<?php if ( 'In Production' === $order_status ) : ?>
							<?php if ( current_user_can( 'administrator' ) ) : ?>
								<li>
									<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo esc_html( $order_id ); ?>"
									   class="hoodslyhub-order-hold"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_order_hold_nonce' ) ); ?>">
										<?php esc_html_e( 'Order Hold', 'hoodslyhub' ); ?>
									</a>
								</li>
							<?php endif ?>
						<?php endif; ?>
					</ul>
				</td>
			</tr>
			<div class="modal fade" id="test_<?php echo esc_html( $order_id ); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
				 aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<?php
							$i = 0;
							foreach ( $line_items['line_items'] as $key => $value ) {
								echo '<div class="order-item-popup">';
								echo '<p><b>Product Name: </b>' . esc_html( $value['product_name'] ) . '</p>';
								echo '<p><b>Quantiry: </b>' . intval( $value['quantity'] ) . '</p>';
								echo '<p><b>Price: </b>' . esc_html( $value['item_total'] ) . '</p>';
								echo '</div>';
								?>
								<?php
								$i ++;
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
		} // end while
	} // end if
	wp_reset_postdata();
}

/*
 * Quick Shipping order table pagination
 */
add_action( 'wp_ajax_quick_order_table_pagination', 'hub_quick_order_table_pagination' );
function hub_quick_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
		),
	);
	$all_orders             = new WP_Query( $args );
	if ( $all_orders->have_posts() ) {
		while ( $all_orders->have_posts() ) {
			$all_orders->the_post();
			$shipping_add            = get_post_meta( get_the_ID(), 'shipping', true );
			$shipping                = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
			$first_name              = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
			$last_name               = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
			$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
			$vent_status             = get_post_meta( get_the_ID(), 'action', true );
			$vent_status             = ! empty( $vent_status ) ? $vent_status : 'Waiting';
			$assign_shop             = trim( get_post_meta( get_the_ID(), 'shop', true ) );
			$shop                    = ( isset( $assign_shop ) && ! empty( $assign_shop ) ) ? $assign_shop : 'Not Assigned Yet';
			$es_shipping_date        = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
			$shipping_date           = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
			$estimated_shipping_date = $shipping_date ?? '';
			$origin                  = get_post_meta( get_the_ID(), 'origin', true );
			$domain_parts            = explode( '.', $origin );
			$order_link              = get_template_link( 't_order-details.php' );
			$order_id                = get_post_meta( get_the_ID(), 'order_id', true );
			$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
			$current_date            = gmdate( 'm/d/Y H:i:s', time() );
			$date1                   = strtotime( $estimated_shipping_date );
			$date2                   = strtotime( $current_date );
			$date_difference         = $date1 - $date2;
			$bill_of_landing_id      = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
			$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
			$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
			$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$status_color            = ( 'Received' === $vent_status ) ? 'style=background-color:#A8DCD7' : ( ( 'Picked' === $vent_status ) ? 'style=background-color:#DCD8A8' : ( ( 'Delivered' === $vent_status ) ? 'style=background-color:#BEA8DC' : 'style=background-color:#F09D9D' ) );
			$result                  = round( $date_difference / ( 60 * 60 * 24 ) );
			?>
			<tr style="background-color: #4747471a;">
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
												)
												?>
													"><?php the_title(); ?></a>
				<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
				<td data-title="Order Status">
					<button class="btn btn-violet"<?php echo esc_attr( $backgroundg_color ); ?>>
						<?php echo esc_html( $order_status ); ?>
					</button>
				</td>
				<td data-title="Estimated Shipping Date">
					<?php echo esc_html( $estimated_shipping_date ) . ' (' . esc_html( $result ) . ' Days)'; ?>
				</td>
				<td data-title="Order Source">
					<?php
					echo esc_html(
						ucfirst(
							$domain_parts[0]
						)
					)
					?>
				</td>
				<td data-title="Items" id="ordered_items">
					<button type="button" class="btn" data-toggle="modal"
							data-target="#test_<?php echo esc_html( $order_id ); ?>">
						<?php echo isset( $line_items['line_items'] ) ? count( $line_items['line_items'] ) : ''; ?>
					</button>
				</td>
				<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right"
					title='<h6 class="title">Order Files</h6><ul class="tooltip-dropdown">
													<?php if ( $bol_link ) : ?>
													<li>BOL</li>
														<?php
					endif;
													if ( $shipping_file_link ) :
														?>
													<li>Shipping Label</li>
																		<?php endif; ?>
													</ul>'>
					<?php esc_html_e( 'Files', 'hoodslyhub' ); ?>
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
															"><?php esc_html_e( 'View', 'hoodslyhub' ); ?></a></li>
						<?php if ( current_user_can( 'administrator' ) ) : ?>
							<li>
								<a href="#" data-orderid="<?php echo get_the_ID(); ?>"
								   class="hoodslyhub-delete-order"
								   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_delete_order_nonce' ) ); ?>">
									<?php esc_html_e( 'Delete', 'hoodslyhub' ); ?>
								</a>
							</li>
						<?php endif ?>
						<?php if ( 'In Production' === $order_status ) : ?>
							<?php if ( current_user_can( 'administrator' ) ) : ?>
								<li>
									<a href="#" data-postid="<?php echo get_the_ID(); ?>"
									   data-orderid="<?php echo esc_html( $order_id ); ?>"
									   class="hoodslyhub-order-hold"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_order_hold_nonce' ) ); ?>">
										<?php esc_html_e( 'Order Hold', 'hoodslyhub' ); ?>
									</a>
								</li>
							<?php endif ?>
						<?php endif; ?>
					</ul>
				</td>
			</tr>
			<div class="modal fade" id="test_<?php echo esc_html( $order_id ); ?>" tabindex="-1" role="dialog"
				 aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<?php
							$i = 0;
							foreach ( $line_items['line_items'] as $key => $value ) {
								echo '<div class="order-item-popup">';
								echo '<p><b>Product Name: </b>' . esc_html( $value['product_name'] ) . '</p>';
								echo '<p><b>Quantiry: </b>' . intval( $value['quantity'] ) . '</p>';
								echo '<p><b>Price: </b>' . esc_html( $value['item_total'] ) . '</p>';
								echo '</div>';
								?>
								<?php
								$i ++;
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
		} // end while
	} else {
		?>
		<tr style="background-color: #4747471a;">
			<td colspan="100%" class="text-left"><?php esc_html_e( 'There is no order yet', 'hoodslyhub' ); ?></td>
		</tr>
		<?php
	} // end if
	wp_reset_postdata();
}

/*
 * Quick Shipping order table pagination
 */
add_action( 'wp_ajax_quick_completed_order_table_pagination', 'hub_quick_completed_order_table_pagination' );
function hub_quick_completed_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
				'key'     => 'action',
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
			$shipping_add            = get_post_meta( get_the_ID(), 'shipping', true );
			$shipping                = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
			$first_name              = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
			$last_name               = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
			$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
			$origin                  = get_post_meta( get_the_ID(), 'origin', true );
			$domain_parts            = explode( '.', $origin );
			$current_date            = gmdate( 'm/d/Y H:i:s', time() );
			$date1                   = strtotime( $estimated_shipping_date );
			$date2                   = strtotime( $current_date );
			$date_difference         = $date1 - $date2;
			$result                  = round( $date_difference / ( 60 * 60 * 24 ) );
			$order_status_array      = array( 'In Production', 'Pre Assembly', 'Assembly', 'Sanding', 'Finishing' );
			$backgroundg_color       = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
			$bill_of_landing_id      = intval( get_post_meta( get_the_ID(), 'bill_of_landing_id', true ) );
			$bol_link                = home_url() . '/wp-content/uploads/bol/' . $bill_of_landing_id . '.pdf';
			$shipping_file_link      = home_url() . '/wp-content/uploads/bol/shipping_label_' . $bill_of_landing_id . '.pdf';
			$assign_shop             = trim( get_post_meta( get_the_ID(), 'shop', true ) );
			$shop                    = ( isset( $assign_shop ) && ! empty( $assign_shop ) ) ? $assign_shop : 'Not Assigned Yet';

			?>
			<tr style="background-color: #4747471a;">
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
												)
												?>
													"><?php the_title(); ?></a>
				<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
				<td data-title="Order Status">
					<button class="btn btn-violet"<?php echo esc_attr( $backgroundg_color ); ?>>
						<?php echo esc_html( $order_status ); ?>
					</button>
				</td>
				<td data-title="Estimated Shipping Date">
					<?php echo esc_html( $estimated_shipping_date ) . ' (' . esc_html( $result ) . ' Days)'; ?>
				</td>
				<td data-title="Order Source">
					<?php
					echo esc_html(
						ucfirst(
							$domain_parts[0]
						)
					)
					?>
				</td>
				<td data-title="Items" id="ordered_items">
					<button type="button" class="btn" data-toggle="modal"
							data-target="#test_<?php echo esc_html( $order_id ); ?>">
						<?php echo isset( $line_items['line_items'] ) ? count( $line_items['line_items'] ) : ''; ?>
					</button>
				</td>
				<td class="files" data-title="Files" data-toggle="tooltip" data-placement="right"
					title='<h6 class="title">Order Files</h6><ul class="tooltip-dropdown">
													<?php if ( $bol_link ) : ?>
													<li>BOL</li>
														<?php
					endif;
													if ( $shipping_file_link ) :
														?>
													<li>Shipping Label</li>
																					<?php endif; ?>
													</ul>'>
					<?php esc_html_e( 'Files', 'hoodslyhub' ); ?>
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
															"><?php esc_html_e( 'View', 'hoodslyhub' ); ?></a></li>
						<?php if ( current_user_can( 'administrator' ) ) : ?>
							<li>
								<a href="#" data-orderid="<?php echo get_the_ID(); ?>"
								   class="hoodslyhub-delete-order"
								   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_delete_order_nonce' ) ); ?>">
									<?php esc_html_e( 'Delete', 'hoodslyhub' ); ?>
								</a>
							</li>
						<?php endif ?>
						<?php if ( 'In Production' === $order_status ) : ?>
							<?php if ( current_user_can( 'administrator' ) ) : ?>
								<li>
									<a href="#" data-postid="<?php echo get_the_ID(); ?>"
									   data-orderid="<?php echo esc_html( $order_id ); ?>"
									   class="hoodslyhub-order-hold"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'hoodslyhub_order_hold_nonce' ) ); ?>">
										<?php esc_html_e( 'Order Hold', 'hoodslyhub' ); ?>
									</a>
								</li>
							<?php endif ?>
						<?php endif; ?>
					</ul>
				</td>
			</tr>
			<div class="modal fade" id="test_<?php echo esc_html( $order_id ); ?>" tabindex="-1" role="dialog"
				 aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<?php
							$i = 0;
							foreach ( $line_items['line_items'] as $key => $value ) {
								echo '<div class="order-item-popup">';
								echo '<p><b>Product Name: </b>' . esc_html( $value['product_name'] ) . '</p>';
								echo '<p><b>Quantiry: </b>' . intval( $value['quantity'] ) . '</p>';
								echo '<p><b>Price: </b>' . esc_html( $value['item_total'] ) . '</p>';
								echo '</div>';
								?>
								<?php
								$i ++;
							}
							?>
						</div>
					</div>
				</div>
			</div>
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
}

/*
 * Quick Shipping vent order table pagination
 */
add_action( 'wp_ajax_quick_vent_order_table_pagination', 'hub_quick_vent_order_table_pagination' );
function hub_quick_vent_order_table_pagination() {
	$hub_paged              = ( isset( $_POST['hub_paged'] ) ) ? $_POST['hub_paged'] : 1;
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
	$custom_color_orders    = new WP_Query( $args );
	if ( $custom_color_orders->have_posts() ) {
		while ( $custom_color_orders->have_posts() ) {
			$custom_color_orders->the_post();
			$order_id          = get_post_meta( get_the_ID(), 'order_id', true );
			$vent_status       = get_post_meta( get_the_ID(), 'action', true );
			$vent_status       = ! empty( $vent_status ) ? $vent_status : 'Waiting';
			$order_status      = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
			$assign_shop       = trim( get_post_meta( get_the_ID(), 'shop', true ) );
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
									<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo intval( $order_id ); ?>"
									   class="quick-ship-wrh-picked"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_ship_wrh_nonce' ) ); ?>">
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
									<a href="#" data-postid="<?php echo get_the_ID(); ?>" data-orderid="<?php echo intval( $order_id ); ?>"
									   class="quick-ship-wrh-delivered"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'quick_ship_wrh_nonce' ) ); ?>"><?php esc_html_e( 'Delivered', 'hoodslyhub' ); ?></a>
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
}

/**
 *  Shop claim approving Method
 */
function shop_claim_approved_request() {
	$permission = check_ajax_referer( 'shop_claim_approved_nonce', 'nonce', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'error',
			)
		);
		wp_die();
	} else {
		global $wpdb;
		$post_id = $_POST['post_id'];
		update_post_meta( $post_id, 'shop_claim', 'approved' );

		$bill_of_landing_id        = intval( get_post_meta( $post_id, 'bill_of_landing_id', true ) );
		$shipping                  = get_post_meta( $post_id, 'shipping', true );
		$billing                   = get_post_meta( $post_id, 'billing', true );
		$customer_note             = get_post_meta( $post_id, 'customer_note', true );
		$order_desc                = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM $wpdb->posts WHERE ID = %d AND post_type = %s", $post_id, 'wrh_order' ) );
		$order_status              = trim( get_post_meta( $post_id, 'order_status', true ) );
		$order_date                = get_post_meta( $post_id, 'order_date', true );
		$es_shipping_date          = get_post_meta( $post_id, 'estimated_shipping_date', true );
		$origin                    = get_post_meta( $post_id, 'origin', true );
		$meta_data_arr             = get_post_meta( $post_id, 'meta_data_arr', true );
		$product_names             = get_post_meta( $post_id, 'product_name', true );
		$product_cat               = get_post_meta( $post_id, 'product_cat', true );
		$product_cat_name          = get_post_meta( $post_id, 'product_cat_name', true );
		$tradewinds_quickship      = get_post_meta( $post_id, 'tradewinds_quickship', true );
		$product_sku               = get_post_meta( $post_id, 'item_sku', true );
		$custom_color_match        = get_post_meta( $post_id, 'custom_color_match', true );
		$shipping_lines            = get_post_meta( $post_id, 'shipping_lines', true );
		$order_id                  = get_post_meta( $post_id, 'order_id', true );
		$line_items                = get_post_meta( $post_id, 'line_items', true );
		$bol_pdf                   = get_post_meta( $post_id, 'bol_pdf', true );
		$shipping_label            = get_post_meta( $post_id, 'shipping_label', true );
		$samples_status            = get_post_meta( $post_id, 'shipping_label', true );
		$is_tradewinds_selected    = get_post_meta( $post_id, 'is_tradewinds_selected', true );
		$completion_date           = get_post_meta( $post_id, 'completion_date', true );
		$bol_regenerated           = get_post_meta( $post_id, 'bol_regenerated', true );
		$damage_claim_id           = get_post_meta( $post_id, 'damage_claim_id', true );
		$damage_item               = get_post_meta( $post_id, 'damage_item', true );
		$damage_type               = get_post_meta( $post_id, 'damage_type', true );
		$damage_details            = get_post_meta( $post_id, 'damage_details', true );
		$damage_claim_filling_date = get_post_meta( $post_id, 'damage_claim_filling_date', true );
		$damage_proof_submit_date  = get_post_meta( $post_id, 'damage_proof_submit_date', true );
		$claim_value               = get_post_meta( $post_id, 'claim_value', true );
		$hood_replace              = get_post_meta( $post_id, 'hood_replace', true );
		$f_shelf_replace           = get_post_meta( $post_id, 'f_shelf_replace', true );
		$hall_tree_replace         = get_post_meta( $post_id, 'hall_tree_replace', true );
		$no_replace                = get_post_meta( $post_id, 'no_replace', true );
		$damage_image_src          = get_post_meta( $post_id, 'damage_image_src', true );

		/**
		 * Inserting manual order based on rest api request data
		 */
		$wrh_order = array(
			'post_title'   => 'REP-' . $order_id,
			'post_content' => $order_desc,
			'post_status'  => 'publish',
			'post_date'    => current_time( 'mysql' ),
			'post_type'    => 'wrh_order',
		);

		$post_id = wp_insert_post( $wrh_order );
		/**
		 * Saving order data as meta to all_orders cpt
		 */

		add_post_meta( $post_id, 'estimated_shipping_date', $es_shipping_date );
		add_post_meta( $post_id, 'customer_note', $customer_note );
		add_post_meta( $post_id, 'origin', $origin );
		add_post_meta( $post_id, 'order_date', $order_date );
		add_post_meta( $post_id, 'order_id', 'REP-' . $order_id );
		add_post_meta( $post_id, 'meta_data_arr', $meta_data_arr );
		add_post_meta( $post_id, 'product_name', $product_names );
		add_post_meta( $post_id, 'product_cat', $product_cat );
		add_post_meta( $post_id, 'product_cat_name', $product_cat_name );
		add_post_meta( $post_id, 'tradewinds_quickship', $tradewinds_quickship );
		add_post_meta( $post_id, 'item_sku', $product_sku );
		add_post_meta( $post_id, 'order_status', $order_status );
		add_post_meta( $post_id, 'custom_color_match', $custom_color_match );
		add_post_meta( $post_id, 'billing', $billing );
		add_post_meta( $post_id, 'shipping', $shipping );
		add_post_meta( $post_id, 'bill_of_landing_id', $bill_of_landing_id );
		add_post_meta( $post_id, 'shipping_lines', $shipping_lines );
		add_post_meta( $post_id, 'line_items', $line_items );
		add_post_meta( $post_id, 'bol_pdf', $bol_pdf );
		add_post_meta( $post_id, 'shipping_label', $shipping_label );
		add_post_meta( $post_id, 'samples_status', $samples_status );
		add_post_meta( $post_id, 'is_tradewinds_selected', $is_tradewinds_selected );
		add_post_meta( $post_id, 'completion_date', $completion_date );
		add_post_meta( $post_id, 'bol_regenerated', $bol_regenerated );
		add_post_meta( $post_id, 'is_priority', 'yes' );
		add_post_meta( $post_id, 'damage_claim_id', $damage_claim_id );
		add_post_meta( $post_id, 'damage_item', $damage_item );
		add_post_meta( $post_id, 'damage_type', $damage_type );
		add_post_meta( $post_id, 'damage_details', $damage_details );
		add_post_meta( $post_id, 'damage_claim_filling_date', $damage_claim_filling_date );
		add_post_meta( $post_id, 'damage_proof_submit_date', $damage_proof_submit_date );
		add_post_meta( $post_id, 'damage_image_src', $damage_image_src );
		add_post_meta( $post_id, 'claim_value', $claim_value );
		add_post_meta( $post_id, 'hood_replace', $hood_replace );
		add_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
		add_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );
		add_post_meta( $post_id, 'no_replace', $no_replace );
		add_post_meta( $post_id, 'hood_replace', $hood_replace );
		add_post_meta( $post_id, 'f_shelf_replace', $f_shelf_replace );
		add_post_meta( $post_id, 'hall_tree_replace', $hall_tree_replace );
		add_post_meta( $post_id, 'no_replace', $no_replace );

		//$order_date = get_post_meta( $post_id, 'order_date', true );
		HoodslyHubHelper::add_order_history( $post_id, 'Claim Approved' );
		wp_send_json(
			array(
				'success' => true,
				'msg'     => 'success',
			)
		);
	}
}

add_action( 'wp_ajax_shop_claim_approved_request', 'shop_claim_approved_request' );

function send_to_local_del() {
	$stock_quantity = $_POST['stock_quantity'];
	$variation_id   = $_POST['variation_id'];
	$size_attr      = $_POST['size_attr'];
	$hub_data       = wp_json_encode(
		array(
			'variation_id'   => $variation_id,
			'stock_quantity' => $stock_quantity,
			'size_attr'      => $size_attr,
		)
	);
	$api_secret     = get_option( 'hoodslyhub_api_credentials' );
	$api_signature  = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
	$data           = wp_remote_post(
		'http://hoodslyhub.test/wp-json/stock_request/v1/locald',
		array(
			'body'    => $hub_data,
			'headers' => array(
				'content-type'  => 'application/json',
				'Api-Signature' => $api_signature,
			),
		)
	);
}
add_action( 'wp_ajax_send_to_local_del', 'send_to_local_del' );
/**
 * Undocumented function
 *
 * @return void
 */
function order_new_changes_approve_deny() {
	$post_id  = $_POST['post_id'];
	$orderid  = $_POST['orderid'];
	$hub_data = wp_json_encode(
		array(
			'post_id' => $post_id,
			'orderid' => $orderid,
		)
	);

	$estimated_shipping_date    = ! empty( get_post_meta( $post_id, 'estimated_shipping_date', true ) ) ? get_post_meta( $post_id, 'estimated_shipping_date', true ) : '';
	$bill_of_landing_id         = ! empty( get_post_meta( $post_id, 'bill_of_landing_id', true ) ) ? get_post_meta( $post_id, 'bill_of_landing_id', true ) : '';
	$customer_note              = ! empty( get_post_meta( $post_id, 'customer_note', true ) ) ? get_post_meta( $post_id, 'customer_note', true ) : '';
	$product_names              = ! empty( get_post_meta( $post_id, 'product_name', true ) ) ? get_post_meta( $post_id, 'product_name', true ) : '';
	$origin                     = ! empty( get_post_meta( $post_id, 'origin', true ) ) ? get_post_meta( $post_id, 'origin', true ) : '';
	$order_date                 = ! empty( get_post_meta( $post_id, 'order_date', true ) ) ? get_post_meta( $post_id, 'order_date', true ) : '';
	$meta_data_arr              = ! empty( get_post_meta( $post_id, 'meta_data_arr', true ) ) ? get_post_meta( $post_id, 'meta_data_arr', true ) : '';
	$product_names              = ! empty( get_post_meta( $post_id, 'product_name', true ) ) ? get_post_meta( $post_id, 'product_name', true ) : '';
	$product_cat                = ! empty( get_post_meta( $post_id, 'product_cat', true ) ) ? get_post_meta( $post_id, 'product_cat', true ) : '';
	$product_cat_name           = ! empty( get_post_meta( $post_id, 'product_cat_name', true ) ) ? get_post_meta( $post_id, 'product_cat_name', true ) : '';
	$tradewinds_quickship       = ! empty( get_post_meta( $post_id, 'tradewinds_quickship', true ) ) ? get_post_meta( $post_id, 'tradewinds_quickship', true ) : '';
	$item_sku                   = ! empty( get_post_meta( $post_id, 'item_sku', true ) ) ? get_post_meta( $post_id, 'item_sku', true ) : '';
	$order_status               = ! empty( get_post_meta( $post_id, 'order_status', true ) ) ? get_post_meta( $post_id, 'order_status', true ) : '';
	$custom_color_match         = ! empty( get_post_meta( $post_id, 'custom_color_match', true ) ) ? get_post_meta( $post_id, 'custom_color_match', true ) : '';
	$billing                    = ! empty( get_post_meta( $post_id, 'billing', true ) ) ? get_post_meta( $post_id, 'billing', true ) : '';
	$shipping                   = ! empty( get_post_meta( $post_id, 'shipping', true ) ) ? get_post_meta( $post_id, 'shipping', true ) : '';
	$shipping_lines             = ! empty( get_post_meta( $post_id, 'shipping_lines', true ) ) ? get_post_meta( $post_id, 'shipping_lines', true ) : '';
	$edited_line_items          = ! empty( get_post_meta( $post_id, 'edited_line_items', true ) ) ? get_post_meta( $post_id, 'edited_line_items', true ) : '';
	$bol_pdf                    = ! empty( get_post_meta( $post_id, 'bol_pdf', true ) ) ? get_post_meta( $post_id, 'bol_pdf', true ) : '';
	$shipping_label             = ! empty( get_post_meta( $post_id, 'shipping_label', true ) ) ? get_post_meta( $post_id, 'shipping_label', true ) : '';
	$samples_status             = ! empty( get_post_meta( $post_id, 'samples_status', true ) ) ? get_post_meta( $post_id, 'samples_status', true ) : '';
	$is_tradewinds_selected     = ! empty( get_post_meta( $post_id, 'is_tradewinds_selected', true ) ) ? get_post_meta( $post_id, 'is_tradewinds_selected', true ) : '';
	$completion_date            = ! empty( get_post_meta( $post_id, 'completion_date', true ) ) ? get_post_meta( $post_id, 'completion_date', true ) : '';
	$shipping_state             = ! empty( get_post_meta( $post_id, 'shipping_state', true ) ) ? get_post_meta( $post_id, 'shipping_state', true ) : '';
	$rush_manufacturing         = ! empty( get_post_meta( $post_id, 'rush_manufacturing', true ) ) ? get_post_meta( $post_id, 'rush_manufacturing', true ) : '';
	$completion_date_ymd_format = gmdate( 'Y-m-d', strtotime( get_post_meta( $post_id, 'completion_date', true ) ) );
	/**
	 * Inserting manual order based on rest api request data
	 */
	$wrh_order = array(
		'post_title'  => $orderid . ' - 1',
		'post_status' => 'publish',
		'post_date'   => current_time( 'mysql' ),
		'post_type'   => 'wrh_order',
	);

	$new_post_id = wp_insert_post( $wrh_order );
	/**
	 * Saving order data as meta to all_orders cpt
	 */
	add_post_meta( $new_post_id, 'estimated_shipping_date', $estimated_shipping_date );
	add_post_meta( $new_post_id, 'customer_note', $customer_note );
	add_post_meta( $new_post_id, 'origin', $origin );
	add_post_meta( $new_post_id, 'order_date', $order_date );
	add_post_meta( $new_post_id, 'order_id', intval( $orderid ) . '-1' );
	add_post_meta( $new_post_id, 'meta_data_arr', $meta_data_arr );
	add_post_meta( $new_post_id, 'product_name', $product_names );
	add_post_meta( $new_post_id, 'product_cat', $product_cat );
	add_post_meta( $new_post_id, 'product_cat_name', $product_cat_name );
	add_post_meta( $new_post_id, 'tradewinds_quickship', $tradewinds_quickship );
	add_post_meta( $new_post_id, 'item_sku', $item_sku );
	add_post_meta( $new_post_id, 'order_status', $order_status );
	add_post_meta( $new_post_id, 'custom_color_match', $custom_color_match );
	add_post_meta( $new_post_id, 'billing', $billing );
	add_post_meta( $new_post_id, 'shipping', $shipping );
	add_post_meta( $new_post_id, 'bill_of_landing_id', $bill_of_landing_id );
	add_post_meta( $new_post_id, 'shipping_lines', $shipping_lines );
	add_post_meta( $new_post_id, 'line_items', $edited_line_items );
	add_post_meta( $new_post_id, 'bol_pdf', $bol_pdf );
	add_post_meta( $new_post_id, 'shipping_label', $shipping_label );
	add_post_meta( $new_post_id, 'samples_status', $samples_status );
	add_post_meta( $new_post_id, 'is_tradewinds_selected', $is_tradewinds_selected );
	add_post_meta( $new_post_id, 'completion_date', $completion_date );
	add_post_meta( $new_post_id, 'completion_date_ymd_format', $completion_date_ymd_format );
	add_post_meta( $new_post_id, 'shipping_state', $shipping_state );
	add_post_meta( $new_post_id, 'rush_manufacturing', 'rush_my_order' );
	HoodslyHubHelper::add_order_history( $new_post_id, 'Order Placed on HoodslyHub.com' );
	if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
		$options    = array(
			'ssl' => array(
				'verify_peer'      => false,
				'verify_peer_name' => false,
			),
		);
		$bolpdfData = file_get_contents( $bol_pdf, false, stream_context_create( $options ) );
	} else {
		$bolpdfData = file_get_contents( $bol_pdf );
	}
	$bolpdf_name = basename( $bol_pdf );
	$upload      = wp_upload_bits( $bolpdf_name, null, $bolpdfData );

	$api_endpoint          = get_option( 'hoodslyhub_api_settings' );
	$edited_order_approval = '';
	foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
		if ( 'edited_order_approval' === $value['end_point_type'] ) {
			$edited_order_approval = $value['end_point_url'];
		}
	}
	$hub_data      = wp_json_encode(
		array(
			'order_id'      => $orderid,
			'approval_type' => 'deny',
		)
	);
	$api_secret    = get_option( 'hoodslyhub_api_credentials' );
	$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
	$data          = wp_remote_post(
		$edited_order_approval,
		array(
			'body'    => $hub_data,
			'headers' => array(
				'content-type'  => 'application/json',
				'Api-Signature' => $api_signature,
			),
		)
	);
	wp_send_json(
		array(
			'success' => true,
			'msg'     => 'success',
		)
	);
}
add_action( 'wp_ajax_order_new_changes_approve_deny', 'order_new_changes_approve_deny' );
/**
 * Search function for Resource page
 */
if ( ! function_exists( 'hub_search_handler' ) ) {
	function hub_search_handler() {
		?>
		<div class="dashboard-page__order-body">
			<table class="table table-order">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Order Id', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Customer Info', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Status', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Estimated Shipping Date', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Order Source', 'hoodslyhub' ); ?></th>
				</tr>
				</thead>
				<tbody id="hub_no_data">
				<?php
				$meta_query    = array();
				$args          = array();
				$search_string = $_POST['query'];

				$meta_query[] = array(
					'key'     => 'shipping',
					'value'   => $search_string,
					'compare' => 'LIKE',
				);
				$meta_query[] = array(
					'key'     => 'billing',
					'value'   => $search_string,
					'compare' => 'LIKE',
				);
				$meta_query[] = array(
					'key'     => 'order_id',
					'value'   => $search_string,
					'compare' => 'LIKE',
				);

				//if there is more than one meta query 'or' them
				if ( count( $meta_query ) > 1 ) {
					$meta_query['relation'] = 'OR';
				}

				// The Query
				$args['post_type']      = 'wrh_order';
				$args['_meta_or_title'] = $search_string; //not using 's' anymore
				$args['meta_query']     = $meta_query;

				$all_orders = new WP_Query( $args );
				if ( $all_orders->have_posts() ) {
					while ( $all_orders->have_posts() ) {
						$all_orders->the_post();
						$shipping_add            = get_post_meta( get_the_ID(), 'shipping', true );
						$shipping                = ( isset( $shipping_add ) && is_array( $shipping_add ) ) ? $shipping_add : array();
						$first_name              = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
						$last_name               = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';
						$order_status            = trim( get_post_meta( get_the_ID(), 'order_status', true ) );
						$es_shipping_date        = get_post_meta( get_the_ID(), 'estimated_shipping_date', true );
						$shipping_date           = gmdate( 'F jS Y', strtotime( $es_shipping_date ) );
						$estimated_shipping_date = $shipping_date ?? '';
						$origin                  = get_post_meta( get_the_ID(), 'origin', true );
						$domain_parts            = explode( '.', $origin );
						$order_link              = get_template_link( 't_order-details.php' );
						$order_id                = get_post_meta( get_the_ID(), 'order_id', true );
						$line_items              = get_post_meta( get_the_ID(), 'line_items', true );
						$current_date            = gmdate( 'm/d/Y H:i:s', time() );
						$date1                   = strtotime( $estimated_shipping_date );
						$date2                   = strtotime( $current_date );
						$date_difference         = $date1 - $date2;
						$result                  = round( $date_difference / ( 60 * 60 * 24 ) );
						$bg_color                = ( 'Invoice Paid' === $order_status ) ? 'style=background-color:#44d660' : ( ( 'Invoice Sent' === $order_status ) ? 'style=background-color:#f4d699' : ( ( 'In Production' === $order_status ) ? 'style=background-color:#b7cddc' : ( ( 'Order Hold' === $order_status ) ? 'style=background-color:#DCA8A8' : ( ( 'Delivered' === $order_status ) ? 'style=background-color:#17ff00' : ( ( 'Staged To Ship' === $order_status ) ? 'style=background-color:#afdca8' : ( ( 'Sending' === $order_status ) ? 'style=background-color:#9DEEF0' : '' ) ) ) ) ) );
						?>
						<tr style="background-color: #4747471a;">
							<td data-title="Order Id">
							<input type="checkbox" id="bulk_move_order" data-status="<?php echo esc_html( $order_status ); ?>" data-date="<?php echo esc_html( $estimated_shipping_date ); ?>" class="bulk_move_order" value="test" data-orderid="<?php echo esc_html( $order_id ); ?>" data-postid="<?php echo get_the_ID(); ?>"/>	
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
							<td data-title="Customer Info"><?php echo esc_html( $first_name ) . ' ' . esc_html( $last_name ); ?></td>
							<td data-title="Order Status">
								<button class="btn btn-violet" <?php echo esc_attr( $bg_color ); ?>><?php echo esc_html( $order_status ); ?></button>
							</td>
							<td data-title="Estimated Shipping Date">
								<?php
								echo esc_html( $estimated_shipping_date ) . ' (' . intval( $result ) . ' Days)';
								?>
							</td>
							<td data-title="Order Source">
								<?php
								echo esc_html(
									ucfirst(
										$domain_parts[0]
									)
								)
								?>
							</td>
						</tr>
						<?php
					} // end while
				} // end if
				wp_reset_postdata();
				?>
				</tbody>
			</table>
		</div>
		<?php
		die();
	}

	add_action( 'wp_ajax_hub_search_handler', 'hub_search_handler' );
}

//add_action( 'init', 'test_function' );
function test_function() {

	$api_endpoint          = get_option( 'hoodslyhub_api_settings' );
	$edited_order_approval = '';

	foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
		if ( 'edited_order_approval' === $value['end_point_type'] ) {
			$edited_order_approval = $value['end_point_url'];
		}
	}
}

/**
 * Pending order section Schedule event trigger for On hold order move to incoming order
 * @throws Exception
 */
function wrhhub_on_hold_order_move_to_incoming_order_event_hook() {
	$args           = array(
		'post_type'      => 'wrh_order',
		'posts_per_page' => - 1,
		'meta_query'     => array(
			array(
				'key'     => 'order_status',
				'value'   => 'On Hold',
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'completion_date_ymd_format',
				'value'   => gmdate( 'Y-m-d', time() + 691200 ),
				'type'    => 'DATETIME',
				'compare' => '=',
			),
		),
	);
	$on_hold_orders = new WP_Query( $args );

	if ( $on_hold_orders->have_posts() ) {
		while ( $on_hold_orders->have_posts() ) {
			$on_hold_orders->the_post();
			update_post_meta( get_the_ID(), 'order_status', 'Pending' );
			update_post_meta( get_the_ID(), 'completion_date', 'none' );
			update_post_meta( get_the_ID(), 'order_hold_is_priority', 'yes' );

			/*Data send to HUB*/
			$order_status           = get_post_meta( get_the_ID(), 'order_status', true );
			$order_id               = get_post_meta( get_the_ID(), 'order_id', true );
			$hub_data               = wp_json_encode(
				array(
					'order_status' => $order_status,
					'order_id'     => $order_id,
				)
			);
			$api_endpoint           = get_option( 'hoodslyhub_api_settings' );
			$order_status_end_point = '';
			foreach ( $api_endpoint['hub_order_status_endpoint']['feed'] as $key => $value ) {
				if ( 'order_status_end_point' === $value['end_point_type'] ) {
					$order_status_end_point = $value['end_point_url'];
				}
			}
			$api_secret    = get_option( 'hoodslyhub_api_credentials' );
			$api_signature = base64_encode( hash_hmac( 'sha256', 'NzdhYjZiOWMwMGIxMjI2', $api_secret['hoodslyhub_api_key'] ) );
			$data          = wp_remote_post(
				$order_status_end_point,
				array(
					'body'    => $hub_data,
					'headers' => array(
						'content-type'  => 'application/json',
						'Api-Signature' => $api_signature,
					),
				)
			);
			HoodslyHubHelper::add_order_history( get_the_ID(), 'Order Moved to Incoming Order section' );
		}
	}
	wp_reset_postdata();
}

add_action( 'wrhhub_on_hold_order_move_to_incoming_order_event_hook', 'wrhhub_on_hold_order_move_to_incoming_order_event_hook' );

/**
 * Incoming Order Proof of drop off image upload action
 */
function wrh_packaged_upload_proof_action_process() {
	$permission = check_ajax_referer( 'wrh_packaged_upload_proof_action_process_nonce', 'security', false );
	if ( false === $permission ) {
		wp_send_json(
			array(
				'error' => true,
				'msg'   => 'Delivered Verification Nonce error',
			)
		);
		wp_die();
	} else {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		foreach ( $_FILES as $file ) {
			if ( is_array( $file ) ) {
				$attach_id = HoodslyHubHelper::upload_user_file( $file );  //Call function

				update_post_meta( $post_id, '_thumbnail_id', $attach_id );
				HoodslyHubHelper::add_order_history( $post_id, 'Drop off proof uploaded successfully.' );
				wp_send_json(
					array(
						'success' => true,
						'msg'     => 'Drop off proof uploaded successfully.',
					)
				);

			} else {
				wp_send_json(
					array(
						'error' => true,
						'msg'   => 'Drop off proof upload process failed.',
					)
				);
			}
		}
	}
}

add_action( 'wp_ajax_wrh_packaged_upload_proof_action_process', 'wrh_packaged_upload_proof_action_process' );
