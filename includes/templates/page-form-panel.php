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
{{> list_template_<?php echo $cf_io['id']; ?>}}