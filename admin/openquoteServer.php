<?php
/**
 * OpenQuote Server Administration Content
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

$id = '';
$openQuoteServer = '';
$registrationServiceUrl = '';
$markupServiceUrl = '';
$consumerName = '';
$wsrpid = '';
$serverMessageTemplatesId = '';

// load record to edit if requested
if($mode=='edit'){
	if(isset($row)){
		$id = $row->id;
		$openQuoteServer = $row->openQuoteServer;
		$registrationServiceUrl = $row->registrationServiceUrl;
		$markupServiceUrl = $row->markupServiceUrl;
		$consumerName = $row->consumerName;
		$wsrpid = $row->wsrpid;	
		$serverMessageTemplatesId = $row->messageTemplatesId;	
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
		<h2>Wordpress OpenQuote Server Administration</h2>
		<p align="justify">This administration page allows you to edit the details of an OpenQuote server containing products that you want to make available for use on this website.</p>
		<p align="justify"><strong>Server: </strong> The URL for the OpenQuote server hosting this product, for example the OpenQuote demonstration server URL is 'http://www.appliedindustriallogic.com:8080'.</p>
		<p align="justify"><strong>Registration Service URL: </strong>The relative URL of the WSRP registration service on the OpenQuote server.  Typically this will be '/portal-wsrp/RegistrationService'.</p>
		<p align="justify"><strong>Markup Service URL: </strong>The relative URL of the WSRP registration service on the OpenQuote server.  Typically this will be '/portal-wsrp/MarkupService'.</p>
		<p align="justify"><strong>Consumer Name: </strong>A unique name that you must specify so the OpenQuote server can uniquely identify this website.</p>
		<p align="justify"><strong>WSRP ID: </strong>This value is automatically supplied by the OpenQuote server on first connection.  You should not change this value unless you have a technical understanding of the OpenQuote WSRP server you are accessing.</p>
		<p align="justify"><strong>Message Templates: </strong>WSRP SOAP message XML templates used when creating messages to communicate with the OpenQuote Server, the 'Default' list should work with any OpenQuote server. This list is configured using the <a href="<?php echo $target; ?>">Message Templates administration page</a>.</p>
	</div>
	<div>
		<h2><?php 
	if($mode=='edit'){
		echo "Edit $openQuoteServer";
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
					<label for="openQuoteServer">
						<?php echo _e( 'Server' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="openQuoteServer" id="openQuoteServer" size="100" maxlength="255" value="<?php echo $openQuoteServer;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="registrationServiceUrl">
						<?php echo _e( 'Registration Service URL' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="registrationServiceUrl" id="registrationServiceUrl" size="100" maxlength="255" value="<?php echo $registrationServiceUrl;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="markupServiceUrl">
						<?php echo _e( 'Markup Service URL' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="markupServiceUrl" id="markupServiceUrl" size="100" maxlength="255" value="<?php echo $markupServiceUrl;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="consumerName">
						<?php echo _e( 'Consumer Name' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="consumerName" id="consumerName" size="50" maxlength="50" value="<?php echo $consumerName;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="wsrpid">
						<?php echo _e( 'WSRP ID' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="wsrpid" id="wsrpid" size="25" maxlength="25" value="<?php echo $wsrpid;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="messageTemplatesId">
						<?php echo _e( 'Message Templates' ); ?>:
					</label>
				</td>
				<td>
					<select name="messageTemplatesId" id="messageTemplatesId" class="inputbox">
					<?php
					if ($messageTemplatesRows) {
						foreach ($messageTemplatesRows as $messageTemplatesRow) {
							$messageTemplates = $messageTemplatesRow;
							$messageTemplatesId = $messageTemplates->id;
							$messageTemplatesRef = $messageTemplates->templatesRef;
							$selected = $messageTemplatesId==$serverMessageTemplatesId;
					?>
						<option value="<?php echo $messageTemplatesId; ?>" <?php if($selected){echo 'selected="selected"';} ?>><?php echo $messageTemplatesRef; ?></option>
					<?php
						}
					}
					?>
					</select>
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
