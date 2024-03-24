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
                self::update_button();
                self::render();
            });
        });
    }

    public static function update_button(){
        $url = admin_url('tools.php?page=' . static::$slug);
        $url = add_query_arg('action', 'update', $url);

        if(isset($_GET['action']) && 'update' == $_GET['action']){
            do_action('checktrust_cron_event');
            printf('<a href="%s">Updated</a>', $url);

        } else {
            printf('<a href="%s">Update</a>', $url);

        }
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