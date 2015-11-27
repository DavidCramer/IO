<div class="cf-io-main-headercaldera">
		<h1>
		<span id="cf_io-name-title">{{name}}</span>
		<span class="cf-io-subline">{{slug}}</span>
		<span class="cf-io-nav-separator"></span>
		<span class="add-new-h2 wp-baldrick" data-action="cfio_save_config" data-load-element="#cf-io-save-indicator" data-callback="cfio_handle_save" data-before="cfio_get_config_object" >
			<?php _e('Save Changes', 'cf-io') ; ?>
		</span>
		<span class="cf-io-nav-separator"></span>

	</h1>
	<span style="position: absolute; margin-left: -18px;" id="cf-io-save-indicator">
		<span style="float: none; margin: 16px 0px -5px 10px;" class="spinner"></span>
	</span>
		<div class="updated_notice_box">
		<?php _e( 'Updated Successfully', 'cf-io' ); ?>
	</div>
	<div class="error_notice_box">
		<?php _e( 'Could not save changes.', 'cf-io' ); ?>
	</div>
	<ul class="cf-io-header-tabs cf-io-nav-tabs">
				
		<li class="{{#is _current_tab value="#cf-io-panel-general"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-general">
				<?php _e('General', 'cf-io') ; ?>
			</a>
		</li>
		{{#if form}}
		<li class="{{#is _current_tab value="#cf-io-panel-form"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-form">
				<?php _e('Form', 'cf-io') ; ?>
			</a>
		</li>		
		{{/if}}
		<?php /* ?>
		{{#if custom_template}}		
		<li class="{{#is _current_tab value="#cf-io-panel-template"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-template">
				<?php _e('Template', 'cf-io') ; ?>
			</a>
		</li>
		{{/if}}
		<?php */ ?>
	</ul>

	<span class="wp-baldrick" id="cf-io-field-sync" data-event="refresh" data-target="#cf-io-main-canvas" data-before="cfio_canvas_reset" data-callback="cfio_canvas_init" data-type="json" data-request="#cf-io-live-config" data-template="#main-ui-template"></span>
</div>

<form class="caldera-main-form " id="cf-io-main-form" action="?page=cf_io" method="POST">
	<?php wp_nonce_field( 'cf-io', 'cf-io-setup' ); ?>
	<input type="hidden" value="{{id}}" name="id" id="cf_io-id">
	<input type="hidden" value="{{#if forms}}{{json forms}}{{/if}}" name="forms" id="cf-io-forms">
	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="cf-io-active-tab">

	<div id="cf-io-panel-general" class="cf-io-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#cf-io-panel-general"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'General Settings', 'cf-io' ); ?>
			<small class="description">
				<?php _e( 'General', 'cf-io' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include general settings template
			 */
			include CFIO_PATH . 'includes/templates/general-settings.php';
		?>
	</div>
	<div id="cf-io-panel-form" class="cf-io-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#cf-io-panel-form"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'Form Binding', 'cf-io' ); ?>
			<small class="description">
				<?php _e( 'Form', 'cf-io' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include form settings template
			 */
			include CFIO_PATH . 'includes/templates/form-panel.php';
		?>
	</div>

	<?php /* ?>
	<div id="cf-io-panel-template" class="cf-io-editor-panel" {{#is _current_tab value="#cf-io-panel-template"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e('HTML', 'cf-io') ; ?>
			<small class="description">
				<?php _e('Template', 'cf-io') ; ?>
			</small>
		</h4>
		<?php
			
			//include CFIO_PATH . 'includes/templates/template-panel.php';
		?>
	</div>
	<?php */ ?>

</form>

{{#unless _current_tab}}
	{{#script}}
		jQuery(function($){
			$('.cf-io-nav-tab').first().trigger('click').find('a').trigger('click');
		});
	{{/script}}
{{/unless}}
