<style type="text/css">

#viewer-{{id}}_baldrickModal .baldrick-modal-title .modal-label,
#newentry-{{form}}_baldrickModal .baldrick-modal-title .modal-label{
	background-color: {{color}};
}
#viewer-{{id}}_baldrickModal .cf-io-modal-title > h3,
#newentry-{{form}}_baldrickModal .cf-io-modal-title > h3 {
	background-color: {{color}};
}
#viewer-{{id}}_baldrickModal.baldrick-modal-wrap .navtabs > li a,
#newentry-{{form}}_baldrickModal.baldrick-modal-wrap .navtabs > li a {
  color: {{color}};
}
#viewer-{{id}}_baldrickModal.baldrick-modal-wrap .navtabs > li.selected > a,
#newentry-{{form}}_baldrickModal.baldrick-modal-wrap .navtabs > li.selected > a {
  background: none repeat scroll 0 0 {{color}};
}
</style>
<div class="io-panel-wrapper">
<div style="" class="caldera-entry-exporter">
	{{#if parent_id}}
		<button type="button" class="button cfajax-trigger"
			style="margin: 1px 12px 0px 0px;"
			data-request="<?php echo site_url( "/cf-api/" ); ?>{{form}}/" 
			data-load-element="#cf-io-save-indicator"
			data-modal="newentry-{{form}}"
			data-modal-title="<?php echo esc_attr( __('Add Entry', 'cf-io') ) ; ?>"
			data-method="get"
			data-modal-width="{{width}}"
			data-modal-height="auto"
			data-modal-element="div"
			data-callback="calders_forms_init_conditions"		
			data-io_parent="{{parent_id}}"
			data-io_modal="{{id}}"
			data-modal-buttons='Save Entry|{ "data-for" : "form.{{form}}" {{#if parent_id}},"data-io_parent" : "{{parent_id}}"{{/if}} {{#if relation_field}},"data-io_relation": "{{relation_field}}"{{/if}} }'
		>
			<?php _e('Add Entry', 'cf-io') ; ?>
		</button>
		<input type="hidden" name="params[parent]" value="{{parent_id}}">
		{{#if relation_field}}
		<input type="hidden" name="params[relation_field]" value="{{relation_field}}">
		{{/if}}			
	{{/if}}
	<span style="" class="toggle_option_preview">
		<label style="margin-top: 1px;" class="status_toggles button {{#is data/params/status value="active"}}button-primary{{/is}}" type="button" >Active <input style="display:none;" type="radio" value="active" name="params[status]" {{#is data/params/status value="active"}}checked="checked"{{/is}}> <span class="current-status-count">{{data/status_totals/active/total}}</span></label>
		<label style="margin-top: 1px; margin-right: 10px;" class="status_toggles button {{#is data/params/status value="trash"}}button-primary{{/is}}" type="button" >Trash  <input style="display:none;" type="radio" value="trash" name="params[status]" {{#is data/params/status value="trash"}}checked="checked"{{/is}}> <span class="current-status-count">{{data/status_totals/trash/total}}</span></label>
	</span>

	<span>
		Show <input type="number" class="screen-per-page" name="params[limit]" value="{{#if data/params/limit}}{{data/params/limit}}{{else}}20{{/if}}" id="cf-entries-list-items"> &nbsp;
		<select style="vertical-align: initial;" name="params[action]" id="cf_bulk_action">
			<option value="" selected="selected">Bulk Actions</option>
			<option value="export">Export Selected</option>
			<option value="trash">Move to Trash</option>
		</select>

		<button class="button cf-bulk-action wp-baldrick io-entry-loader io-entry-loader-{{form}}" type="button"
			
			data-page="1"
			data-load-element="#cf-io-save-indicator"
			data-fields="{{#each fields}}{{#unless hide}}{{#if type}}{{@key}},{{/if}}{{/unless}}{{/each}}"
			data-status="active"
			data-form="{{form}}"
			data-target="#entry-trigger-{{id}}"
			data-action="io_browse_entries"
			data-active-class="disabled"
			data-before="cfio_get_filters_object"
			{{#unless data}}
			data-autoload="true"
			{{/unless}}
		>Apply</button>

	</span>
	<span style="margin-left: 45px;"><input placeholder="<?php echo esc_attr( __('Search', 'cf-io' ) ); ?>" type="search" name="params[key_word]" value="{{data/params/key_word}}"></span>
	
	{{#if data/entries}}
	<div class="tablenav caldera-table-nav" style="display:inline;">

		<div class="tablenav-pages">
			<span class="displaying-num">{{data/total}} {{#is data/total value="1"}}item{{else}}items{{/is}}</span>
			<span class="pagination-links" style="display: inline;">
				<a class="first-page pagination" data-page="1" title="Go to the first page" href="#first">«</a>
				<a class="prev-page pagination" data-page="prev" title="Go to the previous page" href="#prev">‹</a>
				<span class="paging-input"><input type="number" class="current-page wp-baldrick" data-for="#io-entry-loader" data-event="change" title="Current page" name="params[page]" style="width:60px;" value="{{#if data/params/page}}{{data/params/page}}{{else}}1{{/if}}"> of <span class="total-pages">{{data/pages}}</span></span>
				<a class="next-page pagination" data-page="next" title="Go to the next page" href="#next">›</a>
				<a class="last-page pagination" data-page="{{data/pages}}" title="Go to the last page" href="#last">»</a>
			</span>
		</div>
	</div>
	{{/if}}
</div>

<hr>
<input type="hidden" name="params[sort]" value="{{#if data/params/sort}}{{data/params/sort}}{{else}}datestamp{{/if}}">
<input type="hidden" name="params[sort_order]" value="desc">
<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr>
			<td class="manage-column column-cb check-column" id="cb">
				<label for="cb-select-all-1" class="screen-reader-text">Select All</label>
				<input type="checkbox" id="cb-select-all-1">
			</td>		
			{{#each fields}}
				{{#unless hide}}

				<th class="manage-column {{#if sort}}{{#is ../data/params/sort value=id}}sorted {{../data/params/sort_order}}{{else}}sortable desc{{/is}}{{/if}}" id="{{id}}" scope="col">
					<label style="display:inline-flex;{{#unless sort}}cursor:default;{{/unless}}">
						<span>
						{{name}}
						</span>
						{{#if sort}}
							<span class="sorting-indicator"></span>
							{{#is ../data/params/sort value=id}}
								<input style="display:none;" type="checkbox" name="params[sort_order]" value="asc" {{#is ../data/params/sort_order value="asc"}}checked="checked"{{/is}}>
							{{else}}
								<input style="display:none;" type="radio" name="params[sort]" value="{{id}}">
							{{/is}}
						{{/if}}
					</label>
				</th>
				{{/unless}}
			{{/each}}
			<th></th>
		</tr>
	</thead>
	<tbody id="the-list">
		{{#if data/entries}}
			{{#each data/entries}}
			<tr id="entry_row_{{id}}">
				<th class="check-column" scope="row">
					<input type="checkbox" value="{{id}}" name="post[]" id="cb-select-{{id}}">
				</th>	

				{{#each ../fields}}
					{{#unless hide}}

					<td class="manage-column column-title column-primary" id="{{id}}" scope="col">
					{{#find .. id}} {{{this}}} {{/find}}
					</th>

					{{/unless}}
				{{/each}}

				<td style="text-align: right; white-space: nowrap;">

					<button 
					data-items="{{id}}" 
					data-action="cf_bulk_action" 
					data-load-element="#entry_row_{{id}}"
					data-active-class="disabled" 
					data-form="{{../form}}" 
					data-panel="active"
					data-callback="cfio_refresh_list"	
					{{#is status value="trash"}}
					data-do="active" 
					{{else}}
					data-do="trash" 
					{{/is}}
					data-load-class="active" class="button button-small wp-baldrick" type="button">
					{{#is status value="trash"}}
					Restore
					{{else}}
					Trash
					{{/is}}
					</button>

					{{#if ../editing}}
					{{#is status value="active"}}
					<button type="button" class="button button-small cfajax-trigger"
						data-request="<?php echo site_url( "/cf-api/" ); ?>{{../form}}/{{id}}/" 
						data-load-element="#cf-io-save-indicator"
						data-modal="newentry-{{../form}}"
						data-modal-title="<?php echo esc_attr( __('Edit Entry', 'cf-io') ) ; ?>"
						data-method="get"
						data-modal-width="{{../width}}"
						data-modal-height="auto"
						data-modal-element="div"
						data-static="true"
						data-io_modal="{{../id}}"
						data-modal-buttons='Save Changes|{ "data-for" : "form.{{../form}}"{{#if ../parent_id}},"data-io_parent" : "{{../parent_id}}"{{/if}} {{#if ../relation_field}},"data-io_relation": "{{../relation_field}}"{{/if}} }'						
					>Edit</button>
					{{/is}}
					{{/if}}

					<button type="button" class="button button-small wp-baldrick"
						data-action="get_entry"
						data-io="{{../id}}"
						data-form="{{../form}}"
						data-entry="{{id}}"
						data-load-element="#cf-io-save-indicator"
						data-modal="viewer-{{../id}}"
						data-modal-title="{{../title_prefix}}{{#each ../title}}{{#if show}}{{#find .. id}}{{this}}{{/find}}{{/if}} {{/each}}"
						data-modal-width="1280"
						data-modal-height="850"
						data-modal-element="div"
						data-template="#io-viewer-template"
						data-static="true"
						sdata-modal-buttons='Save Changes|{ "data-for" : "form.{{../form}}" }'
					>View</button>					

				</td>
			</tr>
			{{/each}}
		{{else}}

		{{/if}}
	</tbody>
</table>
{{#unless data}}

	<div style="text-align: center; border: 1px solid rgb(223, 223, 223); padding: 20px; background: rgb(245, 245, 245) none repeat scroll 0% 0%;">Fetching Entries</div>

{{else}}
	{{#unless data/entries}}

		<div style="text-align: center; border: 1px solid rgb(223, 223, 223); padding: 20px; background: rgb(245, 245, 245) none repeat scroll 0% 0%;">No results found</div>

	{{/unless}}
{{/unless}}
<div>

{{#script}}
jQuery( function( $ ){
	$('.cfajax-trigger').baldrick({
		before			: function(el, ev){

			var form	=	$(el),
				buttons = 	form.find(':submit');
			if( !form.is('form') ){
				return;
			}
			ev.preventDefault();

			var validate = form.parsley({
				errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
				errorTemplate : '<span></span>'
			});

			if( !validate.isValid() ){
				$(window).trigger('resize');
				return false;
			}

		},
		callback : function(){
			$('form.{{form}}').parsley({
				errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
				errorTemplate : '<span></span>'
			});
		}
	});
});
{{/script}}