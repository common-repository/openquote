<?php
/**
 * OpenQuote User Display Text Administration Content
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

$id = '';
$infoRef = '';
$infoText = '';
$reportAs404 = '';
$emailAdmin = '';

// load record to edit if requested
if($mode=='edit'){
	if(isset($row)){
		$id = $row->id;
		$infoRef = $row->infoRef;
		$infoText = $row->infoText;
		$reportAs404 = $row->reportAs404;
		$emailAdmin = $row->emailAdmin;
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
		<h2>WordPress OpenQuote User Information Display Text Administration</h2>
		<p align="justify">This administration page allows you to edit the messages displayed to users when problems occur.</p>
		<p align="justify"><strong>Subject: </strong>The name of the issue being reported.</p>
		<p align="justify"><strong>Text to display to user: </strong>The message that should be displayed to users when this issue occurs.</p>
<!--		<p align="justify"><strong>Report as http 404 error: </strong>How the issue should be reported to users, either via a http 404 web page, or by displaying the text directly on the selected web page.</p>-->
		<p align="justify"><strong>Email Admin: </strong>Indicates if the error should be emailed to the site administrator.</p>
	</div>
	<div>
		<h2><?php 
	if($mode=='edit'){
		echo "Edit $infoRef";
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
					<label for="infoRef">
						<?php echo _e( 'Subject' ); ?>:
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="infoRef" id="infoRef" size="100" maxlength="255" value="<?php echo $infoRef;?>" />
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label for="infoText">
						<?php echo _e( 'Text to display to user' ); ?>:
					</label>
				</td>
				<td>
					<textarea class="text_area" cols="100" rows="5" name="infoText" id="infoText"><?php echo $infoText;?></textarea>
				</td>
			</tr>
			<tr style="visibility:hidden;">
				<td width="100" align="right" class="key">
					<label>
						<?php echo _e( 'Report as http 404 error' ); ?>:
					</label>
				</td>
				<td>
					<input disabled name="reportAs404" id="reportAs4040" value="0" <?php if(!$reportAs404){echo 'checked="checked"';}?> type="radio">
					<label for="reportAs4040">No</label>
					<input disabled name="reportAs404" id="reportAs4041" value="1" <?php if($reportAs404){echo 'checked="checked"';}?> type="radio">
					<label for="reportAs4041">Yes</label>
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<label>
						<?php echo _e( 'Email error to site admin' ); ?>:
					</label>
				</td>
				<td>
					<input name="emailAdmin" id="emailAdmin0" value="0" <?php if(!$emailAdmin){echo 'checked="checked"';}?> type="radio">
					<label for="emailAdmin0">No</label>
					<input name="emailAdmin" id="emailAdmin1" value="1" <?php if($emailAdmin){echo 'checked="checked"';}?> type="radio">
					<label for="emailAdmin1">Yes</label>
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
