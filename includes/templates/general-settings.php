<style type="text/css">

#newentry_baldrickModal .baldrick-modal-title .modal-label{
	background-color: {{color}};
}
#newentry_baldrickModal .cf-io-modal-title > h3 {
	background-color: {{color}};
}
#newentry_baldrickModal.baldrick-modal-wrap .navtabs > li a {
  color: {{color}};
}
#newentry_baldrickModal.baldrick-modal-wrap .navtabs > li.selected > a {
  background: none repeat scroll 0 0 {{color}};
}
</style>

		<div class="cf-io-config-group">
			<label for="cf_io-name">
				<?php _e( 'Interface Name', 'cf-io' ); ?>
			</label>
			<input type="text" name="name" value="{{name}}" data-sync="#cf_io-name-title" id="cf_io-name" required>
		</div>
		<div class="cf-io-config-group">
			<label for="cf_io-slug">
				<?php _e( 'Interface Slug', 'cf-io' ); ?>
			</label>
			<input type="text" name="slug" value="{{slug}}" data-format="slug" data-sync=".cf-io-subline" data-master="#cf_io-name" id="cf_io-slug" required>
		</div>
		<div class="cf-io-config-group">
			<label for="cf-io-form-form">
				<?php _e( 'Interface Form', 'cf-io' ); ?>
			</label>

			<select style="width:395px;" placeholder="<?php _e( 'Form to use as the Interface Base', 'cf-io' ); ?>" name="form" data-live-sync="true" id="cf-io-form-form" >
				<option value=""></option>
				{{#each forms}}
				<option value="{{id}}" {{#is ../form value=id}}selected="selected"{{/is}}>{{name}}</option>
				{{/each}}
			</select>

			<p class="description" style="margin-left: 190px;">Form to use as the Interface Base</p>
		</div>

		{{#script}}
			jQuery( function($){
			//	$("#cf-io-form-form").select2();
			});
		{{/script}}

		
		<div class="cf-io-config-group">
			<label for="cf_io-lock_form">
				<?php _e( 'Lock Form', 'cf-io' ); ?>
			</label>
			<label style="width: auto; margin: 0px;"><input type="checkbox" name="lock_form" value="1" {{#if lock_form}}checked="checked"{{/if}} id="cf_io-lock_form"> <?php _e('Remove form form Caldera Forms admin','cf-io'); ?></label>

		</div>

  		<div class="cf-io-config-group">
			<label for="cf_io-entry_tab">
				<?php _e( 'Entry Tab Text', 'cf-io' ); ?>
			</label>
			<input type="text" name="entry_tab" value="{{#if entry_tab}}{{entry_tab}}{{else}}Entry{{/if}}" id="cf_io-entry_tab" class="entry_tab-field">
		</div>		

  		<div class="cf-io-config-group">
			<label for="cf_io-location">
				<?php _e( 'Menu Location', 'cf-io' ); ?>
			</label>
			<select style="width:395px;" name="location" data-live-sync="true" id="cf-io-form-form">
				<option></option>
				<option value="primary" {{#is location value="primary"}}selected="selected"{{/is}}>Primary</option>
				<option value="child" {{#is location value="child"}}selected="selected"{{/is}}>Child</option>
				<option value="relation" {{#is location value="relation"}}selected="selected"{{/is}}>Relation</option>
			</select>

		</div>


		{{#is location value="primary"}}
  		<div class="cf-io-config-group">
			<label for="cf_io-priority">
				<?php _e( 'Menu Priority', 'cf-io' ); ?>
			</label>
			<input type="number" style="width:60px;" name="priority" value="{{#if priority}}{{priority}}{{else}}25{{/if}}" id="cf_io-priority" class="priority-field">
		</div>
  		<div class="cf-io-config-group">
			<label for="cf_io-icon">
				<?php _e( 'Menu Icon', 'cf-io' ); ?>
			</label>
			<input name="icon" value="{{#if icon}}{{icon}}{{else}}dashicons-admin-generic{{/if}}" id="cf_io-icon" class="icon-field" type="text" />
			<input class="button dashicons-picker" type="button" value="Choose Icon" data-target="#cf_io-icon" />
			{{#script}}
			jQuery( function( $ ) {

				$( function () {
					$( '.dashicons-picker' ).dashiconsPicker();
				} );	

			});
			{{/script}}			
		</div>

		{{/is}}
		{{#is location value="child"}}
  		<div class="cf-io-config-group">
			<label for="cf_io-parent">
				<?php _e( 'Menu Parent', 'cf-io' ); ?>
			</label>
			<input type="text" name="parent" value="{{#if parent}}{{parent}}{{else}}tools.php{{/if}}" id="cf_io-parent" class="parent-field">
		</div>
		{{/is}}
		{{#is location value="relation"}}
  		<div class="cf-io-config-group">
			<label for="cf_io-relation">
				<?php _e( 'Relation Interface', 'cf-io' ); ?>
			</label>
	
			<select style="width:395px;" name="relation" data-live-sync="true" id="cf-io-form-relation">
				<option></option>
				<?php 
					foreach ($cf_ios as $io_id => $io_config) {
						if( $io_id === $cf_io['id'] || empty( $io_config['form'] ) ){
							continue;
						}
						?>
						{{#is form value="<?php echo $io_config['form']; ?>"}}
						<option value="" disabled="disabled"><?php echo $io_config['name']; ?></option>
						{{else}}
						<option value="<?php echo $io_id; ?>" {{#is relation value="<?php echo $io_id; ?>"}}selected="selected"{{/is}}><?php echo $io_config['name']; ?></option>
						{{/is}}
						<?php
					}
				?>				
			</select>

		</div>
			{{#if relation}}
	  		<div class="cf-io-config-group">
				<label for="cf_io-relation_field">
					<?php _e( 'Relation Field Connection', 'cf-io' ); ?>
				</label>
		
				
				<?php _e( 'From', 'cf-io' ); ?>: 

				<select name="relation_field_from" id="cf-io-form-relation_field_from">
					<option value="_entry_id"><?php _e( 'Entry ID', 'cf-io' ); ?>: </option>
					<?php 
						foreach ($cf_ios as $io_id => $io_config) {
							if( $io_id === $cf_io['id'] ){
								continue;
							}
							foreach( $io_config['fields'] as $field_id => $field ){
								if( empty( $field['type'] ) ){
									continue;
								}
								?>
								<option value="<?php echo $field_id; ?>" {{#is relation_field_from value="<?php echo $field_id; ?>"}}selected="selected"{{/is}}><?php echo $field['name']; ?> [<?php echo $field['type']; ?>]</option>
								<?php
							}
						}
					?>
				</select>
				
				<?php _e( 'To', 'cf-io' ); ?>: 
				<select name="relation_field" id="cf-io-form-relation_field">
					<option value="_io_parent">- Internal</option>
					{{#each fields}}
						{{#if type}}
						<option value="{{id}}" {{#is ../relation_field value=id}}selected="selected"{{/is}}>{{name}} [{{type}}]</option>
						{{/if}}
					{{/each}}

				</select>			

			</div>
			{{/if}}
		{{/is}}
 
  		<div class="cf-io-config-group">
			<label for="cf_io-color">
				<?php _e( 'Base Color', 'cf-io' ); ?>
			</label>
			<input type="text" name="color" value="{{#if color}}{{color}}{{else}}#e8a200{{/if}}" id="cf_io-color" class="color-field">
		</div>



 		<div class="cf-io-config-group">
			<label for="cf_io-width">
				<?php _e( 'Modal Width', 'cf-io' ); ?>
			</label>
			<input type="number" data-live-sync="true" name="width" value="{{#if width}}{{width}}{{else}}580{{/if}}" id="cf_io-width" style="width:80px;"> px
		</div>

 		<div class="cf-io-config-group">
			<label for="cf_io-test">
				<?php _e( 'Test Modal', 'cf-io' ); ?>
			</label>
			<button type="button" class="button cfajax-trigger"
			data-request="<?php echo site_url( "/cf-api/" ); ?>{{form}}/" 
			data-load-element="#cf-io-save-indicator"
			data-modal="newentry"
			data-modal-title="<?php echo esc_attr( __('Add Entry', 'cf-io') ) ; ?>"
			data-method="get"
			data-modal-width="{{width}}"
			data-modal-height="auto"
			data-modal-element="div"
			data-modal-buttons='Close|dismiss'
			data-callback="calders_forms_init_conditions"
		>
			<?php _e('Open Modal', 'cf-io') ; ?>
		</button>

		</div>		

		
{{#script}}
jQuery( function( $ ){
	$('.cfajax-trigger').baldrick();
});
{{/script}}		