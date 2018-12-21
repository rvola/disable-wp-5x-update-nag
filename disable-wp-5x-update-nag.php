<?php
/*
Plugin Name:            Disable WordPress "Gutenberg" Update
Plugin URI:             https://github.com/rvola/disable-wp-gutenberg-update

Description:            Disable the update to WordPress 5.X (Gutenberg), while keeping the possibility of automatic updates and lower than 5.X.

Version:                1.0.1
Revision:               2018-12-21
Creation:               2018-12-19

Author:                 studio RVOLA
Author URI:             https://www.rvola.com

Requires at least:      3.0
Tested up to:           5.0

License:		        GPLv3
License URI:	        https://www.gnu.org/licenses/gpl-3.0
*/

/**
 * Returns an update table without the version greater than 4.9
 *
 * @param $transient
 *
 * @return mixed
 */
add_filter( 'site_transient_update_core', 'dwgu_disable_update', 1, 1 );
function dwgu_disable_update( $transient ) {

	if ( isset( $transient->updates ) ) {

		// Delete Update for WP 5.X
		foreach ( $transient->updates as $key => $update ) {
			$version_cleaned = preg_replace( '/(\d{1})\.(\d{1})\..*/', '$1.$2', $update->current );
			if ( version_compare( $version_cleaned, '4.9', '>' ) ) {
				unset( $transient->updates[ $key ] );
			}
		}

		$transient->updates = array_values( $transient->updates );

		// Add last version update for upgrade button
		foreach ( $transient->updates as $key => $update ) {
			if ( 'autoupdate' == $update->response ) {
				$update_force           = $transient->updates[ $key ];
				$update_force->response = 'upgrade';
				$transient->updates[]   = $update_force;
				break;
			}
		}
	}

	return $transient;
}

/**
 * Hide the widget for testing Gutenberg
 *
 */
add_action( 'admin_init', 'dwgu_hide_dashboard_widget', 10 );
function dwgu_hide_dashboard_widget() {
	remove_action( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );
}
