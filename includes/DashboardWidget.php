<?php 

namespace U7\CheckTrust\DashboardWidget;

use Aapps\CheckTrust\Reports;

add_action('wp_dashboard_setup', __NAMESPACE__ . '\add_checktrust_dashboard_widget');


function add_checktrust_dashboard_widget() {
	wp_add_dashboard_widget('checktrust_dashboard_widget', 'CheckTrust', __NAMESPACE__ . '\render_checktrust_dashboard_widget');
}

function render_checktrust_dashboard_widget() { 
	$data = get_transient('checktrust_data');
	// echo '<pre>';
	// var_dump($data['websites'][0]);
    // echo '</pre>';
	if(empty($data['websites'])){
		echo 'Data is empty! Update page please';
        
	}

	$key = array_key_first($data['websites']);
	$data = $data['websites'][$key]['summary'];
	printf('<p>Доверие: %s</p>', $data['trust']);
	printf('<p>Спамность: %s</p>', $data['spam']);
	printf('<p>Качество хоста: %s</p>', $data['hostQuality']);
	printf('<p>Время загрузки в мс: %s</p>', $data['loadingTime']);
	printf('<p>Качество сайта: %s</p>', $data['quality']);
	printf('<p>Трафик из Яндекса по КейСо: %s</p>', $data['keysSoTrafYaMSK']);
	printf('<p>Трафик из Гугла по КейСо: %s</p>', $data['keysSoTrafGoogleMSK']);
    // echo '<pre>';
	// var_dump($data);
    // echo '</pre>';

	printf('<a href="%s">Go to Report</a>', Reports::get_url_to_page());
}
