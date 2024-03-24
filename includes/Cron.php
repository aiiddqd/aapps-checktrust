<?php 

namespace U7\CheckTrust\Cron;

add_action('wp', function () {
    if (! wp_next_scheduled('checktrust_cron_event')) {
        wp_schedule_event(time(), 'weekly', 'checktrust_cron_event');
    }
});

add_action('checktrust_cron_event', function(){
    \U7\CheckTrust\update_data_from_api();
});
