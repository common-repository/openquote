<?php
/**
 * OpenQuote Action Confirmation Administration Page
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

?>
<form id="posts-filter" action="<?php echo $target;?>" method="post">
	<div>
		<h2>Please confirm <?php echo $mode ?>:</h2>
		<input type="hidden" value="<?php echo $mode; ?>" name="mode" />
	</div>

	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" value="<?php esc_attr_e('Confirm'); ?>" name="actionconfirm" id="doaction" class="button-secondary action" />
			<input type="submit" value="<?php esc_attr_e('Cancel'); ?>" name="actioncancel" id="doaction" class="button-secondary action" />
		</div>
		<br class="clear" />
	</div>
	
	<div class="clear"></div>
	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="ID" class="manage-column" style="" width="5%">ID</th>
				<th scope="col" id="Reference" class="manage-column column-slug" style="">Reference</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" id="ID" class="manage-column" style="" width="5%">ID</th>
				<th scope="col" id="Reference" class="manage-column column-slug" style="">Reference</th>
			</tr>
		</tfoot>

		<tbody>
<?php 
if ($records) {
	foreach ($records as $id) {
		$ref = $references[$id];
?>
			<tr>
				<input type="hidden" value="<?php echo $id; ?>" name="rowSelected[]" />
				<td class="column-columnname"><?php echo $id;?></td>
				<td class="column-columnname"><?php echo $ref;?></td>
			</tr>
<?php 
	}
} else { 
?>
			<tr>
				<th/>
				<td>No Items Found!</td>
				<td/>
			<tr>
<?php 
} 
?>			
		</tbody>
	</table>

	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" value="<?php esc_attr_e('Confirm'); ?>" name="actionconfirm" id="doaction" class="button-secondary action" />
			<input type="submit" value="<?php esc_attr_e('Cancel'); ?>" name="actioncancel" id="doaction" class="button-secondary action" />
		</div>
		<br class="clear" />
	</div>
	
</form>

