<?php
/**
 * Main admin interface for selecting items to edit/ creating or deleting items.
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */
?>

<div class="wrap cf-io-calderaadmin-wrap" id="cf-io-admin--wrap">
	<div class="cf-io-main-headercaldera">
		<h1>
			<?php _e( 'IO', 'cf-io' ); ?>
			<span class="cf-io-version">
				<?php echo CFIO_VER; ?>
			</span>
			<span class="add-new-h2 wp-baldrick" data-modal="new-cf_io" data-modal-height="222" data-modal-width="405" data-modal-buttons='<?php _e( 'Create Interface', 'cf-io' ); ?>|{"data-action":"cfio_create_cf_io","data-before":"cfio_create_new_cf_io", "data-callback": "bds_redirect_to_cf_io"}' data-modal-title="<?php _e('New Interface', 'cf-io') ; ?>" data-request="#new-cf_io-form">
				<?php _e('Add New', 'cf-io') ; ?>
			</span>
			<span class="cf-io-nav-separator"></span>
			<span class="add-new-h2 wp-baldrick" data-modal="import-cf_io" data-modal-height="auto" data-modal-width="380" data-modal-buttons='<?php _e( 'Import', 'cf-io' ); ?>|{"id":"cfio_import_init", "data-action":"cfio_create_cf_io","data-before":"cfio_create_new_cf_io", "data-callback": "bds_redirect_to_cf_io"}' data-modal-title="<?php _e('Import Interface', 'cf-io') ; ?>" data-request="cfio_start_importer" data-template="#import-cf_io-form">
				<?php _e('Import', 'cf-io') ; ?>
			</span>
		</h1>
	</div>

<?php

	$cf_ios = \calderawp\cfio\options::get_registry();
	if( empty( $cf_ios ) ){
		$cf_ios = array();
	}

	global $wpdb;
	
	foreach( $cf_ios as $cf_io_id => $cf_io ){
		if( empty( $cf_io['icon'] ) ){
			$cf_io['icon'] = 'dashicons-admin-generic';
		}
		if( !empty( $cf_io['relation'] ) ){
			continue;
		}
	echo '<div class="cf-io-interface-wrapper">';
	io_build_card( $cf_io );
	echo '</div>';

	} ?>

</div>
<div class="clear"></div>
<script type="text/javascript">
	
	function cfio_create_new_cf_io(el){
		var cf_io 	= jQuery(el),
			name 	= jQuery("#new-cf_io-name"),
			slug 	= jQuery('#new-cf_io-slug'),
			form 	= jQuery('#new-cf_io-formid'),
			imp 	= jQuery('#new-cf_io-import'); 

			name.removeClass('io-input-error');
			slug.removeClass('io-input-error');
			form.removeClass('io-input-error');

		if( imp.length ){
			if( !imp.val().length ){
				return false;
			}
			cf_io.data('import', imp.val() );
			return true;
		}


		if( slug.val().length === 0 ){
			name.focus().addClass('io-input-error');
			return false;
		}
		if( slug.val().length === 0 ){
			slug.focus().addClass('io-input-error');
			return false;
		}
		if( form.val().length === 0 ){
			form.focus().addClass('io-input-error');
			return false;
		}

		cf_io.data({
			name : name.val(),
			slug : slug.val(),
			formid : form.val()
		});	

	}

	jQuery('.page-bind').change( function(){
		var select = jQuery( this ),
			data = {
				action 	: 'cfio_bind_io',
				id		: select.parent().data('id'),
				page	: select.val()
			};
		jQuery.post( ajaxurl, data, function(res){
			console.log( res );
		});
	});

	function bds_redirect_to_cf_io(obj){
		
		if( obj.data.success ){

			obj.params.trigger.prop('disabled', true).html('<?php _e('Loading IO', 'cf-io'); ?>');
			window.location = '?page=cf_io&edit=' + obj.data.data.id;

		}else{

			jQuery('#new-block-slug').focus().select();
			
		}
	}
	function cfio_remove_deleted(obj){

		if( obj.data.success ){
			jQuery( '#cf_io-' + obj.data.data.block ).fadeOut(function(){
				jQuery(this).remove();
			});
		}else{
			alert('<?php echo __('Sorry, something went wrong. Try again.', 'cf-io'); ?>');
		}
	}
	function cfio_start_importer(){
		return {};
	}
</script>
<script type="text/html" id="new-cf_io-form">
	<div class="cf-io-config-group">
		<label>
			<?php _e('Interface Name', 'cf-io'); ?>
		</label>
		<input type="text" name="name" id="new-cf_io-name" data-sync="#new-cf_io-slug" autocomplete="off">
	</div>
	<div class="cf-io-config-group">
		<label>
			<?php _e('Interface Slug', 'cf-io'); ?>
		</label>
		<input type="text" name="slug" id="new-cf_io-slug" data-format="slug" autocomplete="off">
	</div>
	<div class="cf-io-config-group">
		<label>
			<?php _e('Interface Form', 'cf-io'); ?>
		</label>
		<select style="width:190px;" name="form" id="new-cf_io-formid">
			<option></option>
			<?php
				$forms = Caldera_Forms::get_forms();
				foreach( $forms as $form ){
				?>
				<option value="<?php echo esc_attr( $form['ID'] ); ?>"><?php echo $form['name']; ?></option>
				<?php
				}
				?>
		</form>
	</div>

</script>
<script type="text/html" id="import-cf_io-form">
	<div class="import-tester-config-group">
		<input id="new-cf-io-import-file" type="file" class="regular-text">
		<input id="new-cf_io-import" value="" name="import" type="hidden">
	</div>
	{{#script}}
		jQuery( function($){

			$('#cfio_import_init').prop('disabled', true).addClass('disabled');

			$('#new-cf-io-import-file').on('change', function(){
				$('#cfio_import_init').prop('disabled', true).addClass('disabled');
				var input = $(this),
					f = this.files[0],
				contents;

				if (f) {
					var r = new FileReader();
					r.onload = function(e) { 
						contents = e.target.result;
						var data;
						 try{ 
						 	data = JSON.parse( contents );
						 } catch(e){};
						 
						 if( !data || ! data['cf-io-setup'] ){
						 	alert("<?php echo esc_attr( __('Not a valid IO export file.', 'cf-io') ); ?>");
						 	input[0].value = null;
							return false;
						 }

						$('#new-cf_io-import').val( contents );
						$('#cfio_import_init').prop('disabled', false).removeClass('disabled');
					}
					r.readAsText(f);
				} else { 
					alert("Failed to load file");
					return false;
				}
			});

		});
	{{/script}}
</script>
