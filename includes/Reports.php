<?php 

namespace AappsCheckTrust;

final class Reports {

    public static $slug = 'checktrust';

    public static function init() {

        add_action( 'admin_menu', function() {
            add_management_page( 'CheckTrust', 'CheckTrust', 'manage_options', static::$slug, function() {
                ?>
                <h1>CheckTrust Reports</h1>
                <?php 
                self::render();
            });
        });
    }

    public static function render(){

        $data = get_transient('checktrust_data');

        if(empty($data)){
            echo 'Data is empty! Update page please';
            do_action('checktrust_cron_event');
        }
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    
        printf('<a href="https://checktrust.ru/cabinet">Go to CheckTrust</a>');
    }
}

Reports::init();