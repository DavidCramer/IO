<?php
/**
 * Main edit interface for single items.
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */
$io_editor = true;
$cf_io = \calderawp\cfio\options::get_single( strip_tags( $_GET['edit'] ) );
$cf_ios = \calderawp\cfio\options::get_registry();

$forms = Caldera_Forms::get_forms();
$cf_io['forms'] = array();
$base_fields = array(
	'id' => array(
		'ID' => 'id',
		'label' => 'Entry ID',
	),
	'datestamp' => array(
		'ID' => 'datestamp',
		'label' => 'Entry Date',
	),
	'user_id' => array(
		'ID' => 'user_id',
		'label' => 'Created By',
	),	
	'status' => array(
		'ID' => 'status',
		'label' => 'Status',
	),
);
foreach( $forms as $form_id=>$form ){
	$pre = Caldera_Forms::get_form( $form_id );
	if( empty( $pre['fields'] ) ){
		$pre['fields'] = array();
	}
	if( !empty( $pre ) ){
		$cf_io['forms'][ $form_id ] = array(
			'id' => $form_id,
			'name' => $pre['name'],
			'fields' => array_merge( $base_fields, $pre['fields'] )
		);
	}
}
wp_enqueue_style( 'cf-grid-styles' );
wp_enqueue_style( 'cf-form-styles' );
wp_enqueue_style( 'cf-alert-styles' );
wp_enqueue_style( 'cf-field-styles' );

wp_enqueue_script( 'cf-field' );
wp_enqueue_script( 'cf-conditionals' );
wp_enqueue_script( 'cf-validator' );
wp_enqueue_script( 'cf-init' );


// magic tags
io_build_magic_tags( $cf_io );
?>

<div class="wrap cf-io-calderamain-canvas" id="cf-io-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#cf-io-main-canvas" data-before="cfio_canvas_reset" data-callback="cfio_canvas_init" data-type="json" data-request="#cf-io-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>

<div class="clear"></div>

<input type="hidden" class="clear" autocomplete="off" id="cf-io-live-config" style="width:100%;" value="<?php echo esc_attr( json_encode($cf_io) ); ?>">

<script type="text/html" id="main-ui-template">
	<?php
		/**
		 * Include main UI
		 */
		include CFIO_PATH . 'includes/templates/main-ui.php';
	?>	
</script>
<script type="text/html" data-handlebars-partial="filter_query_<?php echo $cf_io['id']; ?>">
	<?php
		$cf_io_config = $cf_io;
		$cf_io_id = $cf_io['id'];
		// pull in the filters partial
		include CFIO_PATH . 'includes/templates/partial-filters.php';
	?>
</script>