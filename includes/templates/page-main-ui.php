<?php

if( empty( $cf_io['width'] ) ){
	$cf_io['width'] = 550;
}

?><div class="cf-io-main-headercaldera">
		<h1 class="cf-io-main-title">
		<?php echo $cf_io['name']; ?> 
		<span class="cf-io-version">
			<?php //echo CFIO_VER; ?>
		</span>
		<span class="cf-io-nav-separator"></span>
		<?php if( true === $can_capture ){ ?>
		<span class="add-new-h2 cfajax-trigger"
			data-request="<?php echo site_url( "/cf-api/" . $cf_io['form'] ."/" ); ?>" 
			data-load-element="#cf-io-save-indicator"
			data-modal="newentry-{{form}}"
			data-modal-title="<?php echo esc_attr( __('Add Entry', 'cf-io') ) ; ?>"
			data-method="get"
			data-modal-width="<?php echo $cf_io['width']; ?>"
			data-modal-height="auto"
			data-modal-element="div"
			data-io_modal="{{id}}"
			data-callback="calders_forms_init_conditions"
			data-modal-buttons='Save Entry|{ "data-for" : "form.<?php echo $cf_io['form']; ?>" }'
		>
			<?php _e('Add Entry', 'cf-io') ; ?>
		</span>
		<span class="cf-io-nav-separator"></span>
		<?php } ?>

		<span style="position: absolute; top: 5px;" id="cf-io-save-indicator">
			<span style="float: none; margin: 10px 0px -5px 10px;" class="spinner"></span>
		</span>

	</h1>


		<div class="updated_notice_box">
		<?php _e( 'Updated Successfully', 'cf-io' ); ?>
	</div>
	<div class="error_notice_box">
		<?php _e( 'Could not save changes.', 'cf-io' ); ?>
	</div>
	<ul class="cf-io-header-tabs cf-io-nav-tabs">
				
		
				
	</ul>

	<span class="wp-baldrick" id="cf-io-field-sync" data-event="refresh" data-target="#cf-io-main-canvas" data-before="cfio_canvas_reset" data-callback="cfio_canvas_init" data-type="json" data-request="#cf-io-live-config" data-template="#main-ui-template"></span>
</div>
<div class="cf-io-sub-headercaldera">
	<ul class="cf-io-sub-tabs cf-io-nav-tabs">
				<li class="{{#is _current_tab value="#cf-io-panel-form"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-form">
				<?php _e('Entries', 'cf-io') ; ?>
			</a>
		</li>

	</ul>
</div>

<form class="caldera-main-form has-sub-nav" id="cf-io-main-form" action="?page=cf_io" method="POST">
	<?php wp_nonce_field( 'cf-io', 'cf-io-setup' ); ?>
	<input type="hidden" value="{{id}}" name="id" id="cf_io-id">

	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="cf-io-active-tab">
	<input type="hidden" value="{{json fields}}" name="fields">
	<input type="hidden" value="{{form}}" name="form">
	<input type="hidden" value="{{editing}}" name="editing">
	<input type="hidden" value="{{capture}}" name="capture">
	<input type="hidden" value="{{width}}" name="width">
	<input type="hidden" value="{{name}}" name="name">
	<input type="hidden" value="{{title_prefix}}" name="title_prefix">
	<input type="hidden" value="{{json title}}" name="title">
	<input type="hidden" value="{{color}}" name="color">
	<input type="hidden" value="{{entry_tab}}" name="entry_tab">
	
	{{#if relation_field}}
	<input type="hidden" value="{{relation_field}}" name="relation_field">
	{{/if}}
		<div id="cf-io-panel-form" class="cf-io-editor-panel" {{#is _current_tab value="#cf-io-panel-form"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e('Entry Browser', 'cf-io') ; ?>
			<small class="description">
				<?php _e('Entries', 'cf-io') ; ?>
			</small>
		</h4>
		<?php
			/**
			 * Include the form-panel
			 */
			include CFIO_PATH . 'includes/templates/page-form-panel.php';
		?>
	</div>


		

</form>

{{#unless _current_tab}}
	{{#script}}
		jQuery(function($){
			$('.cf-io-nav-tab').first().trigger('click').find('a').trigger('click');
		});
	{{/script}}
{{/unless}}
