<?php

namespace Aapps\CheckTrust;

use Aapps\CheckTrust\Reports;

class Settings {
	public static $menu_slug = 'aapps-checktrust-settings';
	public static $options_key = 'aapps_checktrust_options';

	public static function init() {
		add_action( 'admin_menu', [ static::class, 'add_menu' ] );
		add_action( 'admin_init', [ static::class, 'add_settings' ] );
	}


    public static function get_url_to_page(){
		return admin_url( 'options-general.php?page=' . static::$menu_slug );
	}

    public static function get_urls(){
		$urls_list = get_option( static::$options_key )['urls_list'] ?? null;


		if(empty($urls_list)){
			return [];
		}
		$urls_list = trim($urls_list);
		return explode("\n", $urls_list);

		
	}
    public static function get_websites(){
        $website_list = get_option( static::$options_key )['website_list'] ?? null;


		if(empty($website_list)){
			return [];
		}
		$website_list = explode("\n", $website_list);

		$list = [];
		foreach($website_list as $key => $value){
			$parsed_url = parse_url($value);
			if (isset($parsed_url['host'])) {
				$list[] = $parsed_url['host'];
			} else {
				$list[] = $value;
			}
		}
		return $list;

	}

	
    public static function get_app_key(){
        return get_option( static::$options_key )['api_key'] ?? null;
    }

    


	public static function add_menu() {
		add_options_page(
			'CheckTrust Settings',
			'CheckTrust',
			'manage_options',
			static::$menu_slug,
			function () {
				?>
			<div class="wrap">
				<h1>CheckTrust Settings</h1>
				<div>
					<?= sprintf( '<a href="%s">Go to Report</a>', Reports::get_url_to_page() ); ?>
				</div>
				<form action='options.php' method='post'>
					<?php
						settings_fields( static::$menu_slug );
						do_settings_sections( static::$menu_slug );
						submit_button();
						?>
				</form>
			</div>
			<?php
			}
		);

		add_settings_section( 'default', '', '', static::$menu_slug );


	}

	public static function add_settings() {
		register_setting( static::$menu_slug, static::$options_key );

		self::add_api_key_field();
		self::add_website_list();
		self::add_urls_list();

	}

	static function add_urls_list() {
        add_settings_field(
			'urls_list',
			'URLs List',
			function () {
				$options = get_option( static::$options_key );
				$value = ! empty ( $options['urls_list'] ) ? $options['urls_list'] : '';
				?>
				<textarea name="<?php echo esc_attr( static::$options_key ); ?>[urls_list]" rows="10" cols="50"><?php echo esc_textarea( $value ); ?></textarea>
				<?php
			},
			static::$menu_slug,
			'default'
		);
    }
	static function add_website_list() {
		add_settings_field(
			'website_list',
			'Website List',
			function () {
				$options = get_option( static::$options_key );
				$value = ! empty ( $options['website_list'] ) ? $options['website_list'] : '';
				?>
				<textarea name="<?php echo esc_attr( static::$options_key ); ?>[website_list]" rows="10" cols="50"><?php echo esc_textarea( $value ); ?></textarea>
				<?php
			},
			static::$menu_slug,
			'default'
		);
	}

	static function add_api_key_field() {
		add_settings_field(
			'api_key',
			'API Key',
			function () {
				$options = get_option( static::$options_key );
				$value = ! empty ( $options['api_key'] ) ? $options['api_key'] : '';
				?>
			<input type="text" name="<?php echo esc_attr( static::$options_key ); ?>[api_key]"
				value="<?php echo esc_attr( $value ); ?>">
			<?php
			},
			static::$menu_slug
		);


	}

}

Settings::init();
