	<div class="filter-line-{{_id}}">
		<div class="filter-item filter-{{type}}">
			<span class="filter-remove-line" data-remove-element=".filter-line-{{_id}}"><span class="dashicons dashicons-no-alt"></span></span>
			{{:node_point}}
			<input type="hidden" name="{{:name}}[type]" value="{{type}}">
			<span class="filter-clause">{{#is type value="and"}}{{#unless @first}}<?php _e('and', 'cf-reports'); ?>{{else}}<?php _e('where', 'cf-reports'); ?>{{/unless}}{{else}}<?php _e('or', 'cf-reports'); ?>{{/is}}</span>
			<div class="filter-field">
				<select class="filter-field-select" name="{{:name}}[field]" data-live-sync="true" required="required">
					<option></option>
					<?php foreach( $cf_io_config['forms'][ $cf_io_config['form'] ]['fields'] as $field_id => $field ){ ?>
						<option value="<?php echo $field_id; ?>" {{#is field value="<?php echo $field_id; ?>"}}selected="selected"{{/is}}><?php echo $field['label']; ?></option>
					<?php } ?>
				</select>
				<select class="filter-field-compare" name="{{:name}}[compare]" data-live-sync="true">
					<option value="is" {{#is compare value="is"}}selected="selected"{{/is}}>is</option>
					<option value="isnot" {{#is compare value="isnot"}}selected="selected"{{/is}}>is not</option>
					<option value="isin" {{#is compare value="isin"}}selected="selected"{{/is}}>in</option>
					<option value="isnotin" {{#is compare value="isnotin"}}selected="selected"{{/is}}>not in</option>
					<option value="greater" {{#is compare value="greater"}}selected="selected"{{/is}}>greater than</option>
					<option value="greatereq" {{#is compare value="greatereq"}}selected="selected"{{/is}}>greater or equal</option>
					<option value="smaller" {{#is compare value="smaller"}}selected="selected"{{/is}}>smaller</option>
					<option value="smallereq" {{#is compare value="smallereq"}}selected="selected"{{/is}}>smaller or equal</option>
					<option value="startswith" {{#is compare value="startswith"}}selected="selected"{{/is}}>starts with</option>
					<option value="endswith" {{#is compare value="endswith"}}selected="selected"{{/is}}>ends with</option>
					<option value="contains" {{#is compare value="contains"}}selected="selected"{{/is}}>contains</option>
				</select>
				<input type="hidden" id="field_{{_id}}_val" value="{{value}}">
				{{#is field value="user_id"}}
					<?php wp_dropdown_users( array('id' => 'field_{{_id}}', 'name' => '{{:name}}[value]') ); ?>
				{{/is}}
				{{#is field value="id"}}
					<input type="text" id="field_{{_id}}" name="{{:name}}[value]" value="{{value}}">
				{{/is}}
				{{#is field value="datestamp"}}
					<input type="text" name="{{:name}}[value]" value="{{value}}" data-date-start-view="month" data-date-format="yyyy-mm-dd" id="field_{{_id}}" class="is-cfdatepicker" data-provide="cfdatepicker">
				{{/is}}
				{{#is field value="status"}}
					<select id="field_{{_id}}" name="{{:name}}[value]">
						 <option value="active" {{#is value value="active"}}selected="selected"{{/is}}>Active</option>
						 <option value="trash" {{#is value value="trash"}}selected="selected"{{/is}}>Trash</option>
					</select>
				{{/is}}
				<?php foreach( $cf_io_config['forms'][ $cf_io_config['form'] ]['fields'] as $field_id => $field ){ 
					if( in_array( $field_id, array( "user_id", "id", "datestamp", "status" ) ) ){ continue; }

					// get field
					$field = apply_filters( 'caldera_forms_render_get_field', $field, $cf_io_config['forms'][ $cf_io_config['form'] ] );

					?>
					{{#is field value="<?php echo $field_id; ?>"}}
					<?php if( !empty( $field['config']['option'] ) ){ ?>
						<select id="field_{{_id}}" name="{{:name}}[value]">
							<?php foreach( $field['config']['option'] as $option_id=>$option ){ ?>
							 <option value="<?php echo $option['value']; ?>" {{#is value value="<?php echo $option['value']; ?>"}}selected="selected"{{/is}}><?php echo $option['label']; ?></option>
							<?php } ?>
						</select>
					<?php }else{ ?>
						<input type="text" id="field_{{_id}}" name="{{:name}}[value]" value="{{value}}">
					<?php } ?>
					{{/is}}
				<?php } ?>
				{{#script}}jQuery("#field_{{_id}}").val( jQuery("#field_{{_id}}_val").val() );{{/script}}
			</div>
			<div class="filter-actions">

				<button type="button" class="button filter-action-or wp-baldrick" {{#if @root/parent_id}}data-target="{{@root/id}}"{{/if}} data-add-node="{{_node_point}}.or" data-node-default='{"type":"or"}'>or</button>
				
			</div>
		</div>
		{{#each or}}
			{{> filter_query_<?php echo $cf_io_id; ?>}}
		{{/each}}
	</div>