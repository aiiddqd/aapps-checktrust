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
use Aapps\CheckTrust\Settings;

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
		update_data_from_api();
	}

} );

$files = glob( __DIR__ . '/includes/*.php' );
foreach ( $files as $file ) {
	require_once $file;
}

function update_data_from_api($print = false) {

	$data1 = update_data_for_sites();
	$data2 = update_data_for_urls();
	
	if ( is_wp_error( $data1 ) ) {
		if($print){
			wp_send_json_error( [ 'get_error_message' => $data1->get_error_message() ] );
		}
	}

	if($print){
		wp_send_json_success( $data1 );
	}


}

function update_data_for_urls(){

	$urls = Settings::get_urls();
	$parameterList = 'trust,spam,loadingTime,keysSoTrafYaMSK,keysSoTrafGoogleMSK';
	$data_new = [];
	foreach($urls as $url){
		$data_new[$url] = [];
		$data = request( [ 
			'host' => $url,
			'parameterList' => $parameterList,
		] );

		if ( isset ( $data['summary'] ) ) {
			$data_new[$url]['summary'] = $data['summary'];
		}
	
	}

	if($data_new){
		update_data( 'urls', $data_new );
	}

	if ( isset ( $data['hostLimitsBalance'] ) ) {
		update_data( 'hostLimitsBalance', $data['hostLimitsBalance'] );
	}

	return $data_new;
}

function update_data_for_sites(){
	
	$websites = Settings::get_websites();
	$parameterList = 'trust,spam,hostQuality,loadingTime,keysSoTrafYaMSK,keysSoTrafGoogleMSK';
	$websites_data = [];
	foreach($websites as $website){
		$websites_data[$website] = [];
		$data = request( [ 
			'host' => $website,
			'parameterList' => $parameterList,
		] );

		if ( isset ( $data['summary'] ) ) {
			$websites_data[$website]['summary'] = $data['summary'];
		}
	
	}

	if($websites_data){
		update_data( 'websites', $websites_data );
	}

	if ( isset ( $data['hostLimitsBalance'] ) ) {
		update_data( 'hostLimitsBalance', $data['hostLimitsBalance'] );
	}

	return $websites_data;
}


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

	return json_decode( $response_body, true );

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