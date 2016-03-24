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

		// add filter for CF Auto Populating tab
		add_filter( 'caldera_forms_get_panel_extensions', array( $this, 'add_autopopulate_panel' ) );
		
		// add autopopulate from form entries
		add_action( 'caldera_forms_autopopulate_types', array( $this, 'autopopulate_option' ), 25 );

		// add auto populate template
		add_action ("caldera_forms_autopopulate_type_config", array( $this, "autopopulate_config" ) );

		

	}


	
	/**
	 * Add advanced auto populate option to autopopulate source
	 *
	 * @uses "caldera_forms_autopopulate_types" action
	 *
	 * @since 0.0.1
	 */
	public function autopopulate_option(){
		echo "<option value=\"form_entries\"{{#is auto_type value=\"form_entries\"}} selected=\"selected\"{{/is}}>" . __('Form Entries', 'caldera-forms') . "</option>";
	}

	
	/**
	 * Add advanced auto populate template instruction
	 *
	 * @uses "caldera_forms_autopopulate_type_config" action
	 *
	 * @since 0.0.1
	 */
	public function autopopulate_config(){
		?>

		<div class="caldera-config-group caldera-config-group-auto-form_entries auto-populate-type-panel" style="display:none;">
			<div class="caldera-config-field">
				See the IO Auto populate tab
			</div>
		</div>

		<?php
	}

	
	/**
	 * Add advanced auto populate tab to CF Editor
	 *
	 * @uses "caldera_forms_get_panel_extensions" filter
	 *
	 * @since 0.0.1
	 */
	public function add_autopopulate_panel( $panels ){

		$tabs = $panels['form_layout']['tabs'];
		$panels['form_layout']['tabs'] = array();
		$panels['form_layout']['setup']['styles'][] = CFIO_URL . 'assets/css/autopopulate-panel.css';
		$panels['form_layout']['setup']['scripts'][] = CFIO_URL . 'assets/js/autopopulate-panel.js';
		foreach( $tabs as $tab_id=>$tab ){
			if( $tab_id == 'processors' ){
				$panels['form_layout']['tabs']['auto_populate'] = array(
					'name' => 'Auto-Populate',
					'location' => 'lower',
					'label' => 'Advanced Auto-Populate',
					'canvas' => CFIO_PATH . 'includes/templates/autopopulate-template.php',

				);
			}
			$panels['form_layout']['tabs'][ $tab_id ] = $tab;
		}		

		return $panels;
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
			options::create( $config[ 'name' ], $config[ 'slug' ] );
			options::update( $config );
			wp_send_json_success( $config );
		}

		$new = options::create( $_POST[ 'name' ], $_POST[ 'slug' ], $_POST[ 'formid' ] );

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
				if( empty( $cf_io['access_roles'] ) ){
					continue;
				}
				$has_access = false;
				foreach( $cf_io['access_roles'] as $checkrole => $checken ){
					if( current_user_can( $checkrole ) ){
						$has_access = $checkrole;
						break;
					}
				}
				if( empty( $has_access ) ){
					continue;
				}

				if( empty( $cf_io['location'] ) ){
					$this->plugin_screen_hook_suffix[ 'cf_io-' . $cf_io['id'] ] =  add_submenu_page(
						'cf_io',
						$cf_io['name'],
						$cf_io['name'],
						$has_access,
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
								$has_access, 'cf_io-' . $cf_io['id'],
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
								$has_access,
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

