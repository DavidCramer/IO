<?php
/**
 * Main edit interface for admin page.
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */

$cf_io = \calderawp\cfio\options::get_single( $form_base );
$can_capture = false;
if( !empty( $cf_io['capture_roles'] ) ){
 	if( !empty( $cf_io['capture_roles']['_all_roles'] ) ){
 		$can_capture = true;
 	}else{
 		foreach( $cf_io['capture_roles'] as $role => $enabled ){
 			if( current_user_can( $role ) ){
 				$can_capture = true;
 				break;
 			}
 		}
 	}
}

$can_edit = false;
if( !empty( $cf_io['edit_roles'] ) ){
 	if( !empty( $cf_io['edit_roles']['_all_roles'] ) ){
 		$can_edit = true;
 	}else{
 		foreach( $cf_io['edit_roles'] as $role => $enabled ){
 			if( current_user_can( $role ) ){
 				$can_edit = true;
 				break;
 			}
 		}
 	}
}

// clear out locked filters
$cf_io['params']['filters'] = array();

?>
<style type="text/css">
	
.cf-io-main-headercaldera {
	box-shadow: 8px 1px 6px rgba(0, 0, 0, 0.04), 8px 0 0 <?php echo $cf_io['color']; ?> inset;
}	
.cf-io-sub-headercaldera {
 	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.08), 8px 0 0 <?php echo $cf_io['color']; ?> inset;
}
.cf-io-main-headercaldera .logo-icon{
	color: <?php echo $cf_io['color']; ?>;
}
.cf-io-main-headercaldera .add-new-h2.current {
	background: none repeat scroll 0 0 <?php echo $cf_io['color']; ?>;
}
.cf-io-main-headercaldera .add-new-h2:hover {
	background: none repeat scroll 0 0 <?php echo $cf_io['color']; ?>;
}
.cf-io-main-headercaldera .cf-io-header-tabs > li > a{
	color:<?php echo $cf_io['color']; ?>;
}
.cf-io-main-headercaldera .cf-io-header-tabs > li.active > a{
	background-color: <?php echo $cf_io['color']; ?>;
}
.cf-io-main-headerbrickroad .cf-io-header-tabs > li.active > a {
	background-color: <?php echo $cf_io['color']; ?>;
}
.cf-io-sub-headercaldera .cf-io-sub-tabs > li > a{
	color:<?php echo $cf_io['color']; ?>;
}
.cf-io-sub-headerbrickroad .cf-io-sub-tabs > li > a {
	color: <?php echo $cf_io['color']; ?>;
}
.cf-io-sub-headercaldera .cf-io-sub-tabs > li.active > a{
	background-color: <?php echo $cf_io['color']; ?>;
}
.baldrick-modal-title .modal-label{
	background-color: <?php echo $cf_io['color']; ?>;
}
.cf-io-modal-title > h3 {
	background-color: <?php echo $cf_io['color']; ?>;
}
.baldrick-modal-wrap .navtabs > li a {
  color: <?php echo $cf_io['color']; ?>;
}
.baldrick-modal-wrap .navtabs > li.selected > a {
  background: none repeat scroll 0 0 <?php echo $cf_io['color']; ?>;
}
</style>
<?php

wp_enqueue_style( 'cf-grid-styles' );
wp_enqueue_style( 'cf-form-styles' );
wp_enqueue_style( 'cf-alert-styles' );
wp_enqueue_style( 'cf-field-styles' );

wp_enqueue_script( 'cf-field' );
wp_enqueue_script( 'cf-conditionals' );
wp_enqueue_script( 'cf-validator' );
wp_enqueue_script( 'cf-init' );
//echo \Caldera_Forms::render_form( $cf_io['form'] );
//echo '<a href="#" class="caldera-forms-modal" data-form="' . $cf_io['form']. '" data-entry="33" data-animate="0 -20px" data-button="Save Changes" data-width="650" data-title="FORM">CLICK</a>';
//wp_enqueue_script( 'cf-dynamic' );

