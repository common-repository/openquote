<?php
/**
 * OpenQuote Display Text Administration List
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

?>
<form id="posts-filter" action="<?php echo $target;?>" method="post">
	<div>
		<h2>WordPress OpenQuote Display Text</h2>
		<p align="justify">These items allow you to define how specific errors are presented to the user.</p>
	</div>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action">
				<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<option value="copy"><?php _e('Copy'); ?></option>
				<option value="delete"><?php _e('Delete'); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
			<input type="submit" value="<?php esc_attr_e('New'); ?>" name="mode" id="doaction" class="button-secondary action" />
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
				<th scope="col" id="ID" class="manage-column" style="" width="5%">ID</th>
				<th scope="col" id="Subject" class="manage-column column-slug" style="">Subject</th>
				<th scope="col" id="Display" class="manage-column column-title" style="">Display</th>
<!--				<th scope="col" id="ReportAs404" class="manage-column column-rating" style="">Report as 404</th> -->
				<th scope="col" id="EmailAdmin" class="manage-column column-rating" style="">Email Admin</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
				<th scope="col" id="ID" class="manage-column" style="" width="5%">ID</th>
				<th scope="col" id="Subject" class="manage-column column-slug" style="">Subject</th>
				<th scope="col" id="Display" class="manage-column column-title" style="">Display</th>
<!--				<th scope="col" id="ReportAs404" class="manage-column column-rating" style="">Report as 404</th> -->
				<th scope="col" id="EmailAdmin" class="manage-column column-rating" style="">Email Admin</th>
			</tr>
		</tfoot>

		<tbody>
<?php 
if ($rows) {
	foreach ($rows as $row) {
		$id = $row->id;
		$infoRef = $row->infoRef;
		$infoText = $row->infoText;
		$reportAs404 = $row->reportAs404;
		$emailAdmin = $row->emailAdmin;
 		
		$editlink = $target.'&mode=edit&record='.$row->id; 
		$copylink = $target.'&mode=copy&record='.$id; 
		$deletelink = $target.'&mode=delete&record='.$row->id; 
?>
			<tr>
				<th class="check-column" scope="row"><input name="rowSelected[]" value="<?php echo $id; ?>" type="checkbox"></th>
				<td class="column-columnname"><?php echo $id;?></td>
				<td class="column-columnname"><?php echo $infoRef;?><div class="row-actions"><span class="edit"><a href="<?php echo $editlink?>" title="Edit this item">Edit</a> | </span><span class="copy"><a href="<?php echo $copylink; ?>" title="Copy this item">Copy</a> | </span><span class="trash"><a class="submitdelete" title="Delete this item" href="<?php echo $deletelink?>">Delete</a></span></div></td>
				<td class="column-columnname"><?php echo $infoText;?></td>
<!--				<td class="column-columnname" align="center" style=""><img src="../wp-admin/images/<?php //if($reportAs404){echo 'yes';}else{echo 'no';}?>.png" /></td> -->
				<td class="column-columnname" align="center" style=""><img src="../wp-admin/images/<?php if($emailAdmin){echo 'yes';}else{echo 'no';}?>.png" /></td>
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
				<option value="copy"><?php _e('Copy'); ?></option>
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

