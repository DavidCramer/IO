<div class="cf-io-main-headercaldera">
		<h1>
		<span>
			{{#each forms}}
				{{#is ../form value=id}}
					<a class="cf-io-header-link" target="_blank" href="<?php echo admin_url( 'admin.php?page=caldera-forms&edit=' ); ?>{{id}}">{{name}}</a> / 
				{{/is}}
			{{/each}}
			<span id="cf_io-name-title">{{name}}</span>
		</span>
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
<div class="cf-io-sub-headercaldera">
	<ul class="cf-io-sub-tabs cf-io-nav-tabs">


		<li class="{{#is _current_tab value="#cf-io-panel-general"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-general">
				<?php _e('General', 'cf-io') ; ?>
			</a>
		</li>

		<li class="{{#is _current_tab value="#cf-io-panel-actions"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-actions">
				<?php _e('Actions', 'cf-io') ; ?>
			</a>
		</li>

		<li class="{{#is _current_tab value="#cf-io-panel-fields"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-fields">
				<?php _e('Fields', 'cf-io') ; ?>
			</a>
		</li>		

		<li class="{{#is _current_tab value="#cf-io-panel-filters"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-filters">
				<?php _e('Filters', 'cf-io') ; ?>
			</a>
		</li>		


		<li class="{{#is _current_tab value="#cf-io-panel-table"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-table">
				<?php _e('Table', 'cf-io') ; ?>
			</a>
		</li>		

		<li class="{{#is _current_tab value="#cf-io-panel-display"}}active {{/is}}cf-io-nav-tab">
			<a href="#cf-io-panel-display">
				<?php _e('Display', 'cf-io') ; ?>
			</a>
		</li>

	</ul>

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

	<div id="cf-io-panel-actions" class="cf-io-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#cf-io-panel-actions"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'Interface Actions & Permissions', 'cf-io' ); ?>
			<small class="description">
				<?php _e( 'Actions', 'cf-io' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include actions settings template
			 */
			include CFIO_PATH . 'includes/templates/actions-panel.php';
		?>
	</div>
	<div id="cf-io-panel-filters" class="cf-io-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#cf-io-panel-filters"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'Pre-filter the interface results', 'cf-io' ); ?>
			<small class="description">
				<?php _e( 'Filters', 'cf-io' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include filters settings template
			 */
			include CFIO_PATH . 'includes/templates/filters-panel.php';
		?>
	</div>	
	<div id="cf-io-panel-table" class="cf-io-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#cf-io-panel-table"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'Setup Ordering and Entry Views', 'cf-io' ); ?>
			<small class="description">
				<?php _e( 'Views', 'cf-io' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include table settings template
			 */
			include CFIO_PATH . 'includes/templates/table-panel.php';
		?>
	</div>

	<div id="cf-io-panel-fields" class="cf-io-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#cf-io-panel-fields"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'Field Behavior ', 'cf-io' ); ?>
			<small class="description">
				<?php _e( 'Fields', 'cf-io' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include fields settings template
			 */
			include CFIO_PATH . 'includes/templates/fields-panel.php';
		?>
	</div>


	<div id="cf-io-panel-display" class="cf-io-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#cf-io-panel-display"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'Modal Display & Settings', 'cf-io' ); ?>
			<small class="description">
				<?php _e( 'Modals', 'cf-io' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include display settings template
			 */
			include CFIO_PATH . 'includes/templates/display-panel.php';
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
