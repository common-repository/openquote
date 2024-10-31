<?php
/**
 * OpenQuote Product Administration Content
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

$id = '';
$ref = '';
$published = '';
$productName = '';
$productServerId = '';

// load record to edit if requested
if($mode=='edit'){
	if(isset($row)){
		$id = $row->id;
		$ref = $row->ref;
		$published = $row->published;
		$productName = $row->productName;
		$productServerId = $row->serverId;		
	}
	else{
		$mode='new';
	}
}

?>
 <form id="posts-filter" action="<?php echo $target;?>" method="post">
	<input type="hidden" value="<?php echo $mode; ?>" name="saveaction" />
	<input type="hidden" value="<?php echo $id; ?>" name="id" />
	<div>
		<h2>WordPress Product Administration</h2>
		<p align="justify">This administration page allows you to edit the details of an OpenQuote product that you want to make available for use on this website.</p>
		<p align="justify"><strong>Name: </strong>A unique name for this product, designed to help make website administration easier.</p>
		<p align="justify"><strong>Published: </strong>Controls the availability of this product on the website.</p>
		<p align="justify"><strong>Server: </strong>The URL for the OpenQuote server hosting this product. This list is configured using the <a href="<?php echo $serverListTarget; ?>"> Servers administration page</a>.</p>
		<p align="justify"><strong>Product Name: </strong>This is the id used as a reference to the product by the OpenQuote server, for example the id for the motor product example on the OpenQuote demo server is 'AIL.Demo.MotorPlus'.</p>
	</div>
	<div>
		<h2><?php 
	if($mode=='edit'){
		echo "Edit $ref";
	}	
	else{
		echo "New";
	}
		?></h2>
	</div>
	<div class="tablenav">
		<div class="alignleft actions">
			<input type="hidden" value="<?php echo $mode; ?>" name="saveaction" />
			<input type="submit" value="<?php esc_attr_e('Save'); ?>" name="mode" id="doaction" class="button-secondary action" />
			<input type="submit" value="<?php esc_attr_e('Cancel'); ?>" name="mode" id="doaction" class="button-secondary action" />
		</div>
		<br class="clear" />
	</div>
	<div>
		<fieldset class="adminform">
			<legend><?php echo _e( 'Details' ); ?></legend>

			<table class="admintable">
			<tr>
				<td width="100" align="right" class="key">
					<label for="ref">
						<?php echo _e( 'Name' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="ref" id="ref" size="100" maxlength="255" value="<?php echo $ref;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label>
						<?php echo _e( 'Published' ); ?>:
					</label>
				</td>
				<td>
					<input name="published" id="published0" value="0" <?php if(!$published){echo 'checked="checked"';}?> type="radio">
					<label for="published0">No</label>
					<input name="published" id="published1" value="1" <?php if($published){echo 'checked="checked"';}?> type="radio">
					<label for="published1">Yes</label>
				</td>
			</tr>		
			<tr>
				<td width="100" align="right" class="key">
					<label for="serverId">
						<?php echo _e( 'Server' ); ?>:
					</label>
				</td>
				<td>
					<select name="serverId" id="serverId" class="inputbox">
					<?php
					if ($serverRows) {
						foreach ($serverRows as $serverRow) {
							$server = $serverRow;
							$serverid = $server->id;
							$serverName = $server->openQuoteServer;
							$selected = $serverid==$productServerId;
					?>
						<option value="<?php echo $serverid; ?>" <?php if($selected){echo 'selected="selected"';} ?>><?php echo $serverName; ?></option>
					<?php
						}
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="productName">
						<?php echo _e( 'Product Name' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="productName" id="productName" size="100" maxlength="255" value="<?php echo $productName;?>" />
				</td>
			</tr>
		</table>
		</fieldset>
	</div>

	<div class="clear"></div>

	<div class="tablenav">
		<div class="alignleft actions">
			<input type="hidden" value="<?php echo $mode; ?>" name="saveaction" />
			<input type="submit" value="<?php esc_attr_e('Save'); ?>" name="mode" id="doaction" class="button-secondary action" />
			<input type="submit" value="<?php esc_attr_e('Cancel'); ?>" name="mode" id="doaction" class="button-secondary action" />
		</div>
		<br class="clear" />
	</div>

</form>