// simplyfy creation
if( false === $cf_io ){
	$cf_io = array(
		'id' => 'io'
	);
}
$cf_io['_current_tab'] = '#cf-io-panel-form';

if( is_admin() ){
?>
<div class="wrap cf-io-calderamain-canvas" id="cf-io-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#cf-io-main-canvas" data-before="cfio_canvas_reset" data-callback="cfio_canvas_init" data-type="json" data-request="#cf-io-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>
<?php }else{ ?>
<div class="io-interface-wrap" id="cf-io-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#cf-io-main-canvas" data-before="cfio_canvas_reset" data-callback="cfio_canvas_init" data-type="json" data-request="#cf-io-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>
<?php } ?>


<div class="clear"></div>

<input type="hidden" class="clear" autocomplete="off" id="cf-io-live-config" style="width:100%;" value="<?php echo esc_attr( json_encode($cf_io) ); ?>">

<script type="text/html" id="main-ui-template">
	<?php
		/**
		 * Include main UI
		 */
		include CFIO_PATH . 'includes/templates/page-main-ui.php';
	?>	
</script>

<script type="text/html" id="io-viewer-template">


	<div id="main-entry-panel" class="tab-detail-panel" data-tab="{{entry_tab}}">
		{{#each data}}
			<div class="entry-line">
				<label>{{label}}</label>
				<div>{{#if view/label}}{{view/value}}{{else}}{{{view}}}{{/if}}&nbsp;</div>
			</div>
		{{/each}}
	</div>

	{{#each interfaces}}
	<div id="panel-interface-{{@key}}-tab" class="tab-detail-panel" data-tab="{{name}}">
		<form id="panel-interface-{{@key}}-form" data-id="{{@key}}">
			<input name="id" value="{{id}}" type="hidden">
			<input name="name" value="{{name}}" type="hidden">
			<input name="slug" value="{{slug}}" type="hidden">
			<input name="fields" value="{{json fields}}" type="hidden">
			<input name="form" value="{{form}}" type="hidden">
			<input name="params" value="" type="hidden" id="params-init-{{id}}">
			{{#is relation_field_from value="_entry_id"}}
				<input name="parent_id" value="{{../entry_id}}" type="hidden">
			{{else}}
				
				{{#find ../data relation_field_from}}
				<input name="parent_id" value="{{value}}" type="hidden">
				{{/find}}
			{{/is}}
			{{#if relation}}
			<input name="relation" value="{{relation}}" type="hidden">
			{{/if}}
			<input type="hidden" value="{{editing}}" name="editing">
			<input type="hidden" value="{{capture}}" name="capture">
			<input type="hidden" value="{{width}}" name="width">
			<input type="hidden" value="{{title_prefix}}" name="title_prefix">
			<input type="hidden" value="{{json title}}" name="title">
			<input type="hidden" value="{{color}}" name="color">
			<input type="hidden" value="{{entry_tab}}" name="entry_tab">
			{{#if relation_field}}
			<input type="hidden" value="{{relation_field}}" name="relation_field">
			{{/if}}

			<input type="hidden" name="data" value="{{#if data}}{{json data}}{{/if}}" class="wp-baldrick" 
				data-request="#panel-interface-{{@key}}-form"
				data-target="#panel-interface-{{@key}}"
				data-template="#io-list-template-{{id}}"
				data-event="change"
				id="entry-trigger-{{id}}"
				data-autoload="true"
			>
			<div id="panel-interface-{{@key}}"></div>
		</form>
		
	</div>
	{{/each}}

	{{#if meta}}
	{{#each meta}}
	<div id="meta-{{@key}}" data-tab="{{name}}" class="tab-detail-panel">
	<h4>{{name}}</h4>
	<hr>
	{{#unless template}}
		{{#each data}}
			{{#if title}}
				<h4>{{title}}</h4>
			{{/if}}
			{{#each entry}}
				<div class="entry-line">
					<label>{{meta_key}}</label>
					<div>{{{meta_value}}}&nbsp;</div>
				</div>
			{{/each}}
		{{/each}}
	{{/unless}}
	<?php do_action('caldera_forms_entry_meta_templates'); ?>
	</div>
	{{/each}}
	{{/if}}

</script>


<?php
$cf_ios = \calderawp\cfio\options::get_registry();
$done = array();
foreach( $cf_ios as $cf_io_id=>$cf_io_config ){
	if( empty( $cf_io_config['form'] ) || in_array( $cf_io_config['form'], $done ) ){
		continue;
	}
	$done[] = $cf_io_config['form'];
?>
<script type="text/html" id="cfajax_<?php echo $cf_io_config['form']; ?>-tmpl">
{{#script}}
jQuery('#newentry-<?php echo $cf_io_config['form']; ?>_baldrickModalCloser,.io-entry-loader-<?php echo $cf_io_config['form']; ?>').trigger('click');
{{/script}}
</script>
<script type="text/html" data-handlebars-partial="list_template_<?php echo $cf_io_id; ?>">
<?php
	// pull in the table list
	include CFIO_PATH . 'includes/templates/template-table-list.php';

?>
</script>
<script type="text/html" id="io-list-template-<?php echo $cf_io_id; ?>">
	<?php
		$cf_io_config = \calderawp\cfio\options::get_single( $cf_io_id );
		$can_capture = false;
		if( !empty( $cf_io_config['capture_roles'] ) ){
		 	if( !empty( $cf_io_config['capture_roles']['_all_roles'] ) ){
		 		$can_capture = true;
		 	}else{
		 		foreach( $cf_io_config['capture_roles'] as $role => $enabled ){
		 			if( current_user_can( $role ) ){
		 				$can_capture = true;
		 				break;
		 			}
		 		}
		 	}
		}

		$can_edit = false;
		if( !empty( $cf_io_config['edit_roles'] ) ){
		 	if( !empty( $cf_io_config['edit_roles']['_all_roles'] ) ){
		 		$can_edit = true;
		 	}else{
		 		foreach( $cf_io_config['edit_roles'] as $role => $enabled ){
		 			if( current_user_can( $role ) ){
		 				$can_edit = true;
		 				break;
		 			}
		 		}
		 	}
		}

		// pull in the table list
		include CFIO_PATH . 'includes/templates/template-table-list.php';
	?>
</script>
<script type="text/html" data-handlebars-partial="filter_query_<?php echo $cf_io_id; ?>">
	<?php
		// pull in the filters partial
		include CFIO_PATH . 'includes/templates/partial-filters.php';
	?>
</script>
<?php 

}

 ?>
<script type="text/javascript">
	function cfio_start_importer(){
		return {};
	}
	function cfio_create_cf_io(){
		jQuery('#cf-io-field-sync').trigger('refresh');
		jQuery('#cfio-save-button').trigger('click');
	}
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

						$('#cf-io-live-config').val( contents );						
						$('#cfio_import_init').prop('disabled', false).removeClass('disabled');
					}
					if( f.type !== 'application/json' ){
						alert("<?php echo esc_attr( __('Not a valid IO export file.', 'cf-io') ); ?>");
						this.value = null;
						return false;
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
<script type="text/javascript">
	jQuery( function( $ ){
		$(document).on( 'change', "[name^='params[']", function(){
			var clicked = $( this );
			if( clicked.hasClass('io-entrycheck') || clicked.is('select') ){
				return;
			}
			clicked.addClass('disabled');
			clicked.closest( '.io-panel-wrapper' ).find('.io-entry-loader').trigger('click');
		} );
		$(document).on( 'click', ".pagination", function(){
			var clicked = $(this),
				page_input = $('.current-page'),
				current_page = parseInt( page_input.val() ),
				total_pages = parseInt( $('.total-pages').text() ),
				next_page;

			if( clicked.data('page') === 'next' ){
				if( total_pages < current_page + 1 ){
					return;
				}
				next_page = current_page + 1;
			} else if( clicked.data('page') === 'prev' ){
				if( current_page - 1 < 1 ){
					return;
				}
				next_page = current_page - 1;
			}else{
				next_page = parseInt( clicked.data('page') );
			}

			page_input.val( next_page ).trigger('change');
		} );
	});

</script>