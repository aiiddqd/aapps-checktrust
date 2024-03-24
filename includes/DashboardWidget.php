<?php 

namespace U7\CheckTrust\DashboardWidget;

use Aapps\CheckTrust\Reports;

add_action('wp_dashboard_setup', __NAMESPACE__ . '\add_checktrust_dashboard_widget');


function add_checktrust_dashboard_widget() {
	wp_add_dashboard_widget('checktrust_dashboard_widget', 'CheckTrust', __NAMESPACE__ . '\render_checktrust_dashboard_widget');
}

function render_checktrust_dashboard_widget() { 
	$data = get_transient('checktrust_data');
	if(empty($data)){
		echo 'Data is empty! Update page please';
        do_action('checktrust_cron_event');
	}
    echo '<pre>';
	var_dump($data);
    echo '</pre>';

	printf('<a href="%s">Go to Report</a>', Reports::get_url_to_page());
}
