<?php
/**
 * IO Options.
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */

namespace calderawp\cfio;

/**
 * Options class.
 *
 * @package Cf_Io
 * @author  David Cramer
 */
class options {

	/**
	 * The name of the option we use for this plugin
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	public static $option_name = 'cf_io';

	/**
	 * Create a new cf_io.
	 *
	 * @since 0.0.1
	 *
	 * @param string $name Name for cf_io.
	 * @param string $slug Slug for cf_io.
	 *
	 * @return array|void|bool Config array for new cf_io if it exists. Void if not. False if not allowed
	 */
	public static function create( $name, $slug, $form ) {
		$can = self::can();
		if ( $can ) {
			$name     = sanitize_text_field( $name );
			$slug     = sanitize_title_with_dashes( $slug );
			$form     = sanitize_text_field( $form );
			$item_id  = self::create_unique_id();
			$registry = self::get_registry();

			if ( ! isset( $registry[ $item_id ] ) ) {
				$new = array(
					'id'   => $item_id,
					'name' => $name,
					'slug' => $slug,
					'form' => $form,
				);

				$registry[ $item_id ] = $new;

				self::update( $new, $registry );

				return $new;

			}
		} else {
			return $can;

		}

	}

	/**
	 * Get an individual item by its ID.
	 *
	 * @since 0.0.1
	 *
	 * @param string $id cf_io ID
	 *
	 * @return bool|array cf_io config or false if not found.
	 */
	public static function get_single( $id ) {
		$option_name = self::item_option_name( $id );
		$cf_io = get_option( $option_name, false );

		// try slug based lookup
		if ( false === $cf_io ){
			$registry = self::get_registry();
			foreach ( $registry as $single_id => $single ) {
				if ( $single['slug'] === $id ) {
					$option_name = self::item_option_name( $single_id );
					$cf_io = get_option( $option_name, false );
					break;
				}

			}

		}

		/**
		 * Filter a cf_io config before returning
		 *
		 * @since 0.0.1
		 *
		 * @param array $cf_io The config to be returned
		 * @param string $option_name The name of the option it was stored in.
		 */
		return apply_filters( 'cf_io_get_single', $cf_io, $option_name );


	}

	/**
	 * Get the registry of cf_io.
	 *
	 * @since 0.0.1
	 *
	 * @return mixed|bool Array of cf_ios or false if not allowed.
	 */

	public static function get_registry() {
		$registry = get_option( self::registry_name(), array() );

		/**
		 * Filter the registry before returning
		 *
		 * @since 0.0.1
		 */

		return apply_filters( 'cf_io_get_registry', $registry );


	}

	/**
	 * Update both a single item and the registry
	 *
	 * @since 0.0.1
	 *
	 * @param array $config Single item config.
	 * @param array|bool. Optional. If false, current registry will be used, if is array, that reg
	 */
	public static function update( $config, $update_registry = false ) {
		if ( ! is_array( $update_registry ) ) {
			$update_registry = self::get_registry();
		}

		if( isset( $config['id'] ) && !empty( $update_registry[ $config['id'] ] ) ){
			$update = array(
				'id'	=>	$config['id'],
				'name'	=>	$config['name'],
				'slug'	=>	$config['slug'],
			);
			if( !empty( $config['fields'] ) ){
				$update['fields'] = $config['fields'];
			}
			if( !empty( $config['form'] ) ){
				$update['form'] = $config['form'];
			}
			if( !empty( $config['relation'] ) ){
				$update['relation'] = $config['relation'];
			}
			if( !empty( $config['page'] ) ){
				$update['page'] = $config['page'];
			}
			if( !empty( $config['lock_form'] ) ){
				$update['lock_form'] = $config['lock_form'];
			}
			if( !empty( $config['icon'] ) ){
				$update['icon'] = $config['icon'];
			}
			// add search form to registery
			if( ! empty( $config['search_form'] ) ){
				$updated_registery['search_form'] = $config['search_form'];
			}

			$update_registry[ $config[ 'id' ] ] = $update;

		}

		self::save_registry( $update_registry );

		self::save_single( $config['id'], $config );

	}

	/**
	 * Delete an item and clear it from the registry
	 *
	 * @since 0.0.1
	 *
	 * @param string $id Item ID
	 *
	 * @return bool True on success.
	 */
	public static function delete( $id ) {
		$deleted = delete_option( self::item_option_name( $id ) );
		if ( $deleted ) {
			$registry = self::get_registry();
			if ( isset( $registry[ $id ] ) ) {
				unset( $registry[ $id ] );
				return self::save_registry( $registry );

			}

		}

	}

	/**
	 * Save the registry of items.
	 *
	 * @since 0.0.1
	 *
	 * @param array $registry The registry
	 *
	 * @return bool
	 */
	protected static function save_registry( $registry ) {
		return update_option( self::registry_name(), $registry );

	}

	/**
	 * Save an individual item.
	 *
	 * @param string $id cf_io ID
	 * @param array $config cf_io config
	 *
	 * @return bool
	 */
	protected static function save_single( $id, $config ) {
		return update_option( self::item_option_name( $id ), $config );

	}



	/**
	 * Get the name to use for an individual item option.
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	protected static function item_option_name( $id ) {
		$name = self::$option_name . '_' . $id;
		if ( 50 < strlen( $name ) ) {
			$name = md5( $name );
		}

		return $name;

	}

	/**
	 * Get the name used for the registry option
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected static function registry_name() {
		return '_' . self::$option_name . '_registry';

	}

	/**
	 * Create unique ID
	 *
	 * @since 0.0.1
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected static function create_unique_id() {
		$slug_parts = explode( '_', 'cf_io' );
		$slug = '';

		foreach ( $slug_parts as $slug_part ) {
			$slug .= substr( $slug_part, 0,1 );
		}

		$slug = strtoupper( $slug );

		$item_id = uniqid( $slug ) . rand( 100, 999 );

		return $item_id;

	}

	/**
	 * Generic capability check to use before reading/writing
	 *
	 * @since 0.0.1
	 *
	 * @param string $cap Optional. Capability to check. Defaults to 'manage_options'
	 *
	 * @return bool
	 */
	public static function can( $cap = 'manage_options' ) {
		return current_user_can( $cap );

	}

}
