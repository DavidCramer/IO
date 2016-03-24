<?php
/**
 * Functions for this plugin
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer & CalderaWP LLC
 */

function io_build_magic_tags( $cf_io ){
	
	?>
	<span class="cf-io-magic-tags-definitions" data-tag="user:id" data-category="User"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="user:user_login" data-category="User"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="user:first_name" data-category="User"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="user:last_name" data-category="User"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="user:user_email" data-category="User"></span>

	<?php
	$users_query = new \WP_User_Query( array( 
		'fields'	=> array( 'ID', 'display_name' ),
		'orderby'	=>	'display_name',
		'number' => -1
	) );
	$users = $users_query->get_results();
	foreach( $users as $user ){
		?><span class="cf-io-magic-tags-definitions" data-tag="user:<?php echo $user->ID; ?>|<?php echo $user->display_name; ?>" data-category="User"></span><?php	
	}
	$this_form = \Caldera_Forms::get_form( $cf_io['form'] );
	foreach( $this_form['fields'] as $field_id => $field ){ 
		if( in_array( $field_id, array( "user_id", "id", "datestamp", "status" ) ) ){ continue; }
		// get field
		$field = apply_filters( 'caldera_forms_render_get_field', $field, $this_form );
		if( !empty( $field['config']['option'] ) ){
			foreach( $field['config']['option'] as $option_id=>$option ){
				$tag = $option['value'];
				if( $option['value'] != $option['label'] ){
					$tag = $option['value'] . "|" . $option['label'];
				}
			?>
			<span class="cf-io-magic-tags-definitions" data-tag="<?php echo $tag; ?>" data-category="<?php echo $field['label']; ?>"></span>		
			<?php }
		}
	}

	?>



	<span class="cf-io-magic-tags-definitions" data-tag="date|today" data-category="Date"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="date|yesterday" data-category="Date"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="date|-2 days" data-category="Date"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="date:now" data-category="Date"></span>

	<span class="cf-io-magic-tags-definitions" data-tag="ip" data-category="Variable"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="get:*" data-category="Variable"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="post:*" data-category="Variable"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="request:*" data-category="Variable"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="login_url" data-category="Variable"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="logout_url" data-category="Variable"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="register_url" data-category="Variable"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="lostpassword_url" data-category="Variable"></span>

	<span class="cf-io-magic-tags-definitions" data-tag="post_meta:*" data-category="Post"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="embed_post:ID" data-category="Post"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="embed_post:post_title" data-category="Post"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="embed_post:permalink" data-category="Post"></span>
	<span class="cf-io-magic-tags-definitions" data-tag="embed_post:post_date" data-category="Post"></span>
	<?php

}

