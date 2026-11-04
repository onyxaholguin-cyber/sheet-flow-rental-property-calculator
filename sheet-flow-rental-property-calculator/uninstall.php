<?php
/**
 * Remove the plugin's only option on uninstall.
 *
 * @package sheet-flow-rental-property-calculator
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option( 'sfrc_show_credit' );
