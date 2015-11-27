<?php
/**
 * Loads the plugin if dependencies are met.
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer & CalderaWP LLC
 */


if ( file_exists( CFIO_PATH . 'vendor/autoload.php' ) ){
	//autoload dependencies
	require_once( CFIO_PATH . 'vendor/autoload.php' );

	// initialize plugin
	\calderawp\cfio\core::get_instance();

}else{
	return new WP_Error( 'cf-io--no-dependencies', __( '{{Dependencies for IO could not be found.', 'cf-io' ) );
}


