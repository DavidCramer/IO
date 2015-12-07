  		<div class="cf-io-config-group">
			<label for="cf_io-color">
				<?php _e( 'Base Color', 'cf-io' ); ?>
			</label>
			<input type="text" name="color" data-target=".cf-io-modal-title" data-style="background-color" value="{{#if color}}{{color}}{{else}}#a3be5f{{/if}}" id="cf_io-color" class="color-field">
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
			data-before="cfio_record_change"
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


	{{#if view}}
	<div class="postbox" style="margin-top:24px">
		<h3 style="font-size: 14px;line-height: 1.4;margin: 0;padding: 8px 9pt; border-bottom: 1px solid #eee;" >
			<span><?php _e( 'View Modal Title', 'cf-io' ); ?></span>
		</h3>
		<div style="padding:6px;">
			<input type="text" data-live-sync="true" data-sync=".cf-io-modal-title" style="margin: 0px; width: 100px;" name="title_prefix" value="{{#if title_prefix}}{{title_prefix}}{{else}}View {{/if}}" id="cf_io-title_prefix">		
			<div class="cf-io-sort" style="overflow: auto; display: inline-block; padding-bottom: 0px; margin: 0px 0px -13px;">
			{{#each @root/title}}
				{{#find @root/fields id}}
					{{#if in_title}}
					<div style="padding: 12px; border-bottom: 1px solid rgb(239, 239, 239);cursor:move;display:inline-block;background:#fff;white-space:nowrap;">						
						<input type="hidden" name="title[{{@key}}][id]" value="{{@key}}">
						<input type="hidden" name="title[{{@key}}][show]" value="1">
					{{name}}
					</div>
					{{/if}}
				{{/find}}
			{{/each}}
			</div>	
		</div>
	</div>
	{{#each @root/fields}}
		{{#unless hide}}
			<input type="hidden" name="title[{{@key}}][id]" value="{{@key}}">
		{{/unless}}
	{{/each}}		


	{{#is _current_tab value="#cf-io-panel-display"}}
	<div id="preview_newentry_baldrickModal" style="width:{{width}}px;">
		<div class="baldrick-modal-title" style="display: block;">
			<a href="#close" class="baldrick-modal-closer">×</a>
			<h3 class="modal-label cf-io-modal-title">{{#if title_prefix}}{{title_prefix}}{{else}}View - {{/if}}
				{{#each @root/title}}
					{{#find @root/fields id}}
						{{#if in_title}}
							{{name}}
						{{/if}}
					{{/find}}
				{{/each}}
			</h3>
		</div>
		<div style="background: rgb(255, 255, 255) none repeat scroll 0% 0%; position: relative; top: 0px; height: 100px;" class="baldrick-modal-body">
			<p class="description">Modal Example</p>
		</div>
	</div>
	{{/is}}

	{{/if}}


		
{{#script}}
jQuery( function( $ ){
	$('.cfajax-trigger').baldrick();
});
{{/script}}