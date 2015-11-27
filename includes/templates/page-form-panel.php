<?php
/**
 * Panel template for form
 *
 * @package   Cf_Io
 * @author    David Cramer
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 David Cramer
 */

?>

<input type="hidden" name="data" value="{{#if data}}{{json data}}{{/if}}" data-live-sync="true" id="entry-trigger-{{id}}">

<?php
	// pull in the table list
	include CFIO_PATH . 'includes/templates/template-table-list.php';

?>
