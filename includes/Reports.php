<?php

namespace AappsCheckTrust;

final class Reports {

	public static $slug = 'checktrust';

	public static function init() {

		add_action( 'admin_menu', function () {
			add_management_page( 'CheckTrust', 'CheckTrust', 'manage_options', static::$slug, function () {
				?>
				<h1>CheckTrust Reports</h1>
				<div class="actions">
					<span>
						<?php self::update_button(); ?>
					</span>
					<span> | </span>
					<span>
						<?= sprintf( '<a href="https://checktrust.ru/cabinet">Go to CheckTrust</a>' ); ?>
					</span>

				</div>
				<?php
				self::render();
			} );
		} );
	}

	public static function update_button() {
		$url = self::get_url_to_page();
		$url = add_query_arg( 'action', 'update', $url );

		if ( isset ( $_GET['action'] ) && 'update' == $_GET['action'] ) {
			do_action( 'checktrust_cron_event' );
			printf( '<a href="%s">Updated</a>', $url );

		} else {
			printf( '<a href="%s">Update</a>', $url );

		}
	}

    public static function get_url_to_page(){
        return admin_url( 'tools.php?page=' . static::$slug );
    }

	public static function render() {

		$data = get_transient( 'checktrust_data' );

		if ( empty ( $data ) ) {
			echo 'Data is empty! Update page please';
			do_action( 'checktrust_cron_event' );
		}
		if ( isset ( $data['hostLimitsBalance'] ) ) {
			printf( '<p>hostLimitsBalance: %s</p>', $data['hostLimitsBalance'] );
		}

		if ( ! empty ( $data['websites'] ) ) {
			self::render_websites( $data['websites'] );
		}

		if ( ! empty ( $data['urls'] ) ) {
			self::render_urls( $data['urls'] );
		}


	}

	public static function render_urls( $data ) {
		echo '<h2>urls</h2>';
		foreach ( $data as $key => $value ) {
			printf( '<h3>%s</h3>', $key );
			echo '<pre>';
			var_dump( $value );
			echo '</pre>';
		}

	}

	public static function render_websites( $data ) {
		echo '<h2>websites</h2>';

		foreach ( $data as $key => $value ) {
			printf( '<h3>%s</h3>', $key );
			echo '<pre>';
			var_dump( $value );
			echo '</pre>';
		}

	}
}

Reports::init();