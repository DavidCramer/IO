<?php
/**
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer <david@digilab.co.za>
 */
?>

<table class="widefat fixed striped io-fields">
	<thead>
		<tr class="cf-io-sort">
			<th>
				<?php _e('Field', 'cf-io'); ?>
			</th>
			<th style="width: 137px; text-align: center;">
				<?php _e('Visible', 'cf-io'); ?>
			</th>
			<th style="width: 137px; text-align: center;">
				<?php _e('Sortable', 'cf-io'); ?>
			</th>
			<th style="width: 137px; text-align: center;">
				<?php _e('Modal Title', 'cf-io'); ?>
			</th>
			<th style="width: 137px; text-align: center;">
				<?php _e('Responsive Toggle', 'cf-io'); ?>
			</th>
			<th style="width: 137px; text-align: center;">
				<?php _e('Mobile', 'cf-io'); ?>
			</th>					
			<th style="width: 137px; text-align: center;">
				<?php _e('Tablet', 'cf-io'); ?>
			</th>				
					
	
		</tr>
	</thead>
	<tbody>
		{{#find forms form}}

			{{#each fields}}
				<tr>
					<td>
						<input type="hidden" name="fields[{{ID}}][id]" value="{{ID}}">
						<input type="hidden" name="fields[{{ID}}][name]" value="{{label}}">
						<input type="hidden" name="fields[{{ID}}][filter]" value="">
						<input type="hidden" name="fields[{{ID}}][hide]" value="">
						{{#if type}}
						<input type="hidden" name="fields[{{ID}}][type]" value="{{type}}">
						{{/if}}
						{{label}} <small style="color:{{#is @root/_open_field value=ID}}rgba(255,255,255,.7){{else}}#8f8f8f{{/is}};">{{type}} - {{ID}}</small>
					</td>
					{{#find @root/fields ID}}					
					<td style="width: 137px; text-align: center;">
						<label class="dashicons dashicons-yes" style="{{#if hide}}opacity:0.2;{{/if}};"><input type="checkbox" name="fields[{{@key}}][hide]" value="1" data-live-sync="true" style="display:none;" {{#if hide}}checked="checked"{{/if}}></label>						
					</td>
					{{#unless hide}}
					<td style="width: 137px; text-align: center;">
						<label class="dashicons dashicons-yes" style="{{#unless sort}}opacity:0.2;{{/unless}};"><input type="checkbox" name="fields[{{@key}}][sort]" value="1" data-live-sync="true" style="display:none;" {{#if sort}}checked="checked"{{/if}}></label>								
					</td>
					<td style="width: 137px; text-align: center;">
						<label class="dashicons dashicons-yes" style="{{#unless in_title}}opacity:0.2;{{/unless}};"><input type="checkbox" name="fields[{{@key}}][in_title]" value="1" data-live-sync="true" style="display:none;" {{#if in_title}}checked="checked"{{/if}}></label>
					</td>
					<td style="width: 137px; text-align: center;">
						<label class="dashicons dashicons-yes" style="{{#is @root/toggle value=@key}}{{else}}opacity:0.2;{{/is}};"><input type="radio" name="toggle" value="{{@key}}" data-live-sync="true" style="display:none;" {{#is @root/toggle value=@key}}checked="checked"{{/is}}></label>
					</td>
					{{#is @root/toggle value=@key}}
						<td colspan="2"></td>
					{{else}}
					<td style="width: 137px; text-align: center;">
						<label class="dashicons dashicons-yes" style="{{#if phone}}opacity:0.2;{{/if}};"><input type="checkbox" name="fields[{{@key}}][phone]" value="1" data-live-sync="true" style="display:none;" {{#if phone}}checked="checked"{{/if}}></label>
					</td>
					<td style="width: 137px; text-align: center;">
						<label class="dashicons dashicons-yes" style="{{#if tablet}}opacity:0.2;{{/if}};"><input type="checkbox" name="fields[{{@key}}][tablet]" value="1" data-live-sync="true" style="display:none;" {{#if tablet}}checked="checked"{{/if}}></label>
					</td>
					{{/is}}
					{{else}}
						<td colspan="5"></td>
					{{/unless}}
					{{/find}}
				</tr>
			{{/each}}

		{{/find}}

	</tbody>
</table>