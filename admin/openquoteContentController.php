<?php
/**
 * @package OpenQuote
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

// load 3rd party classes
require_once(dirname(__FILE__).'/lib/openquote.php' );


if (!class_exists('OpenQuoteContentController')) {

    class OpenQuoteContentController {

			
	    /**
		 * log table name
		 * @var string
		 */
		var $logTableName = '__openquotelog';
		var $messageTemplatesTableName = '__openquotemessagetemplates';
		var $userInformationTableName = '__openquoteuserinformation';
		var $serverTableName = '__openquoteserver';
		var $productTableName = '__openquoteproduct';

		/**
		 * OpeenQuote Connection Class
		 *
		 * @var object
		 */
		var $_openquoteconnect = null;

		/**
		 * Product name
		 *
		 * @var string
		 */
		var $_productname = null;
		
		/**
		 * Product data
		 *
		 * @var array
		 */
		var $_productdata = null;

		
	    /**
		 * OpenQuote WordPress Shortcode
		 * @var string
		 */
        var $openquoteContentMarkup = 'openquote';
		var $openquoteContentMarkupLength = 9;

		
        function OpenQuoteContentController() { //constructor
        }

		
		/**
		 * Method to set the product name
		 *
		 * @access	public
		 * @param	string product name
		 */
		function setProductName($name)
		{
			// Set product name and wipe data
			$this->_productname = $name;
			$this->_productdata = null;
		}


		/** 
		 * Insert OpenQuote content into post content if '[openquote product="<product name>"]' string is found within it
		 * @param attr shortcode parameters
		 * @param content shortcode content
		 * @return OpenQuote content
		 */
		function addOpenQuoteContent($attr, $content) {
			$product = $attr['product'];
			$this->setProductName($product);
			return $this->getQuotePageFlow();
		}		 


		/**
		 * Gets the quote page flow
		 * @return string html to be displayed to the user
		 */
		function getQuotePageFlow()
		{
			
			// load product & server details
			if (!($this->_loadProductData())){
				$this->_updateLog("Product {$this->_productid} Configuration Resource Not Found");
				$message = $this->_loadUserInformation('No Product Config');
				if(!$message){return false;}
				return '<div class="openquote4wordpress"><p>'.$message.'</p></div>';
			}
			
			// don't use if product not published in wordpress
			if (!($this->_productdata->published)){
				$this->_updateLog("Product $this->_productid: {$this->_productdata->productName} Configuration Not Published");
				$message = $this->_loadUserInformation('No Product Published');
				if(!$message){return false;}
				return '<div class="openquote4wordpress"><p>'.$message.'</p></div>';
			}

			return $this->getQuotePageFlowAfterProductLoad();
		}

		/**
		 * Gets the quote page flow
		 * @return string html to be displayed to the user
		 */
		function getQuotePageFlowAfterProductLoad()
		{
			$OQHtml = '';
			
			// product details
			$productName = $this->_productdata->productName;
			$openQuoteServer = $this->_productdata->openQuoteServer;
			$registrationServiceUrl = $this->_productdata->registrationServiceUrl;
			$markupServiceUrl = $this->_productdata->markupServiceUrl;
			$consumerName = $this->_productdata->consumerName;
			$registrationHandle = $this->_productdata->wsrpid;
			$templatesRef = $this->_productdata->templatesRef;
			$submitWSRPFormXML = $this->_productdata->submitWSRPFormXML;
			$requestWSRPFormXML = $this->_productdata->requestWSRPFormXML;
			$registerWSRPConsumerXML = $this->_productdata->registerWSRPConsumerXML;
			$deregisterWSRPConsumerXML = $this->_productdata->deregisterWSRPConsumerXML;		

			// initialise OpenQuote connection helper
			if($this->_openquoteconnect==null){
				$this->_openquoteconnect = new OpenQuoteConnect();
				$newRegistrationHandle = $this->_openquoteconnect->initialiser(	$productName,
												$openQuoteServer,
												$registrationServiceUrl,
												$markupServiceUrl,
												$consumerName,
												$submitWSRPFormXML,
												$requestWSRPFormXML,
												$registerWSRPConsumerXML,
												$deregisterWSRPConsumerXML,
												$registrationHandle,
												$_SERVER['REQUEST_URI'],
												$_SERVER['HTTP_USER_AGENT']);
				// if no saved registration handle, then saved the generated one
				if(empty($registrationHandle)){
					$registrationHandle = $newRegistrationHandle;
					$this->_saveRegistrationHandle($registrationHandle);
				}
			}

			// no successful registration
			if(empty($registrationHandle)){
				$this->_checkAndUpdateLog("Product {$productName} OpenQuote server registration failed");
				$message = $this->_loadUserInformation('OpenQuote Server Reg Failed');
				if(!$message){return false;}
				return '<div class="openquote4wordpress"><p>'.$message.'</p></div>';
			}
			
			
			// get user session
			$userSessionArray = $_SESSION['openquoteUserSessions'];
			if(empty($userSessionArray) || !is_array($userSessionArray)){
				$userSessionArray = array();		
			}


			// if the requerst contains post parameters, assume it was a portal submit, so pass the form parameters to OpenQuote
			if($_POST!=null && !empty($_POST)){
				// submit the openquote portlet form to the portal markup request service
				$this->_openquoteconnect->submitWSRPForm($userSessionArray);
			}


			// does submit result in redirect?
			if($this->_openquoteconnect->isDownloadAvailable()){
				wp_redirect( $this->_openquoteconnect->getDownloadURL() );
				exit;
			}
					
			
			// get the openquote product form HTML from the portal markup request service
			$openQuotePageHTML = $this->_openquoteconnect->requestWSRPForm($userSessionArray);
			$_SESSION['openquoteUserSessions'] = $userSessionArray;
			if(!empty($openQuotePageHTML) && $openQuotePageHTML!=''){
				$OQHtml = $OQHtml.$openQuotePageHTML;
			}
			else{
				// if an invalid registration, then try reconnecting and reregistering
				if($this->_openquoteconnect->checkLogFaultCode('InvalidRegistration')){
					// log issue
					$this->_checkAndUpdateLog("Product {$productName} OpenQuote server page retrieval failed - automated reregister");
					// rerun this function with no registration id and clear current connection
					$this->_openquoteconnect=null;
					$this->_productdata->wsrpid = "";
					return $this->getQuotePageFlowAfterProductLoad();
				}
			
				$this->_checkAndUpdateLog("Product {$productName} OpenQuote server page retrieval failed");
				$message = $this->_loadUserInformation('OpenQuote Server Page Retrieval Failed');
				if(!$message){return false;}
				return '<div class="openquote4wordpress"><p>'.$message.'</p></div>';
			}


			// final log check to report any unexpected issues if they remain unreported
			$this->_checkLog();

			return '<div class="openquote4wordpress">'.$OQHtml.'</div>';
		}



			
		/**
		 * Method to load product data
		 *
		 * @access	private
		 * @return	boolean	True on success
		 */
		function _loadProductData()
		{
			global $wpdb;
		
			// if no product name then can't load
			if(!($this->_productname)){
				$this->_productdata = null;
				return false;
			}

			// Lets load the content if it doesn't already exist
			if (empty($this->_productdata))
			{
				$__openquotemessagetemplates = $wpdb->prefix.$this->messageTemplatesTableName;
				$__openquoteserver = $wpdb->prefix.$this->serverTableName;
				$__openquoteproduct = $wpdb->prefix.$this->productTableName;

				$loadQuery = " SELECT {$__openquoteproduct}.*, {$__openquoteserver}.*, {$__openquotemessagetemplates}.*"
					. " FROM {$__openquoteproduct}"
					. " LEFT JOIN {$__openquoteserver}"
					. " ON {$__openquoteproduct}.serverId={$__openquoteserver}.id"
					. " LEFT JOIN {$__openquotemessagetemplates}"
					. " ON {$__openquoteserver}.messageTemplatesId={$__openquotemessagetemplates}.id"
					. " WHERE {$__openquoteproduct}.ref = '{$this->_productname}'"
				;

				$this->_productdata = $wpdb->get_row($loadQuery);

				return (boolean) $this->_productdata;
			}
			
			return true;
		}

		
		/**
		 * Method to load user messages
		 *
		 * @access	private
		 * @param infoRef String reference for message
		 * @return message String
		 */
		function _loadUserInformation($infoRef)
		{
			global $wpdb;

			// if no subject then can't load
			if($infoRef==null || empty($infoRef)){
				return 'Page Error';
			}

			$infoText = null;
			$reportAs404 = null;
			$emailAdmin = null;

			// Lets load the text
			$loadQuery = "SELECT infoText, reportAs404, emailAdmin FROM {$wpdb->prefix}{$this->userInformationTableName} WHERE infoRef = '{$infoRef}'";
			$item = $wpdb->get_row($loadQuery);

			if (isset($item)) {
				$infoText = $item->infoText;
				$reportAs404 = $item->reportAs404;
				$emailAdmin = $item->emailAdmin;
			}

			if($emailAdmin){	
				$send = $this->_emailAdmin($infoText);
				if ( $send !== true ) {
					$infoText = $infoText.' (Error sending report email to admin)';
				}
			}			

//			if($reportAs404){		
//				JError::raiseError(404, JText::_($infoText));
//				return false;
//			}

			return $infoText;
		}

		/**
		 * Method to email admin error messages
		 *
		 * @access	private
		 * @param   Message text to send
		 * @return	boolean	True on success
		 * @since	1.5
		 */
		function _emailAdmin($infoText)
		{

			// set recipient as site admin also
			$recipient = get_option( 'admin_email', '' );
			
			// create mail content
			$sitename = get_option( 'blogname', '' );
			$body = "This is an automated email.\n\nAn error has been logged on {$sitename}:\n{$infoText}\n\nFor more information see the component log.";
			$subject = "OpenQuote error on site {$sitename}";

			//send mail (false if error - just ignore)
			$send = wp_mail($recipient, $subject, $body);

			return $send;
		}
	
		/**
		 * Method to save product data
		 *
		 * @access	private
		 * @param	string $registrationHandle
		 * @since	1.5
		 */
		function _saveRegistrationHandle($registrationHandle)
		{
			global $wpdb;
			$id = $this->_productdata->serverId;
			$wpdb->update($wpdb->prefix.$this->serverTableName, array('wsrpid'=>$registrationHandle), array('id'=>$id), array('%s'), array('%d'));
		}
			
		
		/**
		 * Method to read openquote helper log and update component log
		 *
		 * @access	private
		 * @param	string $logDetail
		 */
		function _checkAndUpdateLog($logDetail)
		{

			$logHTML = $this->_openquoteconnect->readLog();
			
			if($logDetail!=null && !empty($logDetail)){
				if($logHTML!=null && !empty($logHTML)){
					$logDetail = $logDetail.'<br/>'.$logHTML;
				}			
			}
			else{
				$logDetail = $logHTML;
			}

			$this->_updateLog($logDetail);
		}

		/**
		 * Method to check for log messages and update log if required
		 *
		 * @access	private
		 */
		function _checkLog()
		{
			$logHTML = $this->_openquoteconnect->readLog();
			$this->_updateLog($logHTML);
		}

		/**
		 * Method to update log
		 *
		 * @access	private
		 * @param	string $logDetail
		 */
		function _updateLog($logDetail)
		{
			global $wpdb;
			
			if($logDetail!=null && !empty($logDetail)){
				$wpdb->insert($wpdb->prefix.$this->logTableName, array('message'=>$logDetail), array('%s'));
			}
		}
		
    }

} //End Class OpenQuoteContentController

