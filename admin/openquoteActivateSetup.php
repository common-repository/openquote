<?php		
/**
 * OpenQuote Activate Setup
 * sets up tables if required when component activated, and deregisters from OpenQuote servers when deactivated
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

require_once(dirname(__FILE__).'/openquoteAdminController.php' );


// table versions
global $openquoteproduct_db_version;
global $openquoteserver_db_version;
global $openquotemessagetemplates_db_version;
global $openquoteuserinformation_db_version;
global $openquotelog_db_version;
$openquoteproduct_db_version = '1.0';
$openquoteserver_db_version = '1.1';
$openquotemessagetemplates_db_version = '1.0';
$openquoteuserinformation_db_version = '1.1';
$openquotelog_db_version = '1.0';

// table names
global $openquoteproduct_table_name;
global $openquoteserver_table_name;
global $openquotemessagetemplates_table_name;
global $openquoteuserinformation_table_name;
global $openquotelog_table_name;
$openquoteproduct_table_name = '__openquoteproduct';
$openquoteserver_table_name = '__openquoteserver';
$openquotemessagetemplates_table_name = '__openquotemessagetemplates';
$openquoteuserinformation_table_name = '__openquoteuserinformation';
$openquotelog_table_name = '__openquotelog';

/**
 * Install openquote tables
 */
function openquote_db_install ($versionOptionName, $newVersionNumber, $table_name, $sqlFields) {
    global $wpdb;
	
	// check if new or updated table required
	$new_table_required = ($wpdb->get_var("show tables like '{$table_name}'") != $table_name);
	$installed_ver = get_option( $versionOptionName );
	$update_table_required = !$new_table_required && ( $installed_ver != $newVersionNumber );
	
	// if new or update table required
	if($new_table_required || $update_table_required) {

		// OpenQuote server table structure
		$sql = "CREATE TABLE " . $table_name . " " . $sqlFields;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		// set table version number for future update use
		update_option( $versionOptionName, $newVersionNumber );
		
	}
	
	return $new_table_required;

}

/**
 * Install openquote product table and content
 * OpenQuote products to be available to via component
 */
function openquoteproduct_db_install () {
	global $wpdb;
	global $openquoteproduct_db_version;
	global $openquoteproduct_table_name;
	
	$table_name = $wpdb->prefix . $openquoteproduct_table_name;

	$sqlFields = "(
		`id` int(11) NOT NULL auto_increment,
		`ref` varchar(255) NOT NULL,
		`serverId` int(11) NOT NULL,
		`productName` varchar(255) NOT NULL,
		`published` tinyint(1) NOT NULL,
		PRIMARY KEY (`id`)
		);";

	$new_table_required = openquote_db_install ('openquoteproduct_db_version', $openquoteproduct_db_version, $table_name, $sqlFields);

	// if table does not currently exist add initial data
	if($new_table_required) {
		// Some of the servers on the OpenQuote demo server to get you started     
		$rows_affected = $wpdb->insert( $table_name, array( 'ref' => 'Life Plus Demo', 'serverId' => 1, 'productName' => 'AIL.Demo.LifePlus', 'published' => 1 ) );
		$rows_affected = $wpdb->insert( $table_name, array( 'ref' => 'Equine Plus Demo', 'serverId' => 1, 'productName' => 'AIL.Demo.EquinePlus', 'published' => 1 ) );
		$rows_affected = $wpdb->insert( $table_name, array( 'ref' => 'Motor Plus Demo', 'serverId' => 1, 'productName' => 'AIL.Demo.MotorPlus', 'published' => 1 ) );
		$rows_affected = $wpdb->insert( $table_name, array( 'ref' => 'Freight Plus Demo', 'serverId' => 1, 'productName' => 'AIL.Demo.FreightPlus', 'published' => 1 ) );
		$rows_affected = $wpdb->insert( $table_name, array( 'ref' => 'Broker Plus Demo', 'serverId' => 1, 'productName' => 'AIL.Demo.IrishBrokerPI', 'published' => 1 ) );
	}

}

/**
 * Install openquote server table and content
 * OpenQuote servers available to component
 */
