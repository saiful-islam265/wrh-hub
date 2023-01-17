<?php
add_action( 'rest_api_init', function () {
	register_rest_route( 'damage-claim/v1', '/damage-claim-order', array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'get_damage_claim_order',
		'permission_callback' => '__return_true',
	) );
} );
/**
 * Rest api callback method
 *
 * @param $response
 */
function get_damage_claim_order( $response ) {
	global $wpdb;

	$body_data    = $response->get_body();
	$arr          = json_decode( $body_data, true );
	$damage_claim = array(
		'post_title'   => $arr['title'],
		'post_content' => $arr['order_id'],
		'post_status'  => 'publish',
		'post_date'    => date( 'Y-m-d H:i:s' ),
		'post_type'    => 'damage_claims',
	);
	$post_id      = wp_insert_post( $damage_claim );
	add_post_meta( $post_id, 'damage_type', $arr['data']['damage_type'] );
	add_post_meta( $post_id, 'order_id', $arr['data']['order_id'] );
	$order_id = get_post_meta( $post_id, 'order_id', true );
	$order_id = ( isset( $order_id ) && ! empty( $order_id ) ) ? $order_id : '';
	$post_id  = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %d AND NOT post_type", $order_id, 'damage_claims' ) );

	$shipping   = get_post_meta( $post_id, 'shipping', true );
	$shipping   = ( isset( $shipping ) && is_array( $shipping ) ) ? $shipping : array();
	$first_name = ( isset( $shipping['first_name'] ) && ! empty( $shipping['first_name'] ) ) ? $shipping['first_name'] : '';
	$last_name  = ( isset( $shipping['last_name'] ) && ! empty( $shipping['last_name'] ) ) ? $shipping['last_name'] : '';

	$damage_claim_date    = current_time( 'mysql' );
	$order_summery        = get_post_meta( $post_id, 'order_summery', true );
	$order_summery        = ( isset( $order_summery ) && is_array( $order_summery ) ) ? $order_summery : array();
	$damage_claim_summery = [ [ 'summery' => $first_name . ' ' . $last_name . ' submitted damage claim form from Hoosdly', 'date' =>
		$damage_claim_date
	] ];
	$order_summery_array  = array_merge( $order_summery, $damage_claim_summery );
	update_post_meta( $post_id, 'order_summery', $order_summery_array );
}

