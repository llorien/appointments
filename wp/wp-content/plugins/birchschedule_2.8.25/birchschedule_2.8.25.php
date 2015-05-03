<?php

/*
  Plugin Name: BirchPress Scheduler
  Plugin URI: http://www.birchpress.com
  Description: An appointment booking and online scheduling plugin that allows service businesses to take online bookings.
  Version: 2.8.25.MS
  Author: BirchPress
  Author URI: http://www.birchpress.com
  License: GPLv2
 */

if ( defined( 'ABSPATH' ) && !defined( 'BIRCHSCHEDULE' ) ) {

	define( 'BIRCHSCHEDULE', true );

	require_once 'lib/vendor/autoload.php';

	require_once 'framework/includes/birchpress.inc.php';

	require_once 'includes/legacy_hooks.php';

	require_once 'includes/package.php';

	global $birchschedule, $birchpress;

	$birchschedule->set_plugin_file_path( __FILE__ );
	$birchpress->set_plugin_url( $birchschedule->plugin_url() );

	$birchschedule->set_product_version( '2.8.25.MS' );
	$birchschedule->set_product_name( 'BirchPress Scheduler' );
	$birchschedule->set_product_code( 'birchschedule_2.8.25' );

	$birchschedule->run();

}
