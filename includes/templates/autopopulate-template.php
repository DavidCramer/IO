<?php
if( empty( $element['io_auto_populate'] ) ){
	$element['io_auto_populate'] = array();
}

// build a list of forms and fields
$form_db = array();
$forms = Caldera_Forms::get_forms();
foreach( $forms as $form ){

	if( $form['ID'] == $element['ID'] ){
		continue;
	}

	$form = Caldera_Forms::get_form( $form['ID'] );

	$form_db[ $form['ID'] ] = array(
		'ID'  	=> $form['ID'],
		'name'	=> $form['name'],
		'fields'=> $form['fields'],
	);

}
?>
<input type="text" id="cf-io-autopopulation" name="config[io_auto_populate]" value=""
	class="ajax-trigger"
	data-event="rebuild-autopopulate"
	data-request="#cf-io-autopopulation"
	data-type="json"
	data-template="#io-field-options-list-tmpl"
	data-target="#io-autopopulation-wrapper"
>
<div id="cf-io-autopopulate-app">
	<div id="io-autopopulation-wrapper"></div>
</div>

<script type="text/html" id="io-field-options-list-tmpl">

<input type="hidden" id="cf-io-autopopulation" name="config[io_auto_populate][active_field]" value="{{active_field}}">
	<div class="io-autopopulation-field-list">
		{{#each fields}}
		<label style="" class="io-field-nav {{#is ../active_field value=id}}active{{/is}}" id="field-{{field}}">
			{{name}}
			<input type="hidden" name="config[io_auto_populate][fields][{{id}}]" value="{{json this}}">
			<input type="radio" style="display:none;" name="config[io_auto_populate][active_field]" value="{{id}}" data-sync-fields="true">
		</label>

		{{/each}}
	</div>

	{{#find fields active_field}}
		<div class="io-autopopulation-field-config">
			<select data-sync-fields="true" name="config[io_auto_populate][fields][{{id}}][form]">
			<option></option>
			{{#each ../forms}}
				<option value="{{ID}}" {{#is ../form value=ID}}selected="selected"{{/is}}>{{name}}</option>
			{{/each}}
			</select>
			{{#if form}}
			<div>
				{{#unless filters}}
					<button type="button" class="button filter-action-and wp-baldrick" data-add-query="fields.{{id}}.filters" data-node-default='{ "form" : "{{form}}", path" : "config[io_auto_populate][fields][{{id}}][filters]", "type" : "and" }'>Add filter</button>
				{{else}}
					{{#each filters}}
						{{>filter_query}}
					{{/each}}
					<button type="button" class="button filter-action-and wp-baldrick" data-add-query="fields.{{id}}.filters" data-node-default='{ "form" : "{{form}}", "path" : "config[io_auto_populate][fields][{{id}}][filters]", "type" : "and" }'>and</button>
				{{/unless}}
			</div>
			{{/if}}
		</div>
	{{/find}}

</script>

<script type="text/javascript">
	var io_cf_forms = <?php echo json_encode( $form_db ); ?>,
		io_cf_config = <?php echo json_encode( $element['io_auto_populate'] ); ?>;

</script>
<script type="text/html" id="filters-partial-tmpl">
	{{json @root/fields}}
	<div class="filter-line-{{_id}}">
		<input type="hidden" name="{{path}}[{{_id}}][form]" value="{{form}}">
		<div class="filter-item filter-{{type}}">
			<span class="filter-remove-line" data-remove-element=".filter-line-{{_id}}"><span class="dashicons dashicons-no-alt"></span></span>

			<input type="hidden" name="{{path}}[{{_id}}][type]" value="{{type}}">
			<span class="filter-clause">{{#is type value="and"}}{{#unless @first}}<?php _e('and', 'cf-io'); ?>{{else}}<?php _e('where', 'cf-io'); ?>{{/unless}}{{else}}<?php _e('or', 'cf-io'); ?>{{/is}}</span>
			<div class="filter-field">
				<select class="filter-field-select" name="{{path}}[{{_id}}][field]" data-sync-fields="true" required="required">
					<option></option>
					{{#find @root/forms form}}
						{{#each fields}}
						<option value="{{ID}}" {{#is ../../field value=ID}}selected="selected"{{/is}}>{{label}}</option>
						{{/each}}
					{{/find}}
				</select>
				<select class="filter-field-compare" name="{{path}}[{{_id}}][compare]" data-sync-fields="true">
					<option value="is" {{#is compare value="is"}}selected="selected"{{/is}}>=</option>
					<option value="isnot" {{#is compare value="isnot"}}selected="selected"{{/is}}>!=</option>
					<option value="isnull" {{#is compare value="isnull"}}selected="selected"{{/is}}>IS NULL</option>
					<option value="isin" {{#is compare value="isin"}}selected="selected"{{/is}}>IN</option>
					<option value="isnotin" {{#is compare value="isnotin"}}selected="selected"{{/is}}>NOT IN</option>
					<option value="greater" {{#is compare value="greater"}}selected="selected"{{/is}}>&gt;</option>
					<option value="greatereq" {{#is compare value="greatereq"}}selected="selected"{{/is}}>&gt;=</option>
					<option value="smaller" {{#is compare value="smaller"}}selected="selected"{{/is}}>&lt;</option>
					<option value="smallereq" {{#is compare value="smallereq"}}selected="selected"{{/is}}>&lt;=</option>
					<option value="startswith" {{#is compare value="startswith"}}selected="selected"{{/is}}>starts with</option>
					<option value="endswith" {{#is compare value="endswith"}}selected="selected"{{/is}}>ends with</option>
					<option value="contains" {{#is compare value="contains"}}selected="selected"{{/is}}>contains</option>
				</select>
				<span style="display: inline-block;">
				{{#is compare value="isnull"}}
					<input type="hidden" id="field_{{_id}}" name="{{path}}[{{_id}}][value]" value="{{value}}">
				{{else}}
					<input type="text" class="magic-tag-enabled" id="field_{{_id}}" name="{{path}}[{{_id}}][value]" value="{{value}}">
				{{/is}}
				</span>
			</div>
			<div class="filter-actions">
				<button type="button" class="button filter-action-or wp-baldrick" data-add-query="{{_node_point}}.or" data-sync-fields="true" data-node-default='{"type":"or"}'>or</button>
			</div>
		</div>
		{{#each or}}

			{{> filter_query}}
			
		{{/each}}
	</div>


</script>