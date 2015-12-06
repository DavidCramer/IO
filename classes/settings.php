<?php
/**
 * IO Setting.
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */
namespace calderawp\cfio;

/**
 * Settings class
 * @package Cf_Io
 * @author  David Cramer
 */
class settings extends core{


	/**
	 * Constructor for class
	 *
	 * @since 0.0.1
	 */
	public function __construct(){

		// add admin page
		add_action( 'admin_menu', array( $this, 'add_settings_pages' ), 25 );

		// save config
		add_action( 'wp_ajax_cfio_save_config', array( $this, 'save_config') );

		// exporter
		add_action( 'init', array( $this, 'check_exporter' ) );

		// create new
		add_action( 'wp_ajax_cfio_create_cf_io', array( $this, 'create_new_cf_io') );

		// delete
		add_action( 'wp_ajax_cfio_delete_cf_io', array( $this, 'delete_cf_io') );

		// update page binds
		add_action( 'wp_ajax_cfio_bind_io', function(){

			$pagebinds_tag = 'io_page_binds';
			$pagebinds = options::get_single( $pagebinds_tag );
			if( empty( $pagebinds ) ){
				$pagebinds = array();
			}

			$ios = options::get_registry();
			if( empty( $ios ) ){
				$ios = array();
			}
			// check for removed ios
			foreach( $pagebinds as $io_id => $binding ){
				if( empty( $ios[ $io_id ] ) ){
					unset( $pagebinds[ $io_id ] );
				}
			}
			if( empty( $ios[ $_POST['id'] ] ) ){
				wp_send_json_error( array( 'message' => 'invalid page id' ) );
			}
			$io_id = $ios[ $_POST['id'] ]['id'];
			$pagebinds[ $io_id ] = (int) $_POST['page'];

			// save it
			$page_bind_object = array(
				'id' => $pagebinds_tag,
				'pages' => $pagebinds
			);
			options::update( $page_bind_object );

			wp_send_json_success( [ 'message' => 'complete' ] );
		});

		

	}

	/**
	 * builds an export
	 *
	 * @uses "wp_ajax_cfio_check_exporter" hook
	 *
	 * @since 0.0.1
	 */
	public function check_exporter(){

		if( current_user_can( 'manage_options' ) ){

			if( !empty( $_REQUEST['download'] ) && !empty( $_REQUEST['cf-io-export'] ) && wp_verify_nonce( $_REQUEST['cf-io-export'], 'cf-io' ) ){

				$data = options::get_single( $_REQUEST['download'] );

				header( 'Content-Type: application/json' );
				header( 'Content-Disposition: attachment; filename="cf-io-export.json"' );
				echo wp_json_encode( $data );
				exit;

			}
			
		}
	}

