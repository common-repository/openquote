<?php
/**
 * OpenQuote Server Administration List
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 
 
?>
<form id="posts-filter" action="<?php echo $target;?>" method="post">
	<div>
		<h2>WordPress Available OpenQuote Servers</h2>
		<p align="justify">These items contain the details of all OpenQuote servers that this component has access to. New servers can be added and existing servers removed at any point, the list of servers being made available for selection when adding or editing products.</p>
	</div>

	<div class="tablenav">
		<div class="alignleft actions">
			<select name="action">
				<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<option value="copy"><?php _e('Copy'); ?></option>
				<option value="delete"><?php _e('Delete'); ?></option>
				<option value="deregister"><?php _e('Deregister'); ?></option>
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
				<th scope="col" id="Server" class="manage-column column-slug" style="">Server</th>
				<th scope="col" id="RegService" class="manage-column column-title" style="">Reg Service</th>
				<th scope="col" id="MarkupService" class="manage-column column-title" style="">Markup Service</th>
				<th scope="col" id="ConsumerName" class="manage-column column-title" style="">Consumer Name</th>
				<th scope="col" id="WSRPID" class="manage-column" style="" width="10%">WSRP ID</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
				<th scope="col" id="ID" class="manage-column" style="" width="5%">ID</th>
				<th scope="col" id="Server" class="manage-column column-slug" style="">Server</th>
				<th scope="col" id="RegService" class="manage-column column-title" style="">Reg Service</th>
				<th scope="col" id="MarkupService" class="manage-column column-title" style="">Markup Service</th>
				<th scope="col" id="ConsumerName" class="manage-column column-title" style="">Consumer Name</th>
				<th scope="col" id="WSRPID" class="manage-column" style="" width="10%">WSRP ID</th>
			</tr>
		</tfoot>

		<tbody>
<?php 

if ($rows) {
	foreach ($rows as $row) {
		$id = $row->id;
		$openQuoteServer = $row->openQuoteServer;
		$registrationServiceUrl = $row->registrationServiceUrl;
		$markupServiceUrl = $row->markupServiceUrl;
		$consumerName = $row->consumerName;
		$wsrpid = $row->wsrpid;
		$messageTemplatesId = $row->messageTemplatesId;
		
		$deregistedOptionRequired = isset($wsrpid) && $wsrpid!='';
		
		$editlink = $target.'&mode=edit&record='.$id; 
		$copylink = $target.'&mode=copy&record='.$id; 
		$deletelink = $target.'&mode=delete&record='.$id; 
		$deregisterlink = $target.'&mode=deregister&record='.$id; 
?>
			<tr>
				<th class="check-column" scope="row"><input name="rowSelected[]" value="<?php echo $id; ?>" type="checkbox"></th>
				<td class="column-columnname"><?php echo $id;?></td>
				<td class="column-columnname"><?php echo $openQuoteServer;?><div class="row-actions"><span class="edit"><a href="<?php echo $editlink?>" title="Edit this item">Edit</a> | </span><span class="copy"><a href="<?php echo $copylink; ?>" title="Copy this item">Copy</a> | </span><span class="trash"><a class="submitdelete" title="Delete this item" href="<?php echo $deletelink?>">Delete</a> <?php if($deregistedOptionRequired){ ?>| </span><span class="trash"><a class="submitderegister" title="Deregister this item" href="<?php echo $deregisterlink?>">Deregister</a><?php } ?></span></div></td>
				<td class="column-columnname"><?php echo $registrationServiceUrl;?></td>
				<td class="column-columnname"><?php echo $markupServiceUrl;?></td>
				<td class="column-columnname"><?php echo $consumerName;?></td>
				<td class="column-columnname"><?php echo $wsrpid;?></td>
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
				<option value="deregister"><?php _e('Deregister'); ?></option>
			</select>
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
		</div>
		<div class='tablenav-pages'>
			<?php echo $this->showPagination();  // Echo out the list of paging. ?>
		</div>
		<br class="clear" />
	</div>

</form>

