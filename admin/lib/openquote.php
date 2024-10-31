<?php
/*
    Document   : index - a PHP OpenQuote portlet consumer
                 This PHP file is a helper library for integrating OpenQuote products
                 into a website. 

                 This PHP will allow:
                    1. registration with an OpenQuote portal server
                    2. requests for the html for quotation question pages
                    3. submitition of answers back to the server
                    4. deregistration form the server

                 For each of the above 4 process a method held in this PHP is called.
                 Each method follows a similar set of steps:
                    1. get portal server registration handle
                    2. use a XML template for the specific service call required
                    3. populate the XML with the registration handle and any other required data
                    4. create a HTTP connection to the specific portal server service
                    5. pass the XML to the service
                    6. read the response XML from the service
                    7. extract required information from the response
		    
		There is also a method to deregister the web application from the OpenQuote server 

                 The template XML documents (passed in from an external source)
                 used by each of the 4 processes are:
                    1. RegisterWSRPConsumer xml
                    2. RequestWSRPForm xml
                    3. SubmitWSRPForm xml
                    4. DeregisterWSRPConsumer xml

    Created on : Sept 24, 2009
    Last Updated : Dec 05, 2011
    Author     : matthew tomlinson
    Copyright  : Applied Industrial Logic Limited
*/

/**
 * Quote Model
 * @version     1.1
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Applied Industrial Logic Limited
 */
class OpenQuoteConnect 
{
	// OpenQuote server urls used by code in this page, to access a different OpenQuote server edit openQuoteServer String
	var $openQuoteServer = null;
	var $registrationServiceUrl = null;
	var $markupServiceUrl = null;
	
	// This is the consumer name used to register with the server (to obtain a registration handle), this name should be unique to this server
	var $consumerName = null;

	// XML message templates for communicating with the WSRP server	
	var $submitWSRPFormXML = null;
	var $requestWSRPFormXML = null;
	var $registerWSRPConsumerXML = null;
	var $deregisterWSRPConsumerXML = null;
	
	// WSRP registration handle
	var $registrationHandle = null;
	
	// OpenQuote product name
	var $productName = null;
	
	// error log
	var $logHtml = null;
	
	// form submit url
	var $formAction = null;
	
	// users browser details
	var $userAgent = null;
	
	// When a submit results in a download (ie quote doc), this url is populated with location of the document
	var $downloadURL = null;
	
	/**
	 * Initialise OpenQuote consumer object
	 * Depending upon specific use, not all parameters need setting.  
	 * For example if you are deregistering a consumer, only server, consumer name, registration url & xml and registration handle need to be supplied.
	 * @param productName Name of product on OpenQuote server to access
	 * @param openQuoteServer Openquore server URL (ie http://www.appliedindustriallogic.com:8080)
	 * @param registrationServiceUrl WSRP registration path (ie /portal-wsrp/RegistrationService)
	 * @param markupServiceUrl WSRP markup service path (ie /portal-wsrp/MarkupService)
	 * @param consumerName WSRP consumer name - a unique id created by the OpenQuote WSRP consumer calling the server
	 * @param submitWSRPFormXML XML template sending page(form) submit SOAP messages to the OpenQuote WSRP server
	 * @param requestWSRPFormXML XML template sending page request SOAP messages to the OpenQuote WSRP server
	 * @param registerWSRPConsumerXML XML template sending consumer registration SOAP messages to the OpenQuote WSRP server
	 * @param deregisterWSRPConsumerXML XML template sending consumer deregistration SOAP messages to the OpenQuote WSRP server
	 * @param registrationHandle handle specified by OpenQuote WSRP server when registration service called
	 * @param formAction url that any OpenQuote page forms should use for submitting responses to
	 * @param userAgent user agent id (browser type)
	 * @return consumer handle handle specified by OpenQuote WSRP server when registration service called (once obtained, this value is passed into this class using the registrationHandle parameter
	 */
	function initialiser(	$productName,
				$openQuoteServer,
				$registrationServiceUrl,
				$markupServiceUrl,
				$consumerName,
				$submitWSRPFormXML,
				$requestWSRPFormXML,
				$registerWSRPConsumerXML,
				$deregisterWSRPConsumerXML,
				$registrationHandle,
				$formAction,
				$userAgent)
	{
		$this->productName = $productName;
		$this->openQuoteServer = $openQuoteServer;
		$this->registrationServiceUrl = $openQuoteServer . $registrationServiceUrl;
		$this->markupServiceUrl = $openQuoteServer . $markupServiceUrl;
		$this->consumerName = $consumerName;
		$this->submitWSRPFormXML = simplexml_load_string($submitWSRPFormXML);
		$this->requestWSRPFormXML = simplexml_load_string($requestWSRPFormXML);
		$this->registerWSRPConsumerXML = simplexml_load_string($registerWSRPConsumerXML);
		$this->deregisterWSRPConsumerXML = simplexml_load_string($deregisterWSRPConsumerXML);
		$this->registrationHandle = $registrationHandle;
		$this->formAction = $formAction;
		$this->userAgent = $userAgent;
		
		// get registration handle if one is not passed in
		if(empty($registrationHandle)){
			$this->registrationHandle = $this->getRegistrationHandle();
		}
		
		// return registration handle for persisting if required
		return $this->registrationHandle;
	}

