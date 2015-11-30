<?php
/**
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 David Cramer & CalderaWP LLC
 *
 * @wordpress-plugin
 * Plugin Name: IO
 * Plugin URI:  http://CalderaWP.com
 * Description: Create Data Management Structures
 * Version:     1.0.0-b1
 * Author:      David Cramer
 * Author URI:  https://CalderaWP.com
 * Text Domain: cf-io
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CFIO_PATH',  plugin_dir_path( __FILE__ ) );
define('CFIO_CORE',  __FILE__ );
define('CFIO_URL',  plugin_dir_url( __FILE__ ) );
define('CFIO_VER',  '1.0.0-b1' );



// Load instance
add_action( 'plugins_loaded', 'cfio_bootstrap' );
function cfio_bootstrap(){

	if ( is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		include_once CFIO_PATH . 'vendor/calderawp/dismissible-notice/src/functions.php';
	}


	if ( ! version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
		if ( is_admin() ) {
			
			//BIG nope nope nope!
			$message = __( sprintf( 'IO requires PHP version %1s or later. We strongly recommend PHP 5.5 or later for security and performance reasons. Current version is %2s.', '5.3.0', PHP_VERSION ), 'cf-io' );
			echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins' );
		}

	}else{
		//bootstrap plugin
		require_once( CFIO_PATH . 'bootstrap.php' );

	}

}
