<?php
/**
 * OpenQuote Log Administration List
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

?>
<form id="posts-filter" action="<?php echo $target;?>" method="post">
	<div>
		<h2>WordPress OpenQuote Log</h2>
		<p align="justify">This log details any issues that have occurred during the use of this component, including any problems with OpenQuote server access.</p>
	</div>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action">
				<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<option value="delete"><?php _e('Delete'); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
		</div>
		<div class='tablenav-pages'>
			<?php echo $this->showPagination();  // Echo out the list of paging. ?>
		</div>
		<br class="clear" />
	</div>

	<div class="clear"></div>

	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
				<th scope="col" id="Timestamp" class="manage-column column-tags" style="">Timestamp</th>
				<th scope="col" id="Detail" class="manage-column column-title" style="">Detail</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
				<th scope="col" class="manage-column column-tags" style="">Timestamp</th>
				<th scope="col" class="manage-column column-title" style="">Detail</th>
			</tr>
		</tfoot>

		<tbody>
			
<?php 
if ($rows) {
	foreach ($rows as $row) {
		$id = $row->id;
		$time = $row->time;
		$message = $row->message;
		
		$editlink = $target.'&mode=edit&record='.$id; 
		$deletelink = $target.'&mode=delete&record='.$id; 
?>
			<tr>
				<th class="check-column" scope="row"><input name="rowSelected[]" value="<?php echo $id; ?>" type="checkbox"></th>
				<td class="column-columnname"><?php echo $time;?><div class="row-actions"><span class="trash"><a class="submitdelete" title="Delete this item" href="<?php echo $deletelink?>">Delete</a></span></div></td>
				<td class="column-columnname"><?php echo $message;?></td>
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
			<select name="action2">
				<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<option value="delete"><?php _e('Delete'); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
		</div>
		<div class='tablenav-pages'>
			<?php echo $this->showPagination();  // Echo out the list of paging. ?>
		</div>
		<br class="clear" />
	</div>

</form>