    /**
     * Register the WSRP Consumer and return the consumer handle
     * This method only needs to be called if no registration handle exists, and is called in the inialisation method if required.
     * If one is not found, it means this web app has not yet registered itself with the portal server and so
     * contacts the server to register.  The server will return a registration handle which this
     * method then stores in an application variable.
     * @return consumer handle
     */
    function getRegistrationHandle(){

		// if no handle exists, register consumer to get one
		if (empty($this->registrationHandle)) {
				
			// use consumer registration xml template 
			$this->registerWSRPConsumerXML->registerXPathNamespace('ns1', 'urn:oasis:names:tc:wsrp:v1:types');

			// apply the consumer name to the consumerregistration xml template
			$registrationHandleElements = $this->registerWSRPConsumerXML->xpath('//ns1:register');
			$registrationHandleElements[0]->consumerName = $this->consumerName;

			// register consumer by posting xml to OpenQuotes portal server registration service
			$soapReturn = $this->postMessage($this->registrationServiceUrl, $this->registerWSRPConsumerXML);
			$soapReturnXML = $soapReturn['xml'];

			if($soapReturnXML!=null){
				$soapReturnXML->registerXPathNamespace('ns1', 'urn:oasis:names:tc:wsrp:v1:types');

				// extract and return consumer handle from the registration service response (XML) <ns1:registerResponse><ns1:registrationHandle>
				$registrationHandleElements = $soapReturnXML->xpath('//ns1:registerResponse/ns1:registrationHandle/text()');

				if(!isset($registrationHandleElements[0])) {
					$this->registrationHandle="";
					$this->writeLog( "GET REGISTRATION HANDLE ERROR: No registration handle from {$this->registrationServiceUrl}");
//			    		$this->writeLog( "<code>".$soapReturnXML->asXML()."</code>");
				}
				else{
					$this->registrationHandle=(string) $registrationHandleElements[0];
				}
			}
			else{
				$this->registrationHandle='';
				$this->writeLog( "GET REGISTRATION HANDLE ERROR: No Soap XML Message returned from {$this->registrationServiceUrl}");
			}

		}
	
        // return the registration handle to the caller
        return $this->registrationHandle;
    }

    /**
     * Deregister the WSRP Consumer
     * This method deregisters this web application from the OpenQuote  portal server if currently registered
     */
    function deregisterConsumer() {

        // if a registration handle exists, deregister this web application with the OpenQuote portlet server
        if (!empty($this->registrationHandle)) {

            // use consumer deregistration xml template 
			$this->deregisterWSRPConsumerXML->registerXPathNamespace('ns1', 'urn:oasis:names:tc:wsrp:v1:types');

            // apply the registration handle to the consumer deregistration xml template
            $registrationHandleElements = $this->deregisterWSRPConsumerXML->xpath('//ns1:deregister');
            $registrationHandleElements[0]->registrationHandle = $this->registrationHandle;

            // deregister consumer by posting xml to OpenQuotes portal server registration service
            $soapReturn = $this->postMessage($this->registrationServiceUrl, $this->deregisterWSRPConsumerXML);
            $soapReturnXML = $soapReturn['xml'];

            // finally, for neatness, clear the now redundent application variable holding the old registration handle
			$this->registrationHandle==null;
			
        }

    }

