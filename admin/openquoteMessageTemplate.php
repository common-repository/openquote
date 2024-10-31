<?php
/**
 * OpenQuote Message Templates Administration Content
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

$id = '';
$templatesRef = '';
$submitWSRPFormXML = '';
$requestWSRPFormXML = '';
$registerWSRPConsumerXML = '';
$deregisterWSRPConsumerXML = '';

// load record to edit if requested
if($mode=='edit'){
	if(isset($row)){
		$id = $row->id;
		$templatesRef = $row->templatesRef;
		$submitWSRPFormXML = $row->submitWSRPFormXML;
		$requestWSRPFormXML = $row->requestWSRPFormXML;
		$registerWSRPConsumerXML = $row->registerWSRPConsumerXML;
		$deregisterWSRPConsumerXML = $row->deregisterWSRPConsumerXML;		
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
		<h2>Wordpress OpenQuote Message Templates Administration</h2>
		<p align="justify">This administration page allows you to edit the WSRP SOAP XML message templates used for communicating with OpenQuote servers.</p>
		<p align="justify"><strong>Reference: </strong> A unique name to identify this set of templates.</p>
		<p align="justify"><strong>Submit WSRP Form XML: </strong>SOAP message XML template used as a basis for 'Submit Form' messages that are sent to the OpenQuote server.</p>
		<p align="justify"><strong>Request WSRP Form XML: </strong>SOAP message XML template used as a basis for 'Request Form' messages that are sent to the OpenQuote server.</p>
		<p align="justify"><strong>Register WSRP Consumer XML: </strong>SOAP message XML template used as a basis for 'Regisgter WSRP Consumer' messages that are sent to the OpenQuote server.</p>
		<p align="justify"><strong>Deregister WSRP Consumer XML: </strong>SOAP message XML template used as a basis for 'Deregister WSRP Consumer' messages that are sent to the OpenQuote server.</p>
	</div>
	<div>
		<h2><?php 
	if($mode=='edit'){
		echo "Edit $templatesRef";
	}	
	else{
		echo "New";
	}
		?></h2>
	</div>
	<div class="tablenav">
		<div class="alignleft actions">
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
					<label for="templatesRef">
						<?php echo _e( 'Reference' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="templatesRef" id="templatesRef" size="25" maxlength="25" value="<?php echo $templatesRef;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="submitWSRPFormXML">
						<?php echo _e( 'Submit WSRP Form XML' ); ?>:
					</label>
				</td>
				<td>
					<textarea class="text_area" cols="100" rows="5" name="submitWSRPFormXML" id="submitWSRPFormXML"><?php echo $submitWSRPFormXML;?></textarea>
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="requestWSRPFormXML">
						<?php echo _e( 'Request WSRP Form XML' ); ?>:
					</label>
				</td>
				<td>
					<textarea class="text_area" cols="100" rows="5" name="requestWSRPFormXML" id="requestWSRPFormXML"><?php echo $requestWSRPFormXML;?></textarea>
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="registerWSRPConsumerXML">
						<?php echo _e( 'Register WSRP Consumer XML' ); ?>:
					</label>
				</td>
				<td>
					<textarea class="text_area" cols="100" rows="5" name="registerWSRPConsumerXML" id="registerWSRPConsumerXML"><?php echo $registerWSRPConsumerXML;?></textarea>
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="deregisterWSRPConsumerXML">
						<?php echo _e( 'Deregister WSRP Consumer XML' ); ?>:
					</label>
				</td>
				<td>
					<textarea class="text_area" cols="100" rows="5" name="deregisterWSRPConsumerXML" id="deregisterWSRPConsumerXML"><?php echo $deregisterWSRPConsumerXML;?></textarea>
				</td>
			</tr>
		</table>
		</fieldset>
	</div>

	<div class="clear"></div>

	<div class="tablenav">
		<div class="alignleft actions">
			<input type="submit" value="<?php esc_attr_e('Save'); ?>" name="mode" id="doaction" class="button-secondary action" />
			<input type="submit" value="<?php esc_attr_e('Cancel'); ?>" name="mode" id="doaction" class="button-secondary action" />
		</div>
		<br class="clear" />
	</div>

</form>