	/**
	 * Saves a config
	 *
	 * @uses "wp_ajax_cfio_save_config" hook
	 *
	 * @since 0.0.1
	 */
	public function save_config(){

		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'cf-io' ) );
		}

		if( empty( $_POST[ 'cf-io-setup' ] ) || ! wp_verify_nonce( $_POST[ 'cf-io-setup' ], 'cf-io' ) ){
			if( empty( $_POST['config'] ) ){
				return;

			}

		}

		if( ! empty( $_POST[ 'cf-io-setup' ] ) && empty( $_POST[ 'config' ] ) ){
			$config = stripslashes_deep( $_POST['config'] );

			options::update( $config );


			wp_redirect( '?page=cf_io&updated=true' );
			exit;

		}

		if( ! empty( $_POST[ 'config' ] ) ){

			$config = json_decode( stripslashes_deep( $_POST[ 'config' ] ), true );

			if(	wp_verify_nonce( $config['cf-io-setup'], 'cf-io' ) ){
				options::update( $config );
				wp_send_json_success( $config );

			}

		}

		// nope
		wp_send_json_error( $config );

	}

	/**
	 * Array of "internal" fields not to mess with
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function internal_config_fields() {
		return array( '_wp_http_referer', 'id', '_current_tab' );

	}


	/**
	 * Deletes an item
	 *
	 *
	 * @uses 'wp_ajax_cfio_create_cf_io' action
	 *
	 * @since 0.0.1
	 */
	public function delete_cf_io(){
		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'cf-io' ) );
		}

		$deleted = options::delete( strip_tags( $_POST[ 'block' ] ) );

		if ( $deleted ) {
			wp_send_json_success( $_POST );
		}else{
			wp_send_json_error( $_POST );
		}

	}

	/**
	 * Create a new item
	 *
	 * @uses "wp_ajax_cfio_create_cf_io"  action
	 *
	 * @since 0.0.1
	 */
	public function create_new_cf_io(){

		$can = options::can();
		if ( ! $can ) {
			status_header( 500 );
			wp_die( __( 'Access denied', 'cf-io' ) );
		}


		if( !empty( $_POST['import'] ) ){
			$config = json_decode( stripslashes_deep( $_POST[ 'import' ] ), true );

			if( empty( $config['name'] ) || empty( $config['slug'] ) ){
				wp_send_json_error( $_POST );
			}
			$id = null;
			if( !empty( $config['id'] ) ){
				$id = $config['id'];
			}
			options::create( $config[ 'name' ], $config[ 'slug' ], $id );
			options::update( $config );
			wp_send_json_success( $config );
		}

		$new = options::create( $_POST[ 'name' ], $_POST[ 'slug' ] );

		if ( is_array( $new ) ) {
			wp_send_json_success( $new );

		}else {
			wp_send_json_error( $_POST );

		}

	}


	/**
	 * Add options page
	 *
	 * @since 0.0.1
	 *
	 * @uses "admin_menu" hook
	 */
	public function add_settings_pages(){
			// This page will be under "Settings"
			$this->plugin_screen_hook_suffix['cf_io'] =  add_submenu_page(
				'caldera-forms',
				__( 'IO', $this->plugin_slug ),
				__( 'IO', $this->plugin_slug )
				, 'manage_options', 'cf_io',
				array( $this, 'create_admin_page' )
			);


			$cf_ios = \calderawp\cfio\options::get_registry();
			if( empty( $cf_ios ) ){
				$cf_ios = array();
			}
			foreach( $cf_ios as $cf_io_id => $cf_io ){
				
				$cf_io = \calderawp\cfio\options::get_single( $cf_io_id );
				if( empty( $cf_io['location'] ) ){
					$this->plugin_screen_hook_suffix[ 'cf_io-' . $cf_io['id'] ] =  add_submenu_page(
						'cf_io',
						$cf_io['name'],
						$cf_io['name'],
						'manage_options',
						'cf_io-' . $cf_io['id'],
						array( $this, 'create_admin_page' )
					);
				}else{
					switch ($cf_io['location']) {
						case 'primary':
							if( empty( $cf_io['priority'] ) ){
								$cf_io['priority'] = 57.99923;
							}
							if( empty( $cf_io['icon'] ) ){
								$cf_io['icon'] = 'dashicons-admin-generic';
							}
							
							$this->plugin_screen_hook_suffix[ 'cf_io-' . $cf_io['id'] ] = add_menu_page(
								$cf_io['name'],
								$cf_io['name'],
								'manage_options', 'cf_io-' . $cf_io['id'],
								array( $this, 'create_admin_page' ),
								$cf_io['icon'],
								$cf_io['priority']
							);
							break;
						
						case 'child':
							$this->plugin_screen_hook_suffix[ 'cf_io-' . $cf_io['id'] ] =  add_submenu_page(
								$cf_io['parent'],
								$cf_io['name'],
								$cf_io['name'],
								'manage_options',
								'cf_io-' . $cf_io['id'],
								array( $this, 'create_admin_page' )
							);
							break;
					}
				}

				add_action( 'admin_print_styles-' . $this->plugin_screen_hook_suffix['cf_io'], array( $this, 'enqueue_admin_stylescripts' ) );
			}

	}

	/**
	 * Options page callback
	 *
	 * @since 0.0.1
	 */
	public function create_admin_page(){
		// Set class property        
		$screen = get_current_screen();
		$base = array_search($screen->id, $this->plugin_screen_hook_suffix);
			
		// include main template
		if( false !== strpos( $base, 'cf_io-' ) ){
			
			$form_base = substr( $base, strlen( 'cf_io-' ) );
			include CFIO_PATH . 'includes/page.php';

		}else{
			// include main template
			if( empty( $_GET['edit'] ) ){
				include CFIO_PATH . 'includes/admin.php';
			}else{
				include CFIO_PATH . 'includes/edit.php';
			}
		}




		// php based script include
		if( file_exists( CFIO_PATH .'assets/js/inline-scripts.php' ) ){
			echo "<script type=\"text/javascript\">\r\n";
			include CFIO_PATH .'assets/js/inline-scripts.php';
			echo "</script>\r\n";
		}

	}



	
}