    /**
     * Request a WSRP form from the producer
     * This service returns the HTML genterated for the OpenQuote quotation pages
     * @param userSessionArray user session array
     * @return quotation portlet HTML
     */
    function requestWSRPForm(&$userSessionArray) {

        // get sessionID for product from php session variable
		$sessionID = null;
		if (isset($userSessionArray[$this->productName . '.ProductSessionID'])) {
			$sessionID = $userSessionArray[$this->productName . '.ProductSessionID'];
		}

        // use request markup xml template 
		$this->requestWSRPFormXML->registerXPathNamespace('ns1', 'urn:oasis:names:tc:wsrp:v1:types');

        // apply the registration handle to the request markup xml template
        $registrationHandleElements = $this->requestWSRPFormXML->xpath('//ns1:registrationContext');
        $registrationHandleElements[0]->registrationHandle = $this->registrationHandle;

        // apply the user browser agent to the request markup xml template
        $userAgentElements = $this->requestWSRPFormXML->xpath('//ns1:clientData');
        $userAgentElements[0]->userAgent = $this->userAgent;

        // connect to portal markup service - this is done seperately to give access to request and response header parameters
        $headers = array();

        // set openquote product request property up so correct product HTML is returned
        $headers['openquote.product']=$this->productName;

        // set user session id request property up (if one exists) so correct stage in the quotation process is returned
        // if one doesn't exist, the quotation process will start from the start, and a new session id will be generated and returned in the response
        if (!empty($sessionID)) {
           $headers['Cookie']=$sessionID;
        }

		$markupHTML=null;
	
        // get the quotation HTML by posting xml to OpenQuotes portal server markup service
        $soapReturn = $this->postMessage($this->markupServiceUrl, $this->requestWSRPFormXML, $headers);
		$soapReturnXML = $soapReturn['xml'];
		if($soapReturnXML!=null){
			$soapReturnXML->registerXPathNamespace('ns1', 'urn:oasis:names:tc:wsrp:v1:types');
		
			// get sessionID from response header if required (ie was not previously set)
			if (empty($sessionID)) {
				$soapReturnHeader = $soapReturn['header'];
				// get the response header field containing the session id
				$sessionID = $soapReturnHeader['Set-Cookie'];
				// extract the session id part (the Set-Cookie value looks like this 'JSESSIONID=AF9AA35D62ADBDC7E71DB0A03C04E710; Path=/')
				$start = stripos($sessionID, 'JSESSIONID');
				$end = stripos($sessionID, ';', $start);
				$sessionID = substr($sessionID, $start, $end-$start);
				// set a session variable to hold the new session id for later reuse
				$userSessionArray[$this->productName . '.ProductSessionID'] = $sessionID;
			}
		
			// extract the portlet quotation page HTML from the xml returned by the markup service			
			$markupHTMLElements = $soapReturnXML->xpath('//ns1:markupContext/ns1:markupString/text()');
			$markupHTML = null;
			if (isset($markupHTMLElements[0])) {
				$markupHTML=(String) $markupHTMLElements[0];
				// make certain that the form action in the HTML is pointing to this page by overwriting the default portal action
				// with this page's details this will ensure all submitted question pages will get sent back to this page
				// (old version of code keeping get params)$markupHTML = str_replace("action='wsrp_rewrite?", "action='".$this->formAction, $markupHTML);
				$startPos = stripos($markupHTML, "action='wsrp_rewrite");
				$startPos = stripos($markupHTML, "'", $startPos)+1;
				$endPos = stripos($markupHTML, "'", $startPos);
				$markupHTML = substr_replace($markupHTML,$this->formAction,$startPos,$endPos-$startPos); 	
				// update all src urls (images, javascript files etc) in the HTML so they point to the full openquote server url
				$markupHTML = str_replace("src='/quotation/", "src='{$this->openQuoteServer}/quotation/", $markupHTML) ;
			}
		
		}
		else{
			$this->registrationHandle='';
			$this->writeLog( "GET PAGE MARKUP ERROR: No Soap XML Message returned from {$this->markupServiceUrl}");
		}

        // return the quotation portal HTML to the caller
        return $markupHTML;

    }

