<?php 

oxygen_vsb_ajax_request_header_check();

global $oxy_api_element, $item_data;

$component_json = file_get_contents('php://input');
$component 		= json_decode( $component_json, true );
$options 		= $component['options']['original'];

$order_id = 'latest';

if( isset($options['order_preview']) && $options['order_preview'] != 'latest' ) {
	$order_id = isset($options['order_id']) ? $options['order_id'] : $order_id;
}

$order = UltimateWooEl::ouwoo_get_builder_preview_order( $order_id );

if ( $order ) {
	$item_data = UltimateWooEl::ouwoo_get_order_item_data( $order );
}

// Actuall thing
$oxy_api_element->ajax_render_callback();