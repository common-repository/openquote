<?php
/**
 * OpenQuote Product Administration List
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 
 
?>
<form id="posts-filter" action="<?php echo $target;?>" method="post">
	<div>
		<h2>WordPress OpenQuote Products</h2>
		<p align="justify">The items listed here define what OpenQuote insurance products are available to use. Products can be added to and removed from the list as required by specifiying the server they are distributed from and their id on that server, these can then be specified when adding the tag <strong>[openquote product=</strong>product name<strong>]</strong> to any page content.</p>
	</div>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action">
				<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<option value="copy"><?php _e('Copy'); ?></option>
				<option value="delete"><?php _e('Delete'); ?></option>
				<option value="publish"><?php _e('Publish'); ?></option>
				<option value="unpublish"><?php _e('Unpublish'); ?></option>
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
				<th scope="col" id="Name" class="manage-column column-tags" style="">Name</th>
				<th scope="col" id="Product" class="manage-column column-tags" style="">Product</th>
				<th scope="col" id="Published" class="manage-column column-rating" style="">Published</th>
				<th scope="col" id="Server" class="manage-column column-title" style="">Server</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
				<th scope="col" id="ID" class="manage-column" style="" width="5%">ID</th>
				<th scope="col" id="Name" class="manage-column column-tags" style="">Name</th>
				<th scope="col" id="Product" class="manage-column column-tags" style="">Product</th>
				<th scope="col" id="Published" class="manage-column column-rating" style="">Published</th>
				<th scope="col" id="Server" class="manage-column column-title" style="">Server</th>
			</tr>
		</tfoot>

		<tbody>
			
<?php 

if ($rows) {
	foreach ($rows as $row) {
		$id = $row->id;
		$ref = $row->ref;
		$productName = $row->productName;
		$published = $row->published;
		$serverId = $row->serverId;
		$openQuoteServer = $row->openQuoteServer;

		$editlink = $target.'&mode=edit&record='.$id; 
		$copylink = $target.'&mode=copy&record='.$id; 
		$deletelink = $target.'&mode=delete&record='.$id; 
		$publishlink = $target.'&mode='.($published?'unpublish':'publish').'&record='.$id; 
?>
			<tr>
				<th class="check-column" scope="row"><input name="rowSelected[]" value="<?php echo $id; ?>" type="checkbox"></th>
				<td class="column-columnname"><?php echo $id;?></td>
				<td class="column-columnname"><?php echo $ref;?><div class="row-actions"><span class="edit"><a href="<?php echo $editlink; ?>" title="Edit this item">Edit</a> | </span><span class="copy"><a href="<?php echo $copylink; ?>" title="Copy this item">Copy</a> | </span><span class="trash"><a class="submitdelete" title="Delete this item" href="<?php echo $deletelink; ?>">Delete</a> | </span><span class="edit"><a class="submitedit" title="Publish this item flag" href="<?php echo $publishlink; ?>"><?php echo $published ? 'Unpublish' : 'Publish';?></a> </span></div></td>
				<td class="column-columnname"><?php echo $productName;?></td>
				<td class="column-columnname" align="center"><img src="../wp-admin/images/<?php if($published){echo 'yes';}else{echo 'no';}?>.png" /></td>
				<td class="column-columnname"><?php echo $serverId . ': ' . $openQuoteServer;?></td>
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
				<option value="publish"><?php _e('Publish'); ?></option>
				<option value="unpublish"><?php _e('Unpublish'); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
		</div>
		<div class='tablenav-pages'>
			<?php echo $this->showPagination();  // Echo out the list of paging. ?>
		</div>
		<br class="clear" />
	</div>

</form>

