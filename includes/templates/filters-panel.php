	{{#unless @root/params/filters}}
	<button type="button" style="margin: 1px 12px 0px 0px;" class="button wp-baldrick" type="button" {{#if parent_id}}data-target="{{id}}"{{/if}} data-add-node="params.filters" data-node-default='{"type":"and"}'>Add Filter</button>
	{{/unless}}
	<div class="filter-wrapper">
		{{#each @root/params/filters}}
			{{> filter_query_<?php echo $cf_io['id']; ?>}}
		{{/each}}
		{{#if @root/params/filters}}
			<button type="button" style="text-transform: uppercase; font-weight: bold; color: rgb(143, 143, 143); margin: 6px 12px 12px;" class="button wp-baldrick" type="button" {{#if parent_id}}data-target="{{id}}"{{/if}} data-add-node="params.filters" data-node-default='{"type":"and"}'>And</button>
		{{/if}}
	</div>