    /**
     * Submit a WSRP form to the producer
     * This service submits all form fields entered in the quotation portal part of the web page to OpenQuote server for validation
     * @param userSessionArray user session array
     */
    function submitWSRPForm(&$userSessionArray) {

        // get sessionID for product from php session variable
		$sessionID = null;
		if (isset($userSessionArray[$this->productName . '.ProductSessionID'])) {
			$sessionID = $userSessionArray[$this->productName . '.ProductSessionID'];
		}

        // use submit form xml template 
		$this->submitWSRPFormXML->registerXPathNamespace('ns1', 'urn:oasis:names:tc:wsrp:v1:types');

        // apply the registration handle to the submit form xml template
        $registrationHandleElements = $this->submitWSRPFormXML->xpath('//ns1:registrationContext');
        $registrationHandleElements[0]->registrationHandle =$this->registrationHandle;

        // apply the user browser agent to the request markup xml template
        $userAgentElements = $this->submitWSRPFormXML->xpath('//ns1:clientData');
        $userAgentElements[0]->userAgent = $this->userAgent;

        // add the submitted fields from the openquote portal form to the submit form xml template
        // each field should be added to the interactionParams element in the following structure:
        // <ns1:interactionParams>
        //   <ns1:formParameters name='fieldName1'><ns1:value>enteredValue1</ns1:value></ns1:formParameters>
        //   <ns1:formParameters name='fieldName1'><ns1:value>enteredValue1</ns1:value></ns1:formParameters>
        // </ns1:interactionParams>
        // firstly get each of the names of submitted fields and thier values
		// Due to the way the _REQUEST array works with form field, it cannot handle
		// wsrp form fields correctly as the field names contact square brackets.
		// Instead we use the raw request contents using the  file_get_contents command,
		// and extract each field and value inturn.
		$cont = file_get_contents('php://input');
		$lines = explode('&',$cont);
		foreach ($lines as $line) {
			$parts = explode('=',$line,2);
			$fieldName = urldecode($parts[0]);
			$fieldValue = urldecode($parts[1]);

            // if the submitted field value is not null, add it to the submit form xml
            if (!is_null($fieldValue)) {
                // get the xml element to which all submitted values are added
                $interactionParamsElements = $this->submitWSRPFormXML->xpath('//ns1:interactionParams');
                // add a formParameter element to the interactionParams element on submit form xml
                $formParametersElement = $interactionParamsElements[0]->addChild('formParameters');
                // add a name attribute equal to the field name to the new formParameter element
                $formParametersElement->addAttribute('name',$fieldName);
                // add a value element formParameter element
                // set the value element with a text content equal to the submitted field value
                $formParametersElement->addChild('value',$fieldValue);
            }
        }

        // set openquote product request property up so correct product HTML is returned
        $headers = array();
        $headers['openquote.product']=$this->productName;

        // set user session id request property up (if one exists) so correct stage in the quotation process is returned
        // if one doesn't exist, the quotation process will start from the start, and a new session id will be generated and returned in the response
        if (!empty($sessionID)) {
           $headers['Cookie']=$sessionID;
        }

        // post submitted form xml to OpenQuotes portal server markup service
        $soapReturn = $this->postMessage($this->markupServiceUrl, $this->submitWSRPFormXML, $headers);
        $soapReturnXML = $soapReturn['xml'];

		// if download to trigger, set download URL
		$this->downloadURL = null;
		if($soapReturnXML!=null){
			$soapReturnXML->registerXPathNamespace('ns1', 'urn:oasis:names:tc:wsrp:v1:types');
				
			// extract the portlet quotation page HTML from the xml returned by the markup service			
			$markupRedirectElement = $soapReturnXML->xpath('//ns1:redirectURL/text()');
			if (isset($markupRedirectElement[0])) {
				$this->downloadURL=(String) $markupRedirectElement[0];
			}
		
		}


    }
	
	
    /**
     * Check to see if download available
     * @return false if no download
     */
    function isDownloadAvailable() {
		$f = fopen("openquoteresponse_Submit_return_redirect.txt", "wb");
		fwrite($f, $this->openQuoteServer.$this->downloadURL);
		fclose($f);
		return $this->downloadURL;
	}
	
    /**
     * Check to see if download available
     * @return false if no download
     */
    function getDownloadURL() {
		return $this->openQuoteServer.$this->downloadURL;
	}
	
