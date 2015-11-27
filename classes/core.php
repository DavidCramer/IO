<?php
/**
 * IO.
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 David Cramer
 */
namespace calderawp\cfio;

/**
 * Main plugin class.
 *
 * @package Cf_Io
 * @author  David Cramer
 */
class core {

	/**
	 * The slug for this plugin
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'cf-io';

	/**
	 * Holds class instance
	 *
	 * @since 0.0.1
	 *
	 * @var      object|\calderawp\cfio\core
	 */
	protected static $instance = null;

	/**
	 * Holds the option screen prefix
	 *
	 * @since 0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 0.0.1
	 *
	 * @access private
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );

		// Load front style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_stylescripts' ) );
				
		//load settings class in admin
		if ( is_admin() ) {
			new settings();
		}

		// browser
		add_action( 'wp_ajax_io_browse_entries', array( $this, 'browse_entries' ) );
		//add_action( 'wp_ajax_nopriv_io_browse_entries', array( $this, 'browse_entries' ) );

		add_action( 'caldera_forms_submit_complete', array( $this, 'connect_entry_reference' ), 10, 1);

		add_filter( 'caldera_forms_get_forms', function( $forms ){

			if( empty( $_REQUEST['page'] ) || $_REQUEST['page'] !== 'caldera-forms' ){
				return $forms;
			}

			$cf_ios = options::get_registry();
			foreach ($cf_ios as $cf_io_id => $config ) {
				if( !empty( $config['lock_form'] ) && !empty( $config['form'] ) ){
					if( !empty( $forms[ $config['form'] ] ) ){
						unset( $forms[ $config['form'] ] );
					}
				}
			}
			return $forms;
		 } );
		add_filter( 'caldera_forms_get_form', function( $form ){
			
			if( empty( $_GET['io_modal'] ) ){
				return $form;
			}

			$cf_ios = options::get_registry();
			foreach ($cf_ios as $io_id => $io_config) {
				if( !empty( $io_config['form'] ) && $_GET['io_modal'] == $io_id &&  $form['ID'] == $io_config['form'] ){
					// remove submit buttons
					$io_config = options::get_single( $io_id );
					foreach( $form['fields'] as $field_id=>$field ){
						if( $field['type'] == 'button' && $field['config']['type'] == 'submit' ){
							unset( $form['fields'][ $field_id ] );
						}
					}
					if( !empty( $io_config['relation_field'] ) ){
						unset( $form['fields'][ $io_config['relation_field'] ] );
					}
				}
			}
			return $form;
		});

		add_filter( 'caldera_forms_get_entry', array( $this, 'filter_get_entry' ), 20, 3 );

		//add_action( 'caldera_forms_autopopulate_types', array( $this, 'add_autopopulate_type' ) );

	}

	/**
	 * addes connected interfaces to entry
	 *
	 * @uses "caldera_forms_get_entry" filter
	 *
	 * @param $entry
	 * @param $entry_is
	 * @param $form
	 *
	 * @return mixed
	 */
	public function filter_get_entry( $entry, $entry_id, $form ){
			
			if( empty( $_POST['io'] ) ){
				return $entry;
			}

			$cf_ios = options::get_registry();
			$relations = array();
			foreach( $cf_ios as $io_id => $io_configs ){
				if( !empty( $io_configs['relation'] ) && $io_configs['relation'] == $_POST['io'] ){
					 $relations[ $io_id ] = options::get_single( $io_id );
				}
				if( $io_id == $_POST['io'] ){
					// add extras
					$io_configs = options::get_single( $io_id );
					$entry['entry_tab'] = $io_configs['entry_tab'];
				}
			}
			if( !empty( $relations ) ){
				$entry['interfaces'] = $relations;
			}
			$entry['entry_id'] = $entry_id;
			return $entry;
		}
	/**
	 * Create reference connection on entry save
	 *
	 * @uses "caldera_forms_entry_saved" filter
	 *
	 * @param $entryid
	 * @param $new_entry
	 * @param $form
	 *
	 * @return mixed
	 */
	public function connect_entry_reference( $form ){

		if( !empty( $_POST['io_parent'] ) ){

			if( !empty( $_POST['io_relation'] ) && !empty( $form['fields'][ $_POST['io_relation'] ] ) ){

				\Caldera_Forms::set_field_data( $_POST['io_relation'], (int) $_POST['io_parent'], $form );

			}else{				
				global $wpdb;

				$field_item = array(
					'entry_id'	=> \Caldera_Forms::do_magic_tags( '{entry_id}' ),
					'field_id'	=> '_io_parent',
					'slug'		=> '_io_parent',
					'value'		=> (int) $_POST['io_parent']
				);
				$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field_item);
			}
		}

	}

	/**
	 * pricess an entry association
	 *
	 *
	 * @param $processors
	 *
	 * @return mixed
	 */
	public function associate_id($config, $form){

		//return
	
	}

	/**
	 * Returns a json opbject of the current entries browser page
	 *
	 * @since 0.0.1
	 *
	 */
	public function browse_entries() {
		global $wpdb;
		
		//wp_send_json_success( $_POST );
		$form = \Caldera_Forms::get_form( $_POST['form'] );
		$fields = explode( ',', trim( $_POST['fields'], ',' ) );
		$field_select = array();
		foreach( $fields as $field ){
			$field_select[] = "MAX(CASE WHEN `field`.`field_id` = '" . $field . "' THEN `field`.`value` ELSE NULL END) `" . $field . "`";
		}
		$default_params = array(
			'status'	=>	'active',
			'limit'		=>	10,
			'page'		=>	1,
			'sort'		=>	'id',
			'sort_order' => 'desc',
			'relation_field' => '_io_parent'

		);


		// get params
		if( !empty( $_POST['params'] ) ){
			$is_json = json_decode( stripslashes_deep( $_POST['params'] ), ARRAY_A );
			if( is_array( $is_json ) && !empty( $is_json ) ){
				$params = array_merge( $default_params, $is_json );
			}
		}

		// parent lock
		$parent_filter = null;
		$parent_join = null;
		if( !empty( $params['parent'] ) ){
			$parent_filter = "AND
			`field`.`field_id` = '" . $params['relation_field'] . "'
			AND
			`field`.`value` = " . (int) $params['parent'];
			$parent_join .= "RIGHT JOIN `" . $wpdb->prefix . "cf_form_entry_values` AS `field` ON ( `field`.`entry_id` = `pri`.`id` )";
		}
		$search = null;
		if( !empty( $params['key_word'] ) ){
			$search = "AND\r\n (";
			$words = explode(' ', $params['key_word'] );
			$keys = array();
			foreach( $words as $word ){
				if( empty( $word ) ){ continue; }
				$keys[] = $wpdb->prepare( "`search`.`value` LIKE %s ", '%' . trim( $word ) . '%' );
			}
			$search .= implode(' OR ', $keys ) . ')';
			$parent_join .= "RIGHT JOIN `" . $wpdb->prefix . "cf_form_entry_values` AS `search` ON ( `search`.`entry_id` = `pri`.`id` )";
		}

		// counter
		$query = " SELECT
		COUNT( `pri`.`id` ) AS `total`,
		`pri`.`status` as `status`,
		GROUP_CONCAT( `pri`.`id` ) AS `list`

		FROM `" . $wpdb->prefix . "cf_form_entries` AS `pri`
		" . $parent_join . "

		WHERE
		`pri`.`form_id` = '" . $form['ID'] . "'
		" . $parent_filter . "
		" . $search . "
		GROUP BY `pri`.`status`
		";

		$total_res = $wpdb->get_results( $query );
		$totals = array();
		foreach( $total_res as $total_status ){
			$totals[ $total_status->status ] = $total_status;
		}
		
		$pages = 0;
		$return = array();
		if( !empty( $totals[ $params['status'] ]->total ) ){
			$pages = ceil( $totals[ $params['status'] ]->total / $params['limit'] );
			if( $params['page'] > $pages ){
				$params['page'] = $pages;
			}
			$offset = ($params['page'] - 1) * $params['limit'];
			$limit = $offset . ',' . $params['limit'];


			$query = "	SELECT
			`pri`.*,
			" . implode( ",\r\n\t", $field_select ) . "

			FROM `" . $wpdb->prefix . "cf_form_entries` AS `pri`
			LEFT JOIN `" . $wpdb->prefix . "cf_form_entry_values` AS `field` ON ( `field`.`entry_id` = `pri`.`id` )
			WHERE
			`pri`.`form_id` = '" . $form['ID'] . "'
			AND
			`pri`.`status` = '" . $params['status'] . "'
			AND 
			`pri`.`id` IN ( " . $totals[ $params['status'] ]->list . ") 

			GROUP BY `pri`.`id`
			ORDER BY " . $params['sort'] . " " . $params['sort_order'] . "
			LIMIT 
			" . $limit . "		
			";

			$results = $wpdb->get_results( $query, ARRAY_A );
			
			\Caldera_Forms::get_field_types();
			foreach ($results as $result) {
				//$form
				$return[ 'res' . $result['id'] ] = $result;
				foreach( $fields as $field ){
					if( isset( $return[ 'res' . $result['id'] ][ $field ] ) ){
						$return[ 'res' . $result['id'] ][ $field ] = apply_filters( 'caldera_forms_view_field_' . $form['fields'][ $field ]['type'], $return[ 'res' . $result['id'] ][ $field ], $form['fields'][ $field ], $form );
					}
				}
			}
		}

		$out = array(
			'total' => ( !empty( $totals[ $params['status'] ]->total ) ? $totals[ $params['status'] ]->total : 0 ),
			'status_totals' => $totals,
			'pages' => $pages,
			'params' => $params,
			'entries' => $return,
			'query' => $query
		);

		wp_send_json( $out );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 0.0.1
	 *
	 * @return    object|\calderawp\cfio\core    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, false, basename( CFIO_PATH ) . '/languages');

	}

	/**
	 * Register and enqueue front-specific style sheet.
	 *
	 * @since 0.0.1
	 *
	 * @return    null
	 */
	public function enqueue_front_stylescripts() {
		
		

	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since 0.0.1
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		if( !is_object( $screen ) ){
			return;

		}

		
		
		if( false !== strpos( $screen->base, 'cf_io' ) ){

			wp_enqueue_style( 'cf-field-styles', CFCORE_URL . 'assets/css/fields.min.css', array() );

			wp_enqueue_style( 'cf_io-core-style', CFIO_URL . '/assets/css/styles.css' );
			wp_enqueue_style( 'cf_io-baldrick-modals', CFIO_URL . '/assets/css/modals.css' );
			wp_enqueue_script( 'cf_io-handlebars', CFIO_URL . '/assets/js/handlebars.js' );
			wp_enqueue_script( 'cf_io-wp-baldrick', CFIO_URL . '/assets/js/wp-baldrick.js', array( 'jquery' ) , false, true );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_style( 'cf_io-codemirror-style', CFIO_URL . '/assets/css/codemirror.css' );
			wp_enqueue_script( 'cf_io-codemirror-script', CFIO_URL . '/assets/js/codemirror.js', array( 'jquery' ) , false );
			wp_enqueue_script( 'cf_io-core-script', CFIO_URL . '/assets/js/scripts.js', array( 'cf_io-wp-baldrick' ) , false );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );			
			//CF
			//wp_enqueue_style( 'cf-admin-styles', CFCORE_URL . 'assets/css/admin.css', array() );

			//wp_enqueue_script( 'cf_io-select2', CFIO_URL . '/assets/js/select2.min.js', array( 'jquery' ) , false );

			//wp_enqueue_style( 'cf_io-select2', CFIO_URL . '/assets/css/select2.css' );
			$field_types = apply_filters( 'caldera_forms_get_field_types', array() );

			foreach( $field_types as $field ){
				if( !empty( $field['styles'])){
					foreach($field['styles'] as $style){
						if( false !== strpos($style, '//')){
							wp_enqueue_style( 'cf-' . sanitize_key( basename( $style ) ), $style, array() );
						}else{
							wp_enqueue_style( $style );
						}
					}
				}

				//enqueue scripts
				if( !empty( $field['scripts'])){
					// check for jquery deps
					$depts[] = 'jquery';
					foreach($field['scripts'] as $script){
						if( false !== strpos($script, '//')){
							wp_enqueue_script( 'cf-' . sanitize_key( basename( $script ) ), $script, $depts );
						}else{
							wp_enqueue_script( $script );
						}
					}
				}	
			}		
		
		}


	}



}















