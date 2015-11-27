<?php
/**
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer <david@digilab.co.za>
 */
?>

<div class="cf-io-fixed-center" style="width: auto; margin-right: 30px;">
	<div class="postbox">
		<h3 style="font-size: 14px;line-height: 1.4;margin: 0;padding: 8px 9pt; border-bottom: 1px solid #eee;" >
			<span><?php _e( 'Ordering', 'cf-io' ); ?></span>
		</h3>
		<div class="cf-io-sort" style="overflow: auto; padding-bottom: 20px;">
		{{#each @root/fields}}
			<span class="cf-io-autocomplete-out-entry-mustache" data-slug="{{id}}" data-label="{{name}}"></span>
			{{#if name}}
				{{#unless hide}}
				<div style="padding: 12px; border-bottom: 1px solid rgb(239, 239, 239);cursor:move;display:inline-block;background:#fff;white-space:nowrap;">
					<input type="hidden" name="fields[{{@key}}][id]" value="{{@key}}">
					{{name}}
				</div>
				{{/unless}}
			{{/if}}
		{{/each}}
		</div>
	</div>

	<?php /* ?>
	<div class="cf-io-config-group">
		<label for="cf_io-custom_template">
			<?php _e( 'Custom Template', 'cf-io' ); ?>
		</label>
		<input type="checkbox" style="margin: 7px 0px 0px;" name="custom_template" data-live-sync="true" value="1" {{#if custom_template}}checked="checked"{{/if}} id="cf_io-custom_template">
	</div>
	<?php */ ?>
	<div class="postbox">
		<h3 style="font-size: 14px;line-height: 1.4;margin: 0;padding: 8px 9pt; border-bottom: 1px solid #eee;" >
			<span><?php _e( 'View Modal Title', 'cf-io' ); ?></span>
		</h3>
		<div style="padding:6px;">
			<input type="text" style="margin: 7px 0px 0px;" name="title_prefix" value="{{#if title_prefix}}{{title_prefix}}{{else}}View - {{/if}}" id="cf_io-title_prefix">
		</div>
		<div class="cf-io-sort" style="overflow: auto; padding-bottom: 20px;">
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
	{{#each @root/fields}}
		{{#unless hide}}
			<input type="hidden" name="title[{{@key}}][id]" value="{{@key}}">			
		{{/unless}}
	{{/each}}		

</div>
{{#find forms form}}
	<div class="cf-io-fixed-list">
		<div class="postbox">
				<h3 style="font-size: 14px;line-height: 1.4;margin: 0;padding: 8px 9pt; border-bottom: 1px solid #eee;" >
					<span><?php _e( 'Fields', 'cf-io' ); ?></span>
				</h3>
				<div>
					{{#each fields}}
						<div>
							<div style="border-bottom: 1px solid rgb(239, 239, 239);">
								<input type="hidden" name="fields[{{ID}}][id]" value="{{ID}}">
								<input type="hidden" name="fields[{{ID}}][name]" value="{{label}}">
								<input type="hidden" name="fields[{{ID}}][filter]" value="">
								<input type="hidden" name="fields[{{ID}}][hide]" value="">
								{{#if type}}
								<input type="hidden" name="fields[{{ID}}][type]" value="{{type}}">
								{{/if}}
								<span style="padding: 11px;text-transform: capitalize;display: inline-block;">
									{{label}} <small style="color:{{#is @root/_open_field value=ID}}rgba(255,255,255,.7){{else}}#8f8f8f{{/is}};">{{type}} - {{ID}}</small>
								</span>
								{{#find @root/fields ID}}
								<label class="dashicons dashicons-filter" style="float: right; margin: 7px;{{#unless filter}}opacity:0.4;{{/unless}};"><input type="checkbox" name="fields[{{@key}}][filter]" value="1" data-live-sync="true" style="display:none;" {{#if filter}}checked="checked"{{/if}}></label>
								<label class="dashicons dashicons-visibility" style="float: right; margin: 7px;{{#if hide}}opacity:0.4;{{/if}};"><input type="checkbox" name="fields[{{@key}}][hide]" value="1" data-live-sync="true" style="display:none;" {{#if hide}}checked="checked"{{/if}}></label>
								<label class="dashicons dashicons-sort" style="float: right; margin: 7px;{{#unless sort}}opacity:0.4;{{/unless}};"><input type="checkbox" name="fields[{{@key}}][sort]" value="1" data-live-sync="true" style="display:none;" {{#if sort}}checked="checked"{{/if}}></label>
								{{#unless hide}}
								<label class="dashicons dashicons-minus" style="float: right; margin: 7px;{{#unless in_title}}opacity:0.4;{{/unless}};"><input type="checkbox" name="fields[{{@key}}][in_title]" value="1" data-live-sync="true" style="display:none;" {{#if in_title}}checked="checked"{{/if}}></label>
								{{/unless}}

								{{/find}}
							</div>
						</div>
					{{/each}}
				</div>
			</div>
	</div>

{{/find}}
