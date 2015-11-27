<?php

$internal_forms = \Caldera_Forms::get_forms();
foreach( $internal_forms as $form_id=>$form_config ){
	if( $element['ID'] == $form_id ){
		continue;
	}
	$useable_forms[ $form_id ] = $form_config;
}
?>
<div class="caldera-config-group">
	<label><?php _e('Form', 'io'); ?></label>
	<div class="caldera-config-field">
		<select id="{{_id}}_form" class="block-input required field-config" name="{{_name}}[form]"required>
		<?php foreach( $useable_forms as $form_id => $form_config ){ ?>
			<option value="<?php echo $form_config['ID']; ?>"><?php echo $form_config['name']; ?></option>
		<?php } ?>
		</select>
	</div>
</div>
