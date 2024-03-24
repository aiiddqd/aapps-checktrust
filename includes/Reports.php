<?php

namespace Aapps\CheckTrust;

use Aapps\CheckTrust\Settings;

final class Reports {

	public static $slug = 'checktrust';
	public static $key_titles = [ 
		'trust' => 'Доверие',
		'spam' => 'Спамность',
		'hostQuality' => 'Качество хоста',
		'loadingTime' => 'Скорость загрузки',
		'keysSoTrafYaMSK' => 'keysSoTrafYaMSK',
		'keysSoTrafGoogleMSK' => 'keysSoTrafGoogleMSK',
		'quality' => 'Качество',
	];

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
					<span> | </span>
					<span>
						<?= sprintf( '<a href="%s">Settings</a>', Settings::get_url_to_page() ); ?>
					</span>

				</div>
				<?php
				self::render();
			} );
		} );
	}


	public static function render_websites( $data ) {
		echo '<hr/>';
		echo '<h2>Websites</h2>';

		foreach ( $data as $key => $value ) {
			printf( '<h3>%s</h3>', $key );
			$metrics = $value['summary'];
			// echo '<pre>';
			// var_dump( $metrics );
			// echo '</pre>';
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<?php foreach ( $metrics as $key_metric => $value_metric ) : ?>
						<th>
							<?= self::get_title_by_key( $key_metric ) ?>
						</th>
					<?php endforeach; ?>
				</thead>

				<tbody>
					<tr>
					<?php foreach ( $metrics as $key_metric => $value_metric ) : ?>
						<td><?= $value_metric ?></td>	
					<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
			<?php
		}

	}

	public static function render_urls( $data ) {
		echo '<hr/>';

		echo '<h2>URLs</h2>';
		foreach ( $data as $key => $value ) {
			printf( '<h3>%s</h3>', $key );
			$metrics = $value['summary'];
			// echo '<pre>';
			// var_dump( $metrics );
			// echo '</pre>';
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<?php foreach ( $metrics as $key_metric => $value_metric ) : ?>
						<th>
							<?= self::get_title_by_key( $key_metric ) ?>
						</th>
					<?php endforeach; ?>
				</thead>

				<tbody>
					<tr>
					<?php foreach ( $metrics as $key_metric => $value_metric ) : ?>
						<td><?= $value_metric ?></td>	
					<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
			<?php
		}



	}

	public static function get_title_by_key( $key ) {

		if ( isset ( static::$key_titles[ $key ] ) ) {
			return static::$key_titles[ $key ];
		}

		return null;

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

	public static function get_url_to_page() {
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

}

Reports::init();