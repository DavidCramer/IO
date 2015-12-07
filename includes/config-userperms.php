<?php
/**
 * Field Visibility config template
 *
 * @package   Caldera_Forms_Users
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer <david@digilab.co.za>
 */




?><div class="caldera-config-group">
	<label><?php echo __('Visibility', 'cf-users'); ?> </label>
	<div class="caldera-config-field">
		<select class="block-input field-config user-visibility-set" id="{{_id}}_visibility_type" data-set="{{_id}}" name="{{_name}}[visibility]">
			<option value="all" {{#is visibility value="all"}}selected="selected"{{/is}}><?php _e( 'Everyone', 'cf-users' ); ?></option>
			<option value="user" {{#is visibility value="user"}}selected="selected"{{/is}}><?php _e( 'Registered Users', 'cf-users' ); ?></option>
			<option value="public" {{#is visibility value="public"}}selected="selected"{{/is}}><?php _e( 'Public (Non Logged In Users Only)', 'cf-users' ); ?></option>
		</select>
	</div>
</div>
<div class="caldera-config-group" id="{{_id}}_roles" style="display:none;">
	<label><?php echo __('Roles', 'cf-users'); ?> </label>
	<div class="caldera-config-field">
	<label><input type="checkbox" id="{{_id}}_all_roles" class="field-config visible-all-roles" data-set="{{_id}}" value="1" name="{{_name}}[all_roles]" {{#if all_rolesform_role}}checked="checked"{{/if}}> <?php echo __('All'); ?></label>
	<hr>
	<?php
	global $wp_roles;
    $all_roles = $wp_roles->roles;
    $editable_roles = apply_filters('editable_roles', $all_roles);
	
	foreach($editable_roles as $role=>$role_details){
		?>
		<label><input type="checkbox" class="field-config {{_id}}_role_check gen_role_check" data-set="{{_id}}" name="{{_name}}[visibility_role][<?php echo $role; ?>]" value="1" {{#if visibility_role/<?php echo $role; ?>}}checked="checked"{{/if}}> <?php echo $role_details['name']; ?></label>
		<?php 
	}

	?>
	</div>
</div>

{{#script}}
	//<script>
	jQuery( function( $ ){

		$('#{{_id}}_visibility_type').on('change', function(){
			if( this.value === 'user' ){
				$('#{{_id}}_roles').slideDown();
			}else{
				$('#{{_id}}_roles').slideUp();
			}
		});


		$('#{{_id}}_visibility_type').trigger('change');
	} );

 
{{/script}}
