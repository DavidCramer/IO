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
		add_action( 'wp_ajax_nopriv_io_browse_entries', array( $this, 'browse_entries' ) );

		// add CF magic tag for IO
		add_filter( 'caldera_forms_get_magic_tags', array( $this, 'register_io_magic_tag'), 12, 2 );
		add_filter( 'caldera_forms_do_magic_tag', array( $this, 'io_magic_tag') );


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
	 * Register IO magic tag declaration
	 *
	 * @uses "caldera_forms_get_magic_tags" filter
	 *
	 * @param $tag
	 *
	 * @return string added magic tags
	 */
	public function register_io_magic_tag( $tags, $form ){

		$cf_ios = options::get_registry();
		if( empty( $cf_ios ) ){
			$cf_ios = array();
		}
		foreach( $cf_ios as $cf_io_id => $cf_io ){
			if( !empty( $cf_io['relation'] ) && $cf_io['form'] == $form ){			
				// get the relation form field
				$relation_io = $cf_ios[ $cf_io['relation'] ];
				$relation_form = \Caldera_Forms::get_form( $relation_io['form'] ); 
				foreach( $relation_io['fields'] as $field_id=>$field ){
					if( isset( $relation_form['fields'][ $field_id ] ) ){
						$tag = 'io:' .  $cf_io['slug'] . ':' . $relation_form['fields'][ $field_id ]['slug'];
					}else{
						$tag = 'io:' .  $cf_io['slug'] . ':' . $field_id;
					}
					$tags['system']['tags'][] = $tag;
				}
				//var_dump( $relation_io['fields'] );
			}

		}

		return $tags;
	}
	/**
	 * IO magic tag declaration
	 *
	 * @uses "caldera_forms_do_magic_tag" filter
	 *
	 * @param $tag
	 *
	 * @return string completed tag or unfiultered tag if not matched
	 */
	public function io_magic_tag( $tag ){
		$parts = explode( ':', $tag );
		if( count( $parts ) === 3 && $parts[0] === 'io' ){

			if( !empty( $_REQUEST['io_parent'] ) ){
				$io_parent = filter_var( $_REQUEST['io_parent'], FILTER_SANITIZE_STRING );
				$entry_detail = \Caldera_Forms::get_entry_detail( $io_parent );
				$form = \Caldera_Forms::get_form( $entry_detail['form_id'] );
				$entry = \Caldera_Forms::get_entry( $io_parent, $form );
				foreach ($form['fields'] as $field_id => $field ) {
					if( $field['slug'] == $parts[2] ){
						if( !empty( $entry['data'][ $field_id ] ) ){
							return $entry['data'][ $field_id ]['value'];
						}
					}
				}
			}
		}
		return $tag;
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
					$entry['entry_tab'] = $io_configs['singular'];
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

				\Caldera_Forms::set_field_data( $_POST['io_relation'], $_POST['io_parent'], $form );

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
	private static function filter_compare( $filter ){
		global $wpdb;

		$return = null;
		if( !empty( $filter['value'] ) ){
			$filter['value'] = \Caldera_Forms::do_magic_tags( $filter['value'] );
		}
		//
		// field or column
		switch ( $filter['field'] ) {
			case 'id':
				$return = "`pri`.`id` ";
				break;
			case 'user_id':
				if( !empty( $filter['value'] ) ){
					if( $filter['value'] == '_current_id' ){
						$filter['value'] = get_current_user_id();
					}
					if( $filter['value'] == '_empty' ){
						$filter['value'] = 0;
					}
				}
				$return = "`pri`.`user_id` ";
				break;
			case 'datestamp':
				$return = "`pri`.`datestamp` ";
				break;
			case 'status':
				$return = "`pri`.`status` ";
				break;
			default:
				$return = "`" . $filter['_id'] . "`.`value` ";
				break;
		}


		if( !empty( $filter['compare'] ) ){

			switch ( $filter['compare'] ) {
				case "isnull":
					$return .= "IS NULL";
					break;
				case "is":
					if( strlen( $filter['value'] ) === 0 ){
						$return .= "IS NULL";
					}else{
						$return .= $wpdb->prepare( " = %s ", $filter['value'] );
					}
					break;
				case "isnot":
				case "isnot":
					$return .= $wpdb->prepare( " != %s ", $filter['value'] );
					break;
				case "isin":
					$list = explode(',', $filter['value']);
					$list = array_filter( $list, 'trim' );
					$qlist = array();
					foreach( $list as $item ){
						$qlist[] = $wpdb->prepare( "%s", $item );
					}
					$return .= " IN (" . implode( ",", $qlist ) .")";
					break;
				case "isnotin":
					$list = explode(',', $filter['value']);
					$list = array_filter( $list, 'trim' );
					$qlist = array();
					foreach( $list as $item ){
						$qlist[] = $wpdb->prepare( "%s", $item );
					}
					$return .= " NOT IN (" . implode( ",", $qlist ) .")";
					break;
				case "greater":
					$return .= $wpdb->prepare( " > %s ", $filter['value'] );
					break;
				case "greatereq":
					$return .= $wpdb->prepare( " >= %s ", $filter['value'] );
					break;
				case "smaller":
					$return .= $wpdb->prepare( " < %s ", $filter['value'] );
					break;
				case "smallereq":
					$return .= $wpdb->prepare( " <= %s ", $filter['value'] );
					break;
				case "startswith":
					$return .= $wpdb->prepare( " LIKE %s ", $filter['value'] . '%' );
					break;
				case "endswith":
					$return .= $wpdb->prepare( " LIKE %s ", '%' . $filter['value'] );
					break;
				case "contains":
					$return .= $wpdb->prepare( " LIKE %s ", '%' . $filter['value'] . '%' );
					break;
			}
		}

		return $return;
	
	}

	/**
	 * Returns a json opbject of the current entries browser page
	 *
	 * @since 0.0.1
	 *
	 */
	public function browse_entries() {
		global $wpdb;
		
		$data = stripslashes_deep( $_POST );
		$io = options::get_single( $data['io'] ); 
		//wp_send_json_success( $_POST );
		$form = \Caldera_Forms::get_form( $io['form'] );
		$field_select = array();
		foreach( $form['fields'] as $field => $field_conf ){
			$field_select[] = "MAX(CASE WHEN `field`.`field_id` = '" . $field . "' THEN `field`.`value` ELSE NULL END) `" . $field . "`";
		}
		$params = array(
			'status'	=>	'active',
			'limit'		=>	10,
			'page'		=>	1,
			'sort'		=>	'id',
			'sort_order' => 'desc',
			'relation_field' => '_io_parent',
			'filters' => array()
		);

		if( !empty( $data['params'] ) ){
			$is_json = json_decode( $data['params'], ARRAY_A );
			if( !empty( $is_json ) ){
				$params = array_merge( $params, $is_json );
			}
		}
		if( !empty( $io['params']['filters'] ) ){
			$params['filters'] = array_merge( $io['params']['filters'], $params['filters'] );
		}

		// parent lock
		$parent_filter = null;
		$search = null;
		$filter_joins = array();
		$filter_join = null;
		$parent_joins = array();
		$parent_join = null;

		// sorting
		$sorting = null;
		if( !empty( $params['sort'] ) && !empty( $form['fields'][ $params['sort'] ] ) ){
			$filter_joins[] = "RIGHT JOIN `" . $wpdb->prefix . "cf_form_entry_values` AS `sorting_field` ON ( `sorting_field`.`entry_id` = `pri`.`id` && `sorting_field`.`field_id` = '" . $params['sort'] . "' )";			
		}
		$sorting = self::filter_compare( array('field' => $params['sort'], '_id' => 'sorting_field' ) ) . " " . strtoupper( $params['sort_order'] );
		if( !empty( $params['filters'] ) ){
			

			foreach( $params['filters'] as $filter ){
				
				if( empty( $filter['field'] ) ){//|| empty( $filter['value'] ) || strlen( $filter['value'] ) < 1 ){
					continue;
				}

				// is it internal field?
				$search .= "AND\r\n (";
				$keys = array();
				if( !empty( $form['fields'][ $filter['field'] ] ) ){
					$join_type = "RIGHT"; 
					if( $filter['compare'] == 'isnull' ){
						$join_type = "LEFT OUTER";
					}
					$filter_joins[] = $join_type . " JOIN `" . $wpdb->prefix . "cf_form_entry_values` AS `" . $filter['_id'] . "` ON ( `" . $filter['_id'] . "`.`entry_id` = `pri`.`id` && `" . $filter['_id'] . "`.`field_id` = '" . $filter['field'] . "' )";
				}
				$keys[] = self::filter_compare( $filter );
				if( !empty( $filter['or'] ) ){
					foreach( $filter['or'] as $or_filter ){
						// field or meta?
						if( !empty( $form['fields'][ $or_filter['field'] ] ) ){
							$join_type = "RIGHT"; 
							if( $or_filter['compare'] == 'isnull' ){
								$join_type = "LEFT OUTER";
							}

							// field
							// check if theres an option field as not to include the mixdown
							$filter_joins[] = $join_type . " JOIN `" . $wpdb->prefix . "cf_form_entry_values` AS `" . $or_filter['_id'] . "` ON ( `" . $or_filter['_id'] . "`.`entry_id` = `pri`.`id` && `" . $or_filter['_id'] . "`.`field_id` = '" . $or_filter['field'] . "' )";
							$keys[] = self::filter_compare( $or_filter );
						}
					}
				}
				$search .= implode(' OR ', $keys ) . ')';
				
			}
			
		}
		$filter_join .= implode( "\r", $filter_joins );

		// parent lock
		$parent_filter = null;
		if( !empty( $params['parent'] ) ){
			$parent_filter = "AND
			`lock_field`.`field_id` = '" . $params['relation_field'] . "'
			AND
			`lock_field`.`value` = '" . $params['parent'] . "'";
			$parent_joins[] = "RIGHT JOIN `" . $wpdb->prefix . "cf_form_entry_values` AS `lock_field` ON ( `lock_field`.`entry_id` = `pri`.`id` )";
		}
		$parent_join .= implode( "\r", $parent_joins );
		

		// counter
		$count_query = " SELECT
		COUNT( `pri`.`id` ) as `total`
		FROM `" . $wpdb->prefix . "cf_form_entries` AS `pri`
		" . $parent_join . "
		" . $filter_join . "
		WHERE
		`pri`.`form_id` = '" . $form['ID'] . "'
		" . $parent_filter . "
		" . $search . "
		";
		$total = $wpdb->get_var( $count_query );
		$pages = ceil( $total / $params['limit'] );
		if( $params['page'] > $pages ){
			$params['page'] = $pages;
		}
		$offset = ($params['page'] - 1) * $params['limit'];
		if( $offset < 0 ){
			$offset = 0;
		}
		// query
		$query = " SELECT
		
		`pri`.`id`

		FROM `" . $wpdb->prefix . "cf_form_entries` AS `pri`
		" . $parent_join . "
		" . $filter_join . "

		WHERE
		`pri`.`form_id` = '" . $form['ID'] . "'
		" . $parent_filter . "
		" . $search . "
		ORDER BY " . $sorting . "
		LIMIT " . $offset . "," . $params['limit'] . "
		";

		$rawresults = $wpdb->get_results( $query, ARRAY_A );

		$query_results = array(
			'total'	=> 0,
			'list'	=> array()
		);
		foreach( $rawresults as $result ){
			$query_results['total'] += 1;
			$query_results['list'][] = $result['id'];
		}

		$return = array();

		if( !empty( $query_results['total'] ) ){
			$items = 0;

			foreach( $query_results['list'] as $entry_instance=>$entry_id ){
				$entry =\Caldera_Forms::get_entry( $entry_id, $form );
				$user_name = null;
				if( !empty( $entry['user']['name'] ) ){
					$user_name = $entry['user']['name'];
				}
				$return[ 'res' . $entry_id ] = array(
					'id'		=>	$entry_id,
					'form_id' 	=>	$form['ID'],
					'user_id'	=> 	$user_name,
					'datestamp'	=>	$entry['date'],
					'status'	=>	$entry['status'],
				);

				foreach( $form['fields'] as $field_id=>$field ){
					if( !empty( $entry['data'][ $field_id ] ) ){
						$return[ 'res' . $entry_id ][ $field_id ] = $entry['data'][ $field_id ]['view'];
					}else{
						$return[ 'res' . $entry_id ][ $field_id ] = null;
					}
				}
				
			}


		}
		
		$out = array(
			//'fields' => $data['fields'],
			'total' => $total,
			'status_totals' => $query_results,
			'pages' => $pages,
			'params' => $params,
			'entries' => $return,
			'query' => $query
		);
		if( !empty( $_POST['process'] ) && $_POST['process'] == 'export' ){
			$transient_id = uniqid('cf_report');
			$trans = array(
				'list' => $query_results['list'],
				'form' => $form['ID']
			);
			set_transient( $transient_id, $trans, 120 );
			$out['export_key'] = $transient_id;
		}

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
	 * fetch io_id bound to page;
	 *
	 * @since 0.0.1
	 *
	 * @return    null
	 */
	public function get_bound_io() {
		global $post;

		if( empty( $post) || $post->post_type !== 'page'){ return; }

		$cf_ios = \calderawp\cfio\options::get_registry();
		$ios = array();
		foreach( $cf_ios as $io_id=>$io ){
			if( !empty( $io['page'] ) && $post->ID == $io['page'] ){

				$cf_io = \calderawp\cfio\options::get_single( $io_id );
				if( empty( $cf_io['access_roles'] ) ){
					continue;
				}
				// check public
				$current_user = wp_get_current_user();
				if( empty( $current_user ) || empty( $current_user->ID ) ){
					if( !empty( $cf_io['access_roles']['_public'] ) ){
						$ios[] = $io_id;
					}
					continue;
				}

				$has_access = false;
				foreach( $cf_io['access_roles'] as $checkrole => $checken ){
					if( current_user_can( $checkrole ) ){
						$has_access = $checkrole;
						continue;
					}
				}
				if( empty( $has_access ) ){
					continue;
				}				

				$ios[] = $io_id;
			}
		}
		if( !empty( $ios ) ){
			return $ios;
		}
		return false;
	}

	/**
	 * Register and enqueue front-specific style sheet.
	 *
	 * @since 0.0.1
	 *
	 * @return    null
	 */
	public function enqueue_front_stylescripts() {

		$io_ids = $this->get_bound_io();
		
		if( empty( $io_ids ) ){return;}

		// yup -- do it!
		if( !empty( $io_ids ) ){
			wp_deregister_script( 'cf-dynamic' );
			add_filter( 'caldera_forms_script_urls', array( $this, 'front_scripts' ) );
			add_filter( 'caldera_forms_style_urls', array( $this, 'front_styles' ) );
			\Caldera_Forms::cf_init_system();
			add_filter( 'the_content', function( $content ){

				$formbases = $this->get_bound_io();
				ob_start();
				foreach( $formbases as $form_base ){				
					include CFIO_PATH . 'includes/page.php';
				}
				$content .= ob_get_clean();
				
				wp_enqueue_style( 'cf-field-styles', CFCORE_URL . 'assets/css/fields.min.css', array() );

			
				wp_enqueue_style( 'cf_io-front-core', CFIO_URL . 'assets/css/styles.css' );
				wp_enqueue_style( 'cf_io-front-theme', CFIO_URL . 'assets/css/jquery-ui.min.css' );

				wp_enqueue_style( 'cf_io-baldrick-modals', CFIO_URL . 'assets/css/modals.css' );
				wp_enqueue_script( 'cf_io-handlebars', CFIO_URL . 'assets/js/handlebars.js' );
				
				wp_enqueue_style( 'cf_io-footable-style', CFIO_URL . 'assets/css/footable.core.min.css' );
				wp_enqueue_script( 'cf_io-footable-script', CFIO_URL . 'assets/js/footable.min.js', array( 'jquery' ) , false );


				wp_enqueue_script( 'io-base', CFIO_URL . 'assets/js/scripts.js', array( 'jquery', 'cf-baldrick' ) , false );
				wp_localize_script( 'cf-dynamic', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
				
				wp_enqueue_script( 'cf-dynamic' );
				return $content;
			});			

		}		

	}


	/**
	 * swap out front front_scripts
	 *
	 * @since 0.0.1
	 *
	 * @return    null
	 */
	public function front_scripts( $scripts ) {
		

			//unset( $scripts['ajax'] );
			unset( $scripts['modals'] );
			$scripts['baldrick'] = CFIO_URL . 'assets/js/wp-baldrick.js';
			$scripts['io-base'] = CFIO_URL . 'assets/js/scripts.js';
			$scripts = array_merge( array( 'handlebars' => CFIO_URL . 'assets/js/handlebars.js' ), $scripts );

		return $scripts;
	}


	/**
	 * swap out front styles
	 *
	 * @since 0.0.1
	 *
	 * @return    null
	 */
	public function front_styles( $styles ) {

		$styles['modals'] = CFIO_URL . 'assets/css/modals-front.css';
		wp_enqueue_style( 'cf_io-core-style', CFIO_URL . 'assets/css/styles.css' );
		return $styles;
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

			wp_enqueue_style( 'dashicons-picker', CFIO_URL . 'assets/css/dashicons-picker.css', array( 'dashicons' ) );
			wp_enqueue_style( 'cf_io-core-style', CFIO_URL . 'assets/css/styles.css' );
			wp_enqueue_style( 'cf_io-baldrick-modals', CFIO_URL . 'assets/css/modals.css' );
			wp_enqueue_script( 'cf_io-handlebars', CFIO_URL . 'assets/js/handlebars.js' );
			wp_enqueue_script( 'cf_io-wp-baldrick', CFIO_URL . 'assets/js/wp-baldrick.js', array( 'jquery' ) , false, true );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_style( 'cf_io-codemirror-style', CFIO_URL . 'assets/css/codemirror.css' );
			wp_enqueue_script( 'cf_io-codemirror-script', CFIO_URL . 'assets/js/codemirror.js', array( 'jquery' ) , false );
			wp_enqueue_script( 'dashicons-picker', CFIO_URL . 'assets/js/dashicons-picker.js', array( 'jquery' ) );
			wp_enqueue_script( 'cf_io-core-script', CFIO_URL . 'assets/js/scripts.js', array( 'cf_io-wp-baldrick' ) , false );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );			

			wp_enqueue_style( 'cf_io-footable-style', CFIO_URL . 'assets/css/footable.core.min.css' );
			wp_enqueue_script( 'cf_io-footable-script', CFIO_URL . 'assets/js/footable.min.js', array( 'jquery' ) , false );


			//CF
			//wp_enqueue_style( 'cf-admin-styles', CFCORE_URL . 'assets/css/admin.css', array() );

			//wp_enqueue_script( 'cf_io-select2', CFIO_URL . 'assets/js/select2.min.js', array( 'jquery' ) , false );

			//wp_enqueue_style( 'cf_io-select2', CFIO_URL . 'assets/css/select2.css' );
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















