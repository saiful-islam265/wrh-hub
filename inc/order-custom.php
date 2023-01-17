<?php
function hoodslyhub_add_custom_order() {
	$order_product           = $_POST['order_product'];
	$order_hood_style        = $_POST['order_hood_style'];
	$order_hood_color        = $_POST['order_hood_color'];
	$order_moding_type       = $_POST['order_moding_type'];
	$order_molding_install   = $_POST['order_molding_install'];
	$order_crown_molding     = $_POST['order_crown_molding'];
	$order_depth             = $_POST['order_product'];
	$order_height_reduction  = $_POST['order_height_reduction'];
	$order_chimney_extension = $_POST['order_chimney_extension'];
	$order_solid_button      = $_POST['order_solid_button'];
	$order_description       = $_POST['order_description'];
	$shipping_customer_name  = $_POST['shipping_customer_name'];
	$shipping_full_address   = $_POST['shipping_full_address'];
	$shipping_customer_email = $_POST['shipping_customer_email'];
	$shipping_customer_phone = $_POST['shipping_customer_phone'];
	$shipping_customer_note  = $_POST['shipping_customer_note'];
	//Update the metaq value
	//update_post_meta($post_id, 'send_to_picked', 'list_as_picked');
	$post_id = wp_insert_post( array(
		'post_status' => 'publish',
		'post_author' => '1',
		'post_type'   => 'post',
		'post_title'  => '#'
	) );
	add_post_meta( $post_id, 'customer_note', $shipping_customer_note );
	add_post_meta( $post_id, 'first_name', $shipping_customer_name );
	// add_post_meta($post_id, 'last_name', $arr['data']['shipping']['last_name']);
	add_post_meta( $post_id, 'address_1', $shipping_full_address );
	add_post_meta( $post_id, 'address_2', $arr['data']['shipping']['address_2'] );
	// add_post_meta($post_id, 'city', $arr['data']['shipping']['city']);
	// add_post_meta($post_id, 'state', $arr['data']['shipping']['state']);
	// add_post_meta($post_id, 'country', $arr['data']['shipping']['country']);
	// add_post_meta($post_id, 'postcode', $arr['data']['shipping']['postcode']);
	add_post_meta( $post_id, 'email', $shipping_customer_email );
	add_post_meta( $post_id, 'phone', $shipping_customer_phone );
	add_post_meta( $post_id, 'origin', 'CUSTOM' );
	add_post_meta( $post_id, 'order_date', date( 'Y-m-d H:i:s' ) );
	add_post_meta( $post_id, 'order_id', $post_id );
	// add_post_meta($post_id, 'meta_data_arr', $arr['meta_data']);
	add_post_meta( $post_id, 'product_name', $order_product );
	// add_post_meta($post_id, 'product_cat', $arr['product_cat']);
	// add_post_meta($post_id, 'item_sku', $arr['item_sku']);

	$data         = array(
		'ID'           => $post_id,
		'post_content' => $post_id . ' ' . $shipping_customer_name . ' ' . $shipping_customer_email . ' ' . $shipping_customer_phone . ' ' . $shipping_full_address,
		'post_title'   => '#' . $post_id
	);
	$responseData = wp_update_post( $data );
	$response     = array(
		'status'      => '200',
		'message'     => 'OK',
		'new_post_ID' => $post_id
	);
	if ( $responseData ) {
		echo json_encode( $response );
	}
	exit; // important
}

add_action( 'wp_ajax_add_custom_order', 'hoodslyhub_add_custom_order' ); // wp_ajax_{ACTION HERE}