function openquoteserver_db_install () {
	global $wpdb;
	global $openquoteserver_db_version;
	global $openquoteserver_table_name;

	$table_name = $wpdb->prefix . $openquoteserver_table_name;
	
	$sqlFields = "(
		`id` int(11) NOT NULL auto_increment,
		`openQuoteServer` varchar(255) NOT NULL,
		`registrationServiceUrl` varchar(255) NOT NULL,
		`markupServiceUrl` varchar(255) NOT NULL,
		`consumerName` varchar(50) NOT NULL,
		`wsrpid` varchar(25),
		`messageTemplatesId` int(11) NOT NULL,
		PRIMARY KEY (`id`)
		);";

	// hostname is used in an attempt to make the server consumer names unique to this server
	$hostName = $_SERVER['HTTP_HOST'];

	$new_table_required = openquote_db_install ('openquoteserver_db_version', $openquoteserver_db_version, $table_name, $sqlFields);

	// if table does not currently exist add initial data
	if($new_table_required) {

		// The OpenQuote demo server to get you started
		$rows_affected = $wpdb->insert( $table_name, array( 'id' => 1, 'openQuoteServer' => 'http://ec2-46-137-2-77.eu-west-1.compute.amazonaws.com:80', 'registrationServiceUrl' => '/portal-wsrp/RegistrationService', 'markupServiceUrl' => '/portal-wsrp/MarkupService', 'consumerName' => "{$hostName}_WordPressOQConsumer", 'messageTemplatesId' => 1 ) );
		
		// A local server (should you have a local instance of OpenQuote running)
		$rows_affected = $wpdb->insert( $table_name, array( 'id' => 2, 'openQuoteServer' => 'http://localhost:8080', 'registrationServiceUrl' => '/portal-wsrp/RegistrationService', 'markupServiceUrl' => '/portal-wsrp/MarkupService', 'consumerName' => "{$hostName}_WordPressOQConsumer", 'messageTemplatesId' => 1 ) );
	}

}

/**
 * Install openquote message templates table and content
 * wsrp xml message templates to be used for server communication
 */
function openquotemessagetemplates_db_install () {
	global $wpdb;
	global $openquotemessagetemplates_db_version;
	global $openquotemessagetemplates_table_name;

	$table_name = $wpdb->prefix . $openquotemessagetemplates_table_name;
	
	$sqlFields = "(
		`id` int(11) NOT NULL auto_increment,
		`templatesRef` varchar(25) NOT NULL,
		`submitWSRPFormXML` text NOT NULL,
		`requestWSRPFormXML` text NOT NULL,
		`registerWSRPConsumerXML` text NOT NULL,
		`deregisterWSRPConsumerXML` text NOT NULL,
		PRIMARY KEY (`id`)
		);";
		
	$new_table_required = openquote_db_install ('openquotemessagetemplates_db_version', $openquotemessagetemplates_db_version, $table_name, $sqlFields);

	// if table does not currently exist add initial data
	if($new_table_required) {

		// default wsrp xml message templates - should be ok for any server
		$rows_affected = $wpdb->insert( $table_name, array( 'id' => 1, 
															'templatesRef' => 'Default', 
															'submitWSRPFormXML' => '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/">
   <env:Header/>
   <env:Body>
      <ns1:performBlockingInteraction xmlns:ns1="urn:oasis:names:tc:wsrp:v1:types" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
         <ns1:registrationContext>
            <ns1:registrationHandle></ns1:registrationHandle>
         </ns1:registrationContext>
         <ns1:portletContext>
            <ns1:portletHandle>quotation.WSRPQuotationPortlet</ns1:portletHandle>
         </ns1:portletContext>
         <ns1:runtimeContext>
            <ns1:userAuthentication>wsrp:none</ns1:userAuthentication>
            <ns1:portletInstanceKey>portletinstance</ns1:portletInstanceKey>
            <ns1:namespacePrefix>portlet_space</ns1:namespacePrefix>
         </ns1:runtimeContext>
         <ns1:userContext xsi:nil="1"/>
         <ns1:markupParams>
            <ns1:secureClientCommunication>false</ns1:secureClientCommunication>
            <ns1:locales>en-GB</ns1:locales>
            <ns1:mimeTypes>text/html</ns1:mimeTypes>
            <ns1:mode>wsrp:view</ns1:mode>
            <ns1:windowState>wsrp:normal</ns1:windowState>
            <ns1:clientData>
               <ns1:userAgent></ns1:userAgent>
            </ns1:clientData>
         </ns1:markupParams>
         <ns1:interactionParams>
            <ns1:portletStateChange>cloneBeforeWrite</ns1:portletStateChange>
         </ns1:interactionParams>
      </ns1:performBlockingInteraction>
   </env:Body>
</env:Envelope>', 
															'requestWSRPFormXML' => '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/">
   <env:Header />
   <env:Body>
      <ns1:getMarkup xmlns:ns1="urn:oasis:names:tc:wsrp:v1:types" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
         <ns1:registrationContext>
            <ns1:registrationHandle></ns1:registrationHandle>
         </ns1:registrationContext>
         <ns1:portletContext>
            <ns1:portletHandle>quotation.WSRPQuotationPortlet</ns1:portletHandle>
         </ns1:portletContext>
         <ns1:runtimeContext>
            <ns1:userAuthentication>wsrp:none</ns1:userAuthentication>
            <ns1:portletInstanceKey>portletinstance</ns1:portletInstanceKey>
            <ns1:namespacePrefix>portlet_space</ns1:namespacePrefix>
         </ns1:runtimeContext>
         <ns1:userContext xsi:nil="1" />
         <ns1:markupParams>
            <ns1:secureClientCommunication>false</ns1:secureClientCommunication>
            <ns1:locales>en-GB</ns1:locales>
            <ns1:mimeTypes>text/html</ns1:mimeTypes>
            <ns1:mode>wsrp:view</ns1:mode>
            <ns1:windowState>wsrp:normal</ns1:windowState>
            <ns1:clientData>
               <ns1:userAgent></ns1:userAgent>
            </ns1:clientData>
         </ns1:markupParams>
      </ns1:getMarkup>
   </env:Body>
</env:Envelope>', 
															'registerWSRPConsumerXML' => '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/">
   <env:Header />
   <env:Body>
      <ns1:register xmlns:ns1="urn:oasis:names:tc:wsrp:v1:types" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
         <ns1:consumerName></ns1:consumerName>
         <ns1:consumerAgent>JoomalPHP.1.0</ns1:consumerAgent>
         <ns1:methodGetSupported>false</ns1:methodGetSupported>
      </ns1:register>
   </env:Body>
</env:Envelope>', 
															'deregisterWSRPConsumerXML' => '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:oasis:names:tc:wsrp:v1:types">
   <env:Header/>
   <env:Body>
      <ns1:deregister>
         <ns1:registrationHandle></ns1:registrationHandle>
      </ns1:deregister>
   </env:Body>
</env:Envelope>'
 ) );

	}

}


/**
 * Install openquote user information table and content
 * HTML messages to be display when the normal quote process does not work
 */
function openquoteuserinformation_db_install () {
	global $wpdb;
	global $openquoteuserinformation_db_version;
	global $openquoteuserinformation_table_name;

	$table_name = $wpdb->prefix . $openquoteuserinformation_table_name;
	
	$sqlFields = "(
		`id` int(11) NOT NULL auto_increment,
		`infoRef` varchar(255) NOT NULL,
		`infoText` text NOT NULL,
		`reportAs404` tinyint(1) NOT NULL,
		`emailAdmin` tinyint(1) NOT NULL,
		PRIMARY KEY (`id`)
		);";

	$new_table_required = openquote_db_install ('openquoteuserinformation_db_version', $openquoteuserinformation_db_version, $table_name, $sqlFields);

	// if table does not currently exist add initial data
	if($new_table_required) {

		// Default text for error message and user information
		$rows_affected = $wpdb->insert( $table_name, array( 'infoRef' => 'No Product Published', 'infoText' => 'Product configuration resource not published on web site. Please contact site administrator for more details.', 'reportAs404' => 0, 'emailAdmin' => 0 ) );
		$rows_affected = $wpdb->insert( $table_name, array( 'infoRef' => 'No Product Config', 'infoText' => 'Product configuration resource not found on web server. Please contact site administrator for more details.', 'reportAs404' => 0, 'emailAdmin' => 0 ) );
		$rows_affected = $wpdb->insert( $table_name, array( 'infoRef' => 'OpenQuote Server Reg Failed', 'infoText' => 'Could not successfully register with OpenQuote server. Please contact site administrator for more details.', 'reportAs404' => 0, 'emailAdmin' => 0 ) );
		$rows_affected = $wpdb->insert( $table_name, array( 'infoRef' => 'OpenQuote Server Page Retrieval Failed', 'infoText' => 'Could not successfully retrieve a page from the OpenQuote server. Please contact site administrator for more details.', 'reportAs404' => 0, 'emailAdmin' => 0 ) );

	}

}


/**
 * Install openquote log table
 * Log for the quote process
 */
function openquotelog_db_install () {
	global $wpdb;
	global $openquotelog_db_version;
	global $openquotelog_table_name;

	$table_name = $wpdb->prefix . $openquotelog_table_name;
	
	$sqlFields = "(
		`id` int(11) NOT NULL auto_increment,
		`time` timestamp NOT NULL DEFAULT now(),
		`message` text NOT NULL,
		PRIMARY KEY (`id`)
		);";

	$new_table_required = openquote_db_install ('openquotelog_db_version', $openquotelog_db_version, $table_name, $sqlFields);

}


/**
 * Deregister all servers
 * Log for the quote process
 */
function openquote_serverdeactivate(){
	global $wpdb;
	global $openquoteserver_table_name;

	$listQuery = 'SELECT * FROM '.$wpdb->prefix . $openquoteserver_table_name;
	$rows = $wpdb->get_results($listQuery);
	$ids = array();

	for ($i=0; $i<count($rows); $i++){
		$ids[$i] = $rows[$i]->id;
	} 

	if (class_exists('OpenQuoteAdminController')) {
		$openquoteAdminController = new OpenQuoteAdminController();
		$openquoteAdminController->deregister($ids);
	}
}		

?>