    /**
     * Post an xml message to a specified HTTP connection and read the response (ie contact a portal sevice)
     * @param markupServiceUrl url to connect to
     * @param xmlToSend xml message document to post to connection
     * @param headers header parameters
     * @return response header and xml in array
     */
    function postMessage($markupServiceUrl, $xmlToSend, $headers=null) {

        if(!isset($headers)){
            $headers = array();
        }

		// split the url into its component parts (host, port etc)
        $urlParts = parse_url($markupServiceUrl);

        // convert the xml document to a string and write it to the HTTP connection.
        $xmlString = $xmlToSend->asXML();

		// empty string ready to write response to
        $response = '';

		// open connection with service
		$fp = null;
		if (empty($urlParts['port'])){
			$fp = fsockopen($urlParts['host']);
		}
		else{
			$fp = fsockopen($urlParts['host'], $urlParts['port']);
		}		
        if ($fp) {
			// create header to post
			$out = "POST {$markupServiceUrl} HTTP/1.1\r\n";
			$out .= "Host: {$urlParts['host']}\r\n";
			$out .= "Accept: text/xml\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Type: text/xml\r\n";
			foreach($headers as $headerName => $headerValue){
				// if the header value is not null, add it to the request
				if (!is_null($headerValue)) {
					$out .= "{$headerName}: {$headerValue}\r\n";
				}
			}
			$out .= 'Content-Length: ' . strlen($xmlString) . "\r\n";
			$out .= "\r\n";

			// add xml for posting
			$out .= $xmlString;
			// post header and xml to service
            fwrite($fp, $out);

			// read service response
            while (!feof($fp)) {
                $response .= fgets($fp, 128);
            }

			// close connection
            fclose($fp);
        }
        else{
            $this->writeLog( "POST MESSAGE ERROR: no connection to {$markupServiceUrl}");
        }
		
		$resultXML = null;
		
        // read the response from the connection
        $responseHeaders = array();
        // for each line in the response get the headers, then finish when the xml is located
		$lines = explode("\n",$response);
		for ($i=0; $i<count($lines); $i++) {
			$line = $lines[$i];

			// split line into two parts to extract header parameter name and values
			$parts = explode(': ',$line, 2);
            if(count($parts) == 2) {
                // 2 parts (line contains a colon) means it is a header line
				$responseHeaders[$parts[0]] = chop($parts[1]);
			}

			if($i>0 && (count($parts) == 1)){
				// just one part means the line is the spacer line between the header lines and the body
				// so extract the xml (body) from the end of the response and exit the for loop
				$lines = explode("\n",$response,$i+2);
				$resultXML = $lines[$i+1];
				$i=$i+2;
			}
        }

		// trim returned string - some wsrp portals return extra unused characters outside the soap xml
		$startXML = strpos($resultXML, '<');if($startXML===false) $startXML=0;
		$endXML = strrpos($resultXML, '>');if($endXML===false or $endXML<$startXML) $endXML=strlen($resultXML);
		$resultXML = substr ( $resultXML, $startXML, $endXML - $startXML + 1 );

        // xml and headers to return
        $soapReturn['xml']=simplexml_load_string($resultXML);
        $soapReturn['header']=$responseHeaders;

        // check result xml for a soap fault (error)
        $this->checkForSoapFault($soapReturn['xml']);

        // return the response xml document
        return $soapReturn;
    }

    /** 
	 * Report error details of a soap fault response
     * @param SimpleXML $soapResponseXML
     **/
    function checkForSoapFault($soapResponseXML){

		if($soapResponseXML==null){
			return;
		}
	    
        // check for soap fault
		$soapResponseXML->registerXPathNamespace('env', 'http://schemas.xmlsoap.org/soap/envelope/');
        $faultElements = $soapResponseXML->xpath('//env:Fault');
        if ($faultElements!=FALSE) {
            // if soap fault found
			$faultCode='';
			$faultString='';
			$faultActor='';

            // extract the faultcode, faultstring and faultactor from the service response (XML)

            $faultCodeElements = $faultElements[0]->xpath('//faultcode/text()');
			if(isset($faultCodeElements[0])){
                $faultCode=(string) $faultCodeElements[0];
            }

            $faultStringElements = $faultElements[0]->xpath('//faultstring/text()');
			if(isset($faultStringElements[0])){
                $faultString=(string) $faultStringElements[0];
            }

            $faultActorElements = $faultElements[0]->xpath('//faultactor/text()');
			if(isset($faultActorElements[0])){
                $faultActor=(string) $faultActorElements[0];
            }

            // output error details to page
            $this->writeLog( 'SOAP ERROR REPORTED');
            $this->writeLog( " faultCode: {$faultCode}");
            $this->writeLog( " faultString: {$faultString}");
            $this->writeLog( " faultActor: {$faultActor}");
        }

    }

    /** 
	 * add log error details
     * @param string $text
     **/
    function writeLog($text){
		if(!empty($text)){
			if(empty($this->logHtml)){
				 $this->logHtml = $text;
			}
			else{
			 $this->logHtml = $this->logHtml.'<br/>'.$text;
			}    
		}
    }
    
    /** 
	 * get log error details
     * @return string log html
     **/
     function readLog(){
	     $html = $this->logHtml;
	     $this->logHtml = null;
	     return $html;
     }

    
    /** 
	 * Check log error details
     * @param string compare text
     * @return boolean match found
     **/
     function checkLogFaultCode($code){
	     $html = $this->logHtml;
		 $startPos = stripos($html, 'faultCode:');
		 if(!$startPos){
			return false;
		 }
		 $endPos = stripos($html, '<br/>', $startPos);
		 if(!$endPos){
			$endPos = strlen($html);
		 }
		 $faultCode = substr($html,$startPos,$endPos-$startPos); 	
	     $codeFound = substr_count($faultCode, $code)>0;
		 return $codeFound;
     }
	 
	 
}
?>