function io_build_card( $cf_io, $child = false ){
	$forms = Caldera_Forms::get_forms();
	if( empty( $forms[ $cf_io['form'] ] ) ){
		return;
	}
	if( true === $child ){
		$cf_io['icon'] = 'dashicons-arrow-right-alt2';
	}else{
		$pagebinds = \calderawp\cfio\options::get_single( 'io_page_binds' );
		if( empty( $pagebinds ) ){
			$pagebinds = array();
		}
	}

	echo '<tr id="cf_io-' . $cf_io[ 'id' ] . '">';
		echo "<td>";
			if( !empty( $child ) ){
				echo '— ';
			}
			echo $cf_io['name'];
		?>
			<div class="cf-io-card-actions">
				<div class="row-actions">
					<span class="edit">
						<a href="?page=cf_io&amp;edit=<?php echo $cf_io[ 'id' ]; ?>"><?php _e('Edit', 'cf-io'); ?></a> |
					</span>
					<span class="edit">
						<a href="?page=cf_io&amp;download=<?php echo $cf_io[ 'id' ]; ?>&cf-io-export=<?php echo wp_create_nonce( 'cf-io' ); ?>" target="_blank"><?php _e('Export', 'cf-io'); ?></a> |
					</span>
					<span class="trash confirm">
						<a href="?page=cf_io&amp;delete=<?php echo $cf_io[ 'id' ]; ?>" data-block="<?php echo $cf_io[ 'id' ]; ?>" class="submitdelete">
							<?php _e('Delete', 'cf-io'); ?>
						</a>
					</span>
				</div>
				<div class="row-actions" style="display:none;">
					<span class="trash">
						<a class="wp-baldrick" style="cursor:pointer;" data-action="cfio_delete_cf_io" data-callback="cfio_remove_deleted" data-block="<?php echo $cf_io['id']; ?>" class="submitdelete"><?php _e('Confirm Delete', 'cf-io'); ?></a> | </span>
					<span class="edit confirm">
						<a href="?page=cf_io&amp;edit=<?php echo $cf_io['id']; ?>">
							<?php _e('Cancel', 'cf-io'); ?>
						</a>
					</span>
				</div>
			</div>
		<?php			
		echo "</td>";
		echo "<td>";
			echo $forms[ $cf_io['form'] ]['name'];
		echo "</td>";
		
		echo "<td>";
			if( false === $child ){
				if( !empty( $cf_io['page'] ) ){
					$page = get_page( $cf_io['page'] );
					echo '<a href="' . get_permalink( $page->ID ) . '" target="_blank">' . $page->post_title . '</a>';
				}elseif( !empty( $cf_io['icon'] ) ){
					echo esc_html__( 'Admin Menu', 'io' );
				}				
			}else{
				echo esc_html__( 'Relation', 'io' );
			}
		echo "</td>";

	echo '</tr>';
	/*
	?>
	<div class="cf-io-card-item" id="cf_io-<?php echo $cf_io[ 'id' ]; ?>">
		<span class="dashicons <?php echo $cf_io['icon']; ?> cf-io-card-icon"></span>
		<div class="cf-io-card-content">
			<h4>
				<?php echo $cf_io[ 'name' ]; ?>
			</h4>
			<div class="description">
				<?php echo $cf_io[ 'slug' ]; ?>
			</div>
			<?php
			if( false === $child ){
				$args = array( 					
					'name' => 'bind-' . $cf_io[ 'id' ], 
					'class' => 'page-bind'
				);
				if( !empty( $pagebinds['pages'][ $cf_io[ 'id' ] ] ) ){
					$args['selected'] = $pagebinds['pages'][ $cf_io[ 'id' ] ];
				}

				//echo '<div data-id="' . $cf_io[ 'id' ] . '" class="description">';
				//wp_dropdown_pages( $args );
				//echo '</div>';
			}
			?>
			<div class="cf-io-card-actions">
				<div class="row-actions">
					<span class="edit">
						<a href="?page=cf_io&amp;download=<?php echo $cf_io[ 'id' ]; ?>&cf-io-export=<?php echo wp_create_nonce( 'cf-io' ); ?>" target="_blank"><?php _e('Export', 'cf-io'); ?></a> |
					</span>
					<span class="edit">
						<a href="?page=cf_io&amp;edit=<?php echo $cf_io[ 'id' ]; ?>"><?php _e('Edit', 'cf-io'); ?></a> |
					</span>
					<span class="trash confirm">
						<a href="?page=cf_io&amp;delete=<?php echo $cf_io[ 'id' ]; ?>" data-block="<?php echo $cf_io[ 'id' ]; ?>" class="submitdelete">
							<?php _e('Delete', 'cf-io'); ?>
						</a>
					</span>
				</div>
				<div class="row-actions" style="display:none;">
					<span class="trash">
						<a class="wp-baldrick" style="cursor:pointer;" data-action="cfio_delete_cf_io" data-callback="cfio_remove_deleted" data-block="<?php echo $cf_io['id']; ?>" class="submitdelete"><?php _e('Confirm Delete', 'cf-io'); ?></a> | </span>
					<span class="edit confirm">
						<a href="?page=cf_io&amp;edit=<?php echo $cf_io['id']; ?>">
							<?php _e('Cancel', 'cf-io'); ?>
						</a>
					</span>
				</div>
			</div>
		</div>
	</div>
	<?php
	*/
	// get relations
	$cf_ios = \calderawp\cfio\options::get_registry();
	foreach( $cf_ios as $inner_id => $inner_io ){
		if( $inner_id == $cf_io[ 'id' ] || empty( $inner_io['relation'] ) || $inner_io['relation'] !== $cf_io[ 'id' ] ){
			continue;
		}
		
		io_build_card( $inner_io, true );
		
	}	
}




function io_bind_interface_init(){

}
add_action( 'wp_print_styles', 'io_bind_interface_init' );