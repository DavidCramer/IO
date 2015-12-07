<?php
/**
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer <david@digilab.co.za>
 */
?>


{{#find forms form}}
	<div class="cf-io-fixed-list">
		<div class="postbox">
				<h3 style="font-size: 14px;line-height: 1.4;margin: 0;padding: 8px 9pt; border-bottom: 1px solid #eee;" >
					<span><?php _e( 'Fields', 'cf-io' ); ?></span>
				</h3>
				<div>
					{{#each fields}}
						<div style="clear:both;">
							<div style="border-bottom: 1px solid rgb(239, 239, 239);">
								<input type="hidden" name="fields[{{ID}}][id]" value="{{ID}}">
								<input type="hidden" name="fields[{{ID}}][name]" value="{{label}}">
								<input type="hidden" name="fields[{{ID}}][filter]" value="">
								<input type="hidden" name="fields[{{ID}}][hide]" value="">
								{{#if type}}
								<input type="hidden" name="fields[{{ID}}][type]" value="{{type}}">
								{{/if}}
								<div style="padding: 11px;text-transform: capitalize;">
									{{label}} <small style="color:{{#is @root/_open_field value=ID}}rgba(255,255,255,.7){{else}}#8f8f8f{{/is}};">{{type}} - {{ID}}</small>
								</div>
								<div style="margin: -4px 0px 6px 10px;">
									{{#find @root/fields ID}}								
									<label class="dashicons dashicons-visibility" style="{{#if hide}}opacity:0.4;{{/if}};"><input type="checkbox" name="fields[{{@key}}][hide]" value="1" data-live-sync="true" style="display:none;" {{#if hide}}checked="checked"{{/if}}></label>
									<label class="dashicons dashicons-sort" style="{{#unless sort}}opacity:0.4;{{/unless}};"><input type="checkbox" name="fields[{{@key}}][sort]" value="1" data-live-sync="true" style="display:none;" {{#if sort}}checked="checked"{{/if}}></label>
									{{#unless hide}}
									<label class="dashicons dashicons-minus" style="{{#unless in_title}}opacity:0.4;{{/unless}};"><input type="checkbox" name="fields[{{@key}}][in_title]" value="1" data-live-sync="true" style="display:none;" {{#if in_title}}checked="checked"{{/if}}></label>
										
										<label class="dashicons dashicons-menu" style="{{#is @root/toggle value=@key}}{{else}}opacity:0.4;{{/is}};"><input type="radio" name="toggle" value="{{@key}}" data-live-sync="true" style="display:none;" {{#is @root/toggle value=@key}}checked="checked"{{/is}}></label>
										{{#is @root/toggle value=@key}}
										{{else}}
										<label class="dashicons dashicons-smartphone" style="{{#if phone}}opacity:0.4;{{/if}};"><input type="checkbox" name="fields[{{@key}}][phone]" value="1" data-live-sync="true" style="display:none;" {{#if phone}}checked="checked"{{/if}}></label>
										<label class="dashicons dashicons-tablet" style="{{#if tablet}}opacity:0.4;{{/if}};"><input type="checkbox" name="fields[{{@key}}][tablet]" value="1" data-live-sync="true" style="display:none;" {{#if tablet}}checked="checked"{{/if}}></label>
										{{/is}}
									{{/unless}}
									{{/find}}
								</div>
							</div>
						</div>
					{{/each}}
				</div>
			</div>
	</div>

{{/find}}
