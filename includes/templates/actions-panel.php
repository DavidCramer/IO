<?php
/**
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer <david@digilab.co.za>
 */
?>
<div class="cf-io-config-group">
			<label for="cf_io-editing">
				<?php _e( 'Enable Editing', 'cf-io' ); ?>
			</label>
			<label style="width: auto; margin: 0px;"><input type="checkbox" name="editing" data-live-sync="true" value="1" {{#if editing}}checked="checked"{{/if}} id="cf_io-editing"> <?php _e('Show the Edit button on entries','cf-io'); ?></label>
		</div>
		
		{{#if editing}}
		<div id="cf-io-editing-rules">
			<div class="cf-io-config-group">
				<label><?php echo __('Editing Roles', 'cf-io'); ?> </label>
				<div class="cf-io-config-field" style="max-width: 500px; display: inline-block;">
				<label><input type="checkbox" class="field-config visible-all-roles" data-live-sync="true" data-set="form_role" value="1" name="edit_roles[_all_roles]" {{#if edit_roles/_all_roles}}checked="checked"{{/if}}> <?php echo __('All'); ?></label>
				{{#unless edit_roles/_all_roles}}
				<hr>
				<?php
				global $wp_roles;
			    $all_roles = $wp_roles->roles;
			    $editable_roles = apply_filters( 'editable_roles', $all_roles);
				
				foreach($editable_roles as $role=>$role_details){

					?>
					<label style="display: block; width: 200px;"><input type="checkbox" class="field-config form_role_role_check gen_role_check" data-set="form_role" name="edit_roles[<?php echo $role; ?>]" value="1"  {{#if edit_roles/<?php echo $role; ?>}}checked="checked"{{/if}}> <?php echo $role_details['name']; ?></label>
					<?php 
				}

				?>
				{{/unless}}
				<hr>
				</div>
			</div>	
		</div>		
		{{/if}}
		<div class="cf-io-config-group">
			<label for="cf_io-capture">
				<?php _e( 'Enable Capture', 'cf-io' ); ?>
			</label>
			<label style="width: auto; margin: 0px;"><input type="checkbox" data-live-sync="true" name="capture" value="1" {{#if capture}}checked="checked"{{/if}} id="cf_io-capture"> <?php _e('Adds the "Add Entry" button','cf-io'); ?></label>
		</div>

		{{#if capture}}
		<div id="cf-io-capture-rules">
			<div class="cf-io-config-group">
				<label><?php echo __('Capture Roles', 'cf-io'); ?> </label>
				<div class="cf-io-config-field" style="max-width: 500px; display: inline-block;">
				<label><input type="checkbox" class="field-config visible-all-roles" data-live-sync="true" data-set="form_role" value="1" name="capture_roles[_all_roles]" {{#if capture_roles/_all_roles}}checked="checked"{{/if}}> <?php echo __('All'); ?></label>
				{{#unless capture_roles/_all_roles}}
				<hr>
				<?php
				global $wp_roles;
			    $all_roles = $wp_roles->roles;
			    $editable_roles = apply_filters( 'editable_roles', $all_roles);
				
				foreach($editable_roles as $role=>$role_details){

					?>
					<label style="display: block; width: 200px;"><input type="checkbox" class="field-config form_role_role_check gen_role_check" data-set="form_role" name="capture_roles[<?php echo $role; ?>]" value="1"  {{#if capture_roles/<?php echo $role; ?>}}checked="checked"{{/if}}> <?php echo $role_details['name']; ?></label>
					<?php 
				}

				?>
				{{/unless}}
				<hr>
				</div>
			</div>	
		</div>		
		{{/if}}