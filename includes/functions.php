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


function io_build_card( $cf_io, $child = false ){
	if( true === $child ){
		$cf_io['icon'] = 'dashicons-arrow-right-alt2';
	}else{
		$pagebinds = \calderawp\cfio\options::get_single( 'io_page_binds' );
		if( empty( $pagebinds ) ){
			$pagebinds = array();
		}
	}
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
	// get relations
	$cf_ios = \calderawp\cfio\options::get_registry();
	foreach( $cf_ios as $inner_id => $inner_io ){
		if( $inner_id == $cf_io[ 'id' ] || empty( $inner_io['relation'] ) || $inner_io['relation'] !== $cf_io[ 'id' ] ){
			continue;
		}
		echo '<div class="cf-io-card-child">';
		io_build_card( $inner_io, true );
		echo '</div>';
	}	
}




function io_bind_interface_init(){

}
add_action( 'wp_print_styles', 'io_bind_interface_init' );