<?php
/**
 * Plugin Name: CheckTrust by AApps
 * Plugin URI: https://github.com/uptimizt/aapps-checktrust
 * Description: Управление информацией из CheckTrust для сайта
 * Author: U7
 * Author URI: https://github.com/uptimizt
 * License: MIT License
 * License URI: http://www.opensource.org/licenses/mit-license.php
 * Version: 0.0.1
 */

namespace U7\CheckTrust;

use WP_Error;

/**
 * tests
 */
add_action( 'admin_init', function () {
	if ( ! current_user_can( 'administrators' ) ) {
		return;
	}

	if ( isset ( $_GET['testCheckTrust'] ) ) {
		wp_send_json_success( get_data() );
	}

	if ( isset ( $_GET['testCheckTrustUpdate'] ) ) {
		update_data_for_site();
	}

} );

$files = glob( __DIR__ . '/includes/*.php' );
foreach ( $files as $file ) {
	require_once $file;
}

function update_data_for_site() {
	$data = request( [ 
		'host' => 'wpcraft.ru',
		'parameterList' => 'trust,spam,hostQuality,loadingTime,keysSoTrafYaMSK,keysSoTrafGoogleMSK',
	] );

	if ( is_wp_error( $data ) ) {
		wp_send_json_error( [ 'get_error_message' => $data->get_error_message() ] );
	}

	if ( isset ( $data->summary ) ) {
		update_data( 'summary', $data->summary );
	}

	if ( isset ( $data->hostLimitsBalance ) ) {
		update_data( 'hostLimitsBalance', $data->hostLimitsBalance );
	}

	wp_send_json_success( $data );
}

/**
 * @return array|WP_Error The response or WP_Error on failure.
 */
function request( $context = [] ) {
	$url = 'https://checktrust.ru/app.php?r=host/app/summary/basic';

	$url = add_query_arg( 'applicationKey', get_app_key(), $url );

	$parameterList = $context['parameterList'] ?? null;
	if ( $parameterList ) {
		$url = add_query_arg( 'parameterList', $parameterList, $url );
	}

	$host = $context['host'] ?? null;
	if ( $host ) {
		$url = add_query_arg( 'host', $host, $url );
	}


	$response = wp_remote_get( $url );

	if ( is_wp_error( $response ) ) {
		return $response;
	}
	// Handle the response
	// $response_code = wp_remote_retrieve_response_code( $response );

	// echo "Response Code: " . $response_code;
	// echo "Response Body: " . $response_body;
	$response_body = wp_remote_retrieve_body( $response );

	return json_decode( $response_body );

	// return $response_body;
}

function get_data( $key = '' ) {
	$data = get_transient( 'checktrust_data' );
	if ( empty ( $key ) ) {
		return $data;
	} else {
		return $data[ $key ] ?? null;
	}
}

function update_data( $key, $value ) {

	$data = get_transient( 'checktrust_data' );
	if ( empty ( $data ) ) {
		$data = [];
	}

	$data[ $key ] = $value;
	set_transient( 'checktrust_data', $data, WEEK_IN_SECONDS );
	return true;
}


/**
 * https://checktrust.ru/cabinet/api.html
 */
function get_app_key() {
	if ( defined( 'CHECKTRUST_APP_KEY' ) ) {
		return CHECKTRUST_APP_KEY;
	}

	return \Aapps\CheckTrust\Settings::get_app_key();
}