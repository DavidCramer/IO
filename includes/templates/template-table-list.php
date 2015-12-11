
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
.add-new-h2.status_toggles-{{id}}:hover,
.add-new-h2.status_toggles-{{id}}.active,
#viewer-{{id}}_baldrickModal.baldrick-modal-wrap .navtabs > li.selected > a,
#newentry-{{form}}_baldrickModal.baldrick-modal-wrap .navtabs > li.selected > a {
  background: none repeat scroll 0 0 {{color}};
}
.add-new-h2.status_toggles-{{id}}.active,
.add-new-h2.status_toggles-{{id}}:hover{
	color: #fff;
}
</style>
<input name="toggle" value="{{toggle}}" type="hidden">
<div class="io-panel-wrapper">
<div style="" class="caldera-entry-exporter">

	{{#unless params/filters}}
	<label style="margin: 1px 12px 0px 0px;" class="button wp-baldrick" type="button" {{#if parent_id}}data-target="{{id}}"{{/if}} data-add-node="params.filters" data-node-default='{"type":"and"}'>Add Filter</label>
	{{else}}
	<label style="margin: 1px 12px 0px 0px;" class="button button-primary" data-remove-element="#filter-wrapper-{{id}}" type="button">Remove Filters</label>
	{{/unless}}
	<?php if( !empty( $can_capture ) ){ ?>
		<?php if( is_admin() ){ ?>
		{{#if parent_id}}
		<?php } ?>
			<button type="button" class="button cfajax-trigger"
				style="margin: 1px 12px 0px 0px;"
				data-request="<?php echo site_url( "/cf-api/" ); ?>{{form}}/" 
				data-load-element="#cf-io-save-indicator"
				data-modal="newentry-{{form}}"
				data-modal-title="<?php echo esc_attr( __('Add', 'cf-io') ) ; ?> {{@root/singular}}"
				data-method="get"
				data-modal-width="{{width}}"
				data-modal-height="auto"
				data-modal-element="div"
				data-callback="calders_forms_init_conditions"		
				data-io_parent="{{parent_id}}"
				data-io_modal="{{id}}"
				data-modal-buttons='Save {{@root/singular}}|{ "data-for" : "form.{{form}}" {{#if parent_id}},"data-io_parent" : "{{parent_id}}"{{/if}} {{#if relation_field}},"data-io_relation": "{{relation_field}}"{{/if}}{{#if relation_field_from}},"data-io_relation_from": "{{relation_field_from}}"{{/if}} }'
			>
				<?php _e('Add', 'cf-io') ; ?> {{@root/singular}}
			</button>
			<input type="hidden" name="params[parent]" value="{{parent_id}}">
			{{#if relation_field}}
			<input type="hidden" name="params[relation_field]" value="{{relation_field}}">
			{{/if}}			
		<?php if( is_admin() ){ ?>
		{{/if}}
		<?php } ?>
	<?php } ?>


	<span>
		Show <input type="number" class="screen-per-page" name="params[limit]" value="{{#if data/params/limit}}{{data/params/limit}}{{else}}20{{/if}}" id="cf-entries-list-items"> &nbsp;
		<?php if( !empty( $can_edit ) ){ ?>
		<select style="vertical-align: initial;" name="params[action]" id="cf_bulk_action">
			<option value="" selected="selected">Bulk Actions</option>
			{{#is data/params/status value="active"}}
			<option value="trash">Move to Trash</option>
			{{else}}
			<option value="active">Restore Selected</option>
			<option value="delete">Delete Permanently</option>
			{{/is}}

		</select>
		<?php } ?>
		{{#unless params/filters}}
		<button class="button cf-bulk-action wp-baldrick io-entry-loader io-entry-loader-{{form}}" type="button"
			
			data-page="1"
			data-load-element="#cf-io-save-indicator"
			data-status="active"
			data-io="{{id}}"
			data-target="#entry-trigger-{{id}}"
			data-action="io_browse_entries"
			data-active-class="disabled"
			data-before="cfio_get_filters_object"
			{{#unless data}}
			data-autoload="true"
			{{/unless}}
		>Apply</button>
		{{/unless}}
	</span>
	
</div>
<div class="filter-wrapper" id="filter-wrapper-{{id}}">
	{{#each params/filters}}
		{{> filter_query_<?php echo $cf_io['id']; ?>}}
	{{/each}}	
	{{#if params/filters}}
		<label class="button filter-add-and wp-baldrick" type="button" {{#if parent_id}}data-target="{{id}}"{{/if}} data-add-node="params.filters" data-node-default='{"type":"and"}'>And</label>
		<button class="button filter-add-and cf-bulk-action wp-baldrick io-entry-loader io-entry-loader-{{form}}" type="button"
			style="margin: 3px 3px 10px 12px;"
			data-page="1"
			data-load-element="#cf-io-save-indicator"
			data-status="active"
			data-io="{{id}}"
			data-target="#entry-trigger-{{id}}"
			data-action="io_browse_entries"
			data-active-class="disabled"
			data-before="cfio_get_filters_object"
			{{#unless data}}
			data-autoload="true"
			{{/unless}}
		>Apply</button>		
	{{/if}}	
</div>

<input type="hidden" name="params[sort]" value="{{#if data/params/sort}}{{data/params/sort}}{{else}}datestamp{{/if}}">
<input type="hidden" name="params[sort_order]" value="desc">
<table class="widefat fixed striped io-table-viewer" id="list-table-{{id}}">
	<thead>
		<tr>
			<?php if( !empty( $can_edit ) || !empty( $can_trash ) ){ ?>
			<th data-hide="phone,tablet" class="column-cb check-column" style="padding:11px 0 0 3px;">
				<label for="cb-select-all-1" class="screen-reader-text">Select</label>
				<input type="checkbox" class="io-bulkcheck">
			</th>		
			<?php } ?>
			{{#each fields}}
				{{#unless hide}}
				
				<th {{#is @root/toggle value=id}}data-toggle="true"{{else}}data-hide="{{#if phone}}phone{{/if}},{{#if tablet}}tablet{{/if}}"{{/is}} class="{{#if sort}}{{#is ../data/params/sort value=id}}sorted {{../data/params/sort_order}}{{else}}sortable desc{{/is}}{{/if}}" id="{{id}}" scope="col">
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
			<?php if( is_user_logged_in() && empty( $no_actions ) ){ ?>
			<th data-hide="phone,tablet"></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody id="the-list">
		{{#if data/entries}}
			{{#each data/entries}}
			<tr id="entry_row_{{id}}">
				<?php if( !empty( $can_edit ) ){ ?>
				<td scope="row">
					<input type="checkbox" value="{{id}}" name="params[entry][]" class="io-entrycheck" id="io-select-{{id}}">
				</td>	
				<?php } ?>
				{{#each ../fields}}
					{{#unless hide}}

					<td id="{{id}}" scope="col">
					{{#find .. id}} {{{this}}} {{/find}}
					</td>

					{{/unless}}
				{{/each}}
				<?php if( is_user_logged_in() && empty( $no_actions ) ){ ?>
				<td style="text-align: right; white-space: nowrap;">
				<?php if( !empty( $can_trash ) ){ ?>
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
				<?php } ?>
				<?php if( !empty( $can_edit ) ){ ?>
					{{#is status value="active"}}
					<button type="button" class="button button-small cfajax-trigger"
						data-request="<?php echo site_url( "/cf-api/" ); ?>{{../form}}/{{id}}/" 
						data-load-element="#cf-io-save-indicator"
						data-modal="newentry-{{../form}}"
						data-modal-title="<?php echo esc_attr( __('Edit', 'cf-io') ) ; ?> {{@root/singular}}"
						data-method="get"
						data-modal-width="{{../width}}"
						data-callback="calders_forms_init_conditions"
						data-modal-height="auto"
						data-modal-element="div"
						data-static="true"
						data-io_modal="{{../id}}"
						data-modal-buttons='Save Changes|{ "data-for" : "form.{{../form}}"{{#if ../parent_id}},"data-io_parent" : "{{../parent_id}}"{{/if}} {{#if ../relation_field}},"data-io_relation": "{{../relation_field}}"{{/if}} }'						
					>Edit</button>
					{{/is}}
				<?php } ?>
				<?php if( !empty( $can_view ) ){ ?>
					<button type="button" class="button button-small wp-baldrick"
						data-action="get_entry"
						data-io="{{../id}}"
						data-form="{{../form}}"
						data-entry="{{id}}"
						data-load-element="#cf-io-save-indicator"
						data-modal="viewer-{{../id}}"
						data-modal-title="{{#unless ../title_prefix}}View{{else}}{{../title_prefix}}{{#each ../title}}{{#if show}}{{#find .. id}}{{this}}{{/find}}{{/if}} {{/each}}{{/unless}}"
						data-modal-width="1280"
						data-modal-height="850"
						data-modal-element="div"
						data-template="#io-viewer-template"
						data-static="true"
						sdata-modal-buttons='Save Changes|{ "data-for" : "form.{{../form}}" }'
					>View</button>
				<?php } ?>
				</td>
				<?php } ?>
			</tr>
			{{/each}}
		{{else}}

		{{/if}}
	</tbody>
</table>
{{#unless data}}

	<div style="text-align: center; border: 1px solid rgb(223, 223, 223); padding: 20px; background: rgb(245, 245, 245) none repeat scroll 0% 0%;">Fetching {{plural}}</div>

{{else}}
	{{#unless data/entries}}

		<div style="text-align: center; border: 1px solid rgb(223, 223, 223); padding: 20px; background: rgb(245, 245, 245) none repeat scroll 0% 0%;">No {{plural}} found</div>

	{{/unless}}
{{/unless}}

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
	$('#list-table-{{id}}').footable();	
});
{{/script}}