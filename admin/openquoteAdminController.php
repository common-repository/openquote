<?php
/**
 * OpenQuote WordPress WSRP consumer admin
 * @package OpenQuote
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

// load 3rd party classes
require_once(dirname(__FILE__).'/lib/pagination.class.php' );
require_once(dirname(__FILE__).'/lib/openquote.php' );


if (!class_exists('OpenQuoteAdminController')) {

    class OpenQuoteAdminController {
			
	    /**
		 * table names
		 */
		var $logTableName = '__openquotelog';
		var $messageTemplatesTableName = '__openquotemessagetemplates';
		var $userInformationTableName = '__openquoteuserinformation';
		var $serverTableName = '__openquoteserver';
		var $productTableName = '__openquoteproduct';
		
	    /**
		 * Pagniation Class
		 * @var object
		 */
		var $pagination;

		/**
		 * OpeenQuote Connection Class
		 * @var object
		 */
		var $_openquoteconnect = null;

	
        function OpenQuoteAdminController() { //constructor
        }

		
		/**
		 * Display OpenQuote Admin Page
		 */
        function printOpenQuoteAdminPage() {
?><div class="wrap"><?php
			// list of admin tabs
			$titles = array('OpenQuote', 'Products', 'Servers', 'Messages', 'Display', 'Log', 'Styling', 'Help');
			// get current selected tab
			$admintab = '';
			if(isset($_GET['admintab'])){
				$admintab = $_GET['admintab'];
			}
			if (!in_array($admintab, $titles)){
				$admintab = 'OpenQuote';
			}
			// display tabs
			$this->printOpenQuoteAdminTabsPage($titles,$admintab);
			// display tab content
			$functionName = "printOpenQuote{$admintab}AdminPage";
			$this->$functionName($mode,$record);
?></div><?php
		}

		
		/**
		 * Display OpenQuote Admin Page
		 */
        function printOpenQuoteAdminTabsPage($titles,$currentTitle='') {
?>		
<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php
			foreach ($titles as $title) {
?><a href="admin.php?page=openquote.php&admintab=<?php echo $title; ?>" class="nav-tab <?php if($title==$currentTitle){ ?>nav-tab-active<?php } ?>"><?php echo $title; ?></a><?php 
			}
?></h2>
<?php		
		}
			

		/**
		 * Display OpenQuote Main Admin Tab
		 */
        function printOpenQuoteOpenQuoteAdminPage() {
			include('openquoteAdmin.php');
		}


		/**
		 * Display OpenQuote Products Admin Page
		 */
        function printOpenQuoteProductsAdminPage() {
			global $wpdb;
		
			$tableName = $this->productTableName;
			$serverTableName = $this->serverTableName;
			$target = 'admin.php?page=openquote.php&admintab=Products';
			
			// what action has been requested?
			$mode = '';
			$records = array();
			$this->getListActions($mode, $records);

			// delete records
			if($mode=='delete'){
				// if delete confirmed by user
				if(isset($_POST['actionconfirm'])){
					$this->delete($records,$tableName);
				}
				// if delete not yet confirmed or cancelled by users
				elseif(!isset($_POST['actioncancel'])){
					$references = array();
					foreach ($records as $record) {
						$loadQuery = "SELECT ref FROM {$wpdb->prefix}{$tableName} WHERE id = {$record}";
						$item = $wpdb->get_row($loadQuery);
						$references[$record] = $item->ref;
					}
					include('openquoteActionConfirmation.php');
					return;
				}
			}

			// copy records
			if($mode=='copy'){
				foreach ($records as $id){
					$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$id}";
					$row = $wpdb->get_row($loadQuery);
					$ref = $row->ref;
					$productName = $row->productName;
					$published = $row->published;
					$serverId = $row->serverId;
					$wpdb->insert($wpdb->prefix.$tableName, array('ref'=>$ref, 'productName'=>$productName, 'published'=>$published, 'serverId'=>$serverId), array('%s','%s','%d','%d'));
				} 
			}
			
			if($mode=='publish' || $mode=='unpublish'){
				$this->publishProduct($records,$tableName,$mode=='publish');
			}

			// save records
			if($mode=='save'){
				$saveAction = $_POST['saveaction'];
				if($saveAction=='edit'){
					// Edit
					$id = $_POST['id'];
					$ref = $_POST['ref'];
					$productName = $_POST['productName'];
					$published = $_POST['published'];
					$serverId = $_POST['serverId'];
					$wpdb->update($wpdb->prefix.$tableName, array('ref'=>$ref, 'productName'=>$productName, 'published'=>$published, 'serverId'=>$serverId), array('id'=>$id), array('%s','%s','%d','%d'), array('%d'));
				}
				else{
					// New
					$ref = $_POST['ref'];
					$productName = $_POST['productName'];
					$published = $_POST['published'];
					$serverId = $_POST['serverId'];
					$wpdb->insert($wpdb->prefix.$tableName, array('ref'=>$ref, 'productName'=>$productName, 'published'=>$published, 'serverId'=>$serverId), array('%s','%s','%d','%d'));
				}
			}			
			
			// edit
			if($mode=='edit' || $mode=='new'){
				$row = null;
				if($mode=='edit'){
					if(isset($records[0])){
						$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$records[0]}";
						$row = $wpdb->get_row($loadQuery);
					}
				}
				
				$serverListQuery = "SELECT id,openQuoteServer FROM {$wpdb->prefix}{$serverTableName}";
				$serverRows = $wpdb->get_results($serverListQuery);

				$serverListTarget = 'admin.php?page=openquote.php&admintab=Servers';
				include('openquoteProduct.php');
			}
			// list
			else{
				$listQuery = "SELECT {$wpdb->prefix}{$tableName}.*, {$wpdb->prefix}{$serverTableName}.openQuoteServer"
							. " FROM {$wpdb->prefix}{$tableName}"
							. " LEFT JOIN {$wpdb->prefix}{$serverTableName}"
							. " ON {$wpdb->prefix}{$tableName}.serverId={$wpdb->prefix}{$serverTableName}.id";
				$listQuery = $this->initPagination($listQuery, $target);
				$rows = $wpdb->get_results($listQuery);
				$target = $target.$this->pageNumberParameter();
				include('openquoteProducts.php');
			}
		}
		
		
		/**
		 * Display OpenQuote Servers Admin Page
		 */
        function printOpenQuoteServersAdminPage() {
			global $wpdb;
		
			$tableName = $this->serverTableName;
			$messageTemplatesTableName = $this->messageTemplatesTableName;
			$target = 'admin.php?page=openquote.php&admintab=Servers';
			
			// what action has been requested?
			$mode = '';
			$records = array();
			$this->getListActions($mode, $records);

			// deregister records
			if($mode=='deregister' || $mode=='delete'){
				$this->deregister($records);
			}

			// delete records
			if($mode=='delete'){
				// if delete confirmed by user
				if(isset($_POST['actionconfirm'])){
					$this->deregister($records,$tableName);
					$this->delete($records,$tableName);
				}
				// if delete not yet confirmed or cancelled by users
				elseif(!isset($_POST['actioncancel'])){
					$references = array();
					foreach ($records as $record) {
						$loadQuery = "SELECT openQuoteServer FROM {$wpdb->prefix}{$tableName} WHERE id = {$record}";
						$item = $wpdb->get_row($loadQuery);
						$references[$record] = $item->openQuoteServer;
					}
					include('openquoteActionConfirmation.php');
					return;
				}
			}

			// copy records
			if($mode=='copy'){
				foreach ($records as $id){
					$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$id}";
					$row = $wpdb->get_row($loadQuery);
					$openQuoteServer = $row->openQuoteServer;
					$registrationServiceUrl = $row->registrationServiceUrl;
					$markupServiceUrl = $row->markupServiceUrl;
					$consumerName = $row->consumerName;
					$wsrpid = $row->wsrpid;
					$messageTemplatesId = $row->messageTemplatesId;
					$wpdb->insert($wpdb->prefix.$tableName, array('openQuoteServer'=>$openQuoteServer, 'registrationServiceUrl'=>$registrationServiceUrl, 'markupServiceUrl'=>$markupServiceUrl, 'consumerName'=>$consumerName, 'wsrpid'=>$wsrpid, 'messageTemplatesId'=>$messageTemplatesId), array('%s','%s','%s','%s','%s','%d'));
				} 
			}

			// save records
			if($mode=='save'){
				$saveAction = $_POST['saveaction'];
				if($saveAction=='edit'){
					// Edit
					$id = $_POST['id'];
					$openQuoteServer = $_POST['openQuoteServer'];
					$registrationServiceUrl = $_POST['registrationServiceUrl'];
					$markupServiceUrl = $_POST['markupServiceUrl'];
					$consumerName = $_POST['consumerName'];
					$wsrpid = $_POST['wsrpid'];
					$oldWsrpid = $_POST['oldwsrpid'];
					$messageTemplatesId = $_POST['messageTemplatesId'];
					//check if wsrpid has been changed to deregister old one before saving changes
					if(isset($oldWsrpid) && $oldWsrpid!='' && $wsrpid!=$oldWsrpid){
						$this->deregister($records,$tableName);
					}
					$wpdb->update($wpdb->prefix.$tableName, array('openQuoteServer'=>$openQuoteServer, 'registrationServiceUrl'=>$registrationServiceUrl, 'markupServiceUrl'=>$markupServiceUrl, 'consumerName'=>$consumerName, 'wsrpid'=>$wsrpid, 'messageTemplatesId'=>$messageTemplatesId), array('id'=>$id), array('%s','%s','%s','%s','%s','%d'), array('%d'));
				}
				else{
					// New
					$openQuoteServer = $_POST['openQuoteServer'];
					$registrationServiceUrl = $_POST['registrationServiceUrl'];
					$markupServiceUrl = $_POST['markupServiceUrl'];
					$consumerName = $_POST['consumerName'];
					$wsrpid = $_POST['wsrpid'];
					$messageTemplatesId = $_POST['messageTemplatesId'];
					$wpdb->insert($wpdb->prefix.$tableName, array('openQuoteServer'=>$openQuoteServer, 'registrationServiceUrl'=>$registrationServiceUrl, 'markupServiceUrl'=>$markupServiceUrl, 'consumerName'=>$consumerName, 'wsrpid'=>$wsrpid, 'messageTemplatesId'=>$messageTemplatesId), array('%s','%s','%s','%s','%s','%d'));
				}
			}			

			// edit
			if($mode=='edit' || $mode=='new'){
				$row = null;
				if($mode=='edit'){
					if(isset($records[0])){
						$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$records[0]}";
						$row = $wpdb->get_row($loadQuery);
					}
				}
				
				$messageTemplatesListQuery = "SELECT id,templatesRef FROM {$wpdb->prefix}{$messageTemplatesTableName}";
				$messageTemplatesRows = $wpdb->get_results($messageTemplatesListQuery);

				include('openquoteServer.php');
			}
			// list
			else{
				$listQuery = "SELECT * FROM {$wpdb->prefix}{$tableName}";
				$listQuery = $this->initPagination($listQuery, $target);
				$rows = $wpdb->get_results($listQuery);
				$target = $target.$this->pageNumberParameter();
				include('openquoteServers.php');
			}
		}
		
		
		/**
		 * Display OpenQuote Message Templates Admin Page
		 */
        function printOpenQuoteMessagesAdminPage() {
			global $wpdb;
		
			$tableName = $this->messageTemplatesTableName;
			$target = 'admin.php?page=openquote.php&admintab=Messages';
			
			// what to do?
			$mode = '';
			$records = array();
			$this->getListActions($mode, $records);

			// delete records
			if($mode=='delete'){
				// if delete confirmed by user
				if(isset($_POST['actionconfirm'])){
					$this->delete($records,$tableName);
				}
				// if delete not yet confirmed or cancelled by users
				elseif(!isset($_POST['actioncancel'])){
					$references = array();
					foreach ($records as $record) {
						$loadQuery = "SELECT templatesRef FROM {$wpdb->prefix}{$tableName} WHERE id = {$record}";
						$item = $wpdb->get_row($loadQuery);
						$references[$record] = $item->templatesRef;
					}
					include('openquoteActionConfirmation.php');
					return;
				}
			}

			// copy records
			if($mode=='copy'){
				foreach ($records as $id){
					$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$id}";
					$row = $wpdb->get_row($loadQuery);
					$templatesRef = $row->templatesRef;
					$submitWSRPFormXML = $row->submitWSRPFormXML;
					$requestWSRPFormXML = $row->requestWSRPFormXML;
					$registerWSRPConsumerXML = $row->registerWSRPConsumerXML;
					$deregisterWSRPConsumerXML = $row->deregisterWSRPConsumerXML;
					$wpdb->insert($wpdb->prefix.$tableName, array('templatesRef'=>$templatesRef, 'submitWSRPFormXML'=>$submitWSRPFormXML , 'requestWSRPFormXML'=>$requestWSRPFormXML, 'registerWSRPConsumerXML'=>$registerWSRPConsumerXML, 'deregisterWSRPConsumerXML'=>$deregisterWSRPConsumerXML), array('%s','%s','%s','%s','%s'));
				} 
			}

			// save records
			if($mode=='save'){
				$saveAction = $_POST['saveaction'];
				if($saveAction=='edit'){
					// Edit
					$id = $_POST['id'];
					$templatesRef = $_POST['templatesRef'];
					$submitWSRPFormXML = $_POST['submitWSRPFormXML'];
					$requestWSRPFormXML = $_POST['requestWSRPFormXML'];
					$registerWSRPConsumerXML = $_POST['registerWSRPConsumerXML'];
					$deregisterWSRPConsumerXML = $_POST['deregisterWSRPConsumerXML'];	
					// remove special character slashes if required
					$submitWSRPFormXML = stripslashes($submitWSRPFormXML);
					$requestWSRPFormXML = stripslashes($requestWSRPFormXML);
					$registerWSRPConsumerXML = stripslashes($registerWSRPConsumerXML);
					$deregisterWSRPConsumerXML = stripslashes($deregisterWSRPConsumerXML);
					$wpdb->update($wpdb->prefix.$tableName, array('templatesRef'=>$templatesRef, 'submitWSRPFormXML'=>$submitWSRPFormXML , 'requestWSRPFormXML'=>$requestWSRPFormXML, 'registerWSRPConsumerXML'=>$registerWSRPConsumerXML, 'deregisterWSRPConsumerXML'=>$deregisterWSRPConsumerXML), array('id'=>$id), array('%s','%s','%s','%s','%s'), array('%d'));
				}
				else{
					// New
					$templatesRef = $_POST['templatesRef'];
					$submitWSRPFormXML = $_POST['submitWSRPFormXML'];
					$requestWSRPFormXML = $_POST['requestWSRPFormXML'];
					$registerWSRPConsumerXML = $_POST['registerWSRPConsumerXML'];
					$deregisterWSRPConsumerXML = $_POST['deregisterWSRPConsumerXML'];	
					// remove special character slashes if required
					$submitWSRPFormXML = stripslashes($submitWSRPFormXML);
					$requestWSRPFormXML = stripslashes($requestWSRPFormXML);
					$registerWSRPConsumerXML = stripslashes($registerWSRPConsumerXML);
					$deregisterWSRPConsumerXML = stripslashes($deregisterWSRPConsumerXML);
					$wpdb->insert($wpdb->prefix.$tableName, array('templatesRef'=>$templatesRef, 'submitWSRPFormXML'=>$submitWSRPFormXML , 'requestWSRPFormXML'=>$requestWSRPFormXML, 'registerWSRPConsumerXML'=>$registerWSRPConsumerXML, 'deregisterWSRPConsumerXML'=>$deregisterWSRPConsumerXML), array('%s','%s','%s','%s','%s'));
				}
			}
				
			// edit
			if($mode=='edit' || $mode=='new'){
				$row = null;
				if($mode=='edit'){
					if(isset($records[0])){
						$loadQuery = "SELECT * FROM  {$wpdb->prefix}{$tableName} WHERE id = {$records[0]}";
						$row = $wpdb->get_row($loadQuery);
					}
				}
				include('openquoteMessageTemplate.php');
			}
			// list
			else{
				$listQuery = "SELECT * FROM {$wpdb->prefix}{$tableName}";
				$listQuery = $this->initPagination($listQuery, $target);
				$rows = $wpdb->get_results($listQuery);
				$target = $target.$this->pageNumberParameter();
				include('openquoteMessageTemplates.php');
			}
		}
		
		
		/**
		 * Display OpenQuote Display Text Admin Page
		 */
        function printOpenQuoteDisplayAdminPage() {
			global $wpdb;
		
			$tableName = $this->userInformationTableName;
			$target = 'admin.php?page=openquote.php&admintab=Display';
			
			// what action has been requested?
			$mode = '';
			$records = array();
			$this->getListActions($mode, $records);

			// delete records
			if($mode=='delete'){
				// if delete confirmed by user
				if(isset($_POST['actionconfirm'])){
					$this->delete($records,$tableName);
				}
				// if delete not yet confirmed or cancelled by users
				elseif(!isset($_POST['actioncancel'])){
					$references = array();
					foreach ($records as $record) {
						$loadQuery = "SELECT infoRef FROM {$wpdb->prefix}{$tableName} WHERE id = {$record}";
						$item = $wpdb->get_row($loadQuery);
						$references[$record] = $item->infoRef;
					}
					include('openquoteActionConfirmation.php');
					return;
				}
			}
			
			// copy records
			if($mode=='copy'){
				foreach ($records as $id){
					$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$id}";
					$row = $wpdb->get_row($loadQuery);
					$infoRef = $row->infoRef;
					$infoText = $row->infoText;
					$reportAs404 = $row->reportAs404;
					$emailAdmin = $row->emailAdmin;
					$wpdb->insert($wpdb->prefix.$tableName, array('infoRef'=>$infoRef, 'infoText'=>$infoText , 'reportAs404'=>$reportAs404, 'emailAdmin'=>$emailAdmin), array('%s','%s','%d','%d'));
				} 
			}

			// save records
			if($mode=='save'){
				$saveAction = $_POST['saveaction'];
				if($saveAction=='edit'){
					// Edit
					$id = $_POST['id'];
					$infoRef = $_POST['infoRef'];
					$infoText = $_POST['infoText'];
					$reportAs404 = $_POST['reportAs404'];
					$emailAdmin = $_POST['emailAdmin'];
					$wpdb->update($wpdb->prefix.$tableName, array('infoRef'=>$infoRef, 'infoText'=>$infoText , 'reportAs404'=>$reportAs404, 'emailAdmin'=>$emailAdmin), array('id'=>$id), array('%s','%s','%d','%d'), array('%d'));
				}
				else{
					// New
					$infoRef = $_POST['infoRef'];
					$infoText = $_POST['infoText'];
					$reportAs404 = $_POST['reportAs404'];
					$emailAdmin = $_POST['emailAdmin'];
					$wpdb->insert($wpdb->prefix.$tableName, array('infoRef'=>$infoRef, 'infoText'=>$infoText , 'reportAs404'=>$reportAs404, 'emailAdmin'=>$emailAdmin), array('%s','%s','%d','%d'));
				}
			}
			
			// edit
			if($mode=='edit' || $mode=='new'){
				$row = null;
				if($mode=='edit'){
					if(isset($records[0])){
						$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$records[0]}";
						$row = $wpdb->get_row($loadQuery);
					}
				}
				include('openquoteDisplayText.php');
			}
			// list
			else{
				$listQuery = "SELECT * FROM {$wpdb->prefix}{$tableName}";
				$listQuery = $this->initPagination($listQuery, $target);
				$rows = $wpdb->get_results($listQuery);
				$target = $target.$this->pageNumberParameter();
				include('openquoteDisplayTexts.php');
			}
		}
		
		
		/**
		 * Display OpenQuote Log Admin Page
		 */
        function printOpenQuoteLogAdminPage() {
			global $wpdb;
			
			$tableName = $this->logTableName;
			$target = 'admin.php?page=openquote.php&admintab=Log';

			// what to do?
			$mode = '';
			$records = array();
			$this->getListActions($mode, $records);

			// delete records
			if($mode=='delete'){
				$this->delete($records,$tableName);
			}
			
			// list records
			$listQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} ORDER BY time DESC";
			$listQuery = $this->initPagination($listQuery, $target);
			$rows = $wpdb->get_results($listQuery);
			$target = $target.$this->pageNumberParameter();
			include('openquoteLog.php');
		}
		
		
		/**
		 * Display OpenQuote Styling Help Admin Page
		 */
        function printOpenQuoteStylingAdminPage() {
			include('openquoteStylingHelp.php');
		}
		
		
		/**
		 * Display OpenQuote Component Help Admin Page
		 */
        function printOpenQuoteHelpAdminPage() {
			include('openquoteComponentHelp.php');
		}
		
		
		/**
		 * get a list action details
		 * @param mode action to perform
		 * @param records to be actioned
		 */
        function getListActions(&$mode, &$records) {
			$mode = '';
			$records = array();
			if(isset($_GET['mode'])){
				$mode = $_GET['mode'];
				if(isset($_GET['record'])){
					$records[0] = $_GET['record'];
				}
			}
			elseif(isset($_POST['mode'])){
				$mode = $_POST['mode'];
			}
			elseif(isset($_POST['doaction'])){
				$mode = $_POST['action'];
			}
			elseif(isset($_POST['doaction2'])){
				$mode = $_POST['action2'];
			}

			$mode = strtolower($mode);
			
			if(isset($_POST['rowSelected'])){
				$records = $_POST['rowSelected'];
			}
			
			return;
		}
		
		
		/**
		 * Method to set up list pagination
		 *
		 * @access	public
		 * @param	string list query string
		 * @param	string post/get action target
		 * @param	int number of list items per page
		 * @return	string updated query
		 */
		function initPagination($query, $target, $itemsPerPage=20){
		
			$items = mysql_num_rows(mysql_query($query)); // number of total rows in the database
			$this->pagination = null;
			 
			if($items > 0) {
					$this->pagination = new pagination;
					$this->pagination->items($items);
					$this->pagination->limit($itemsPerPage); // Limit entries per page
					$this->pagination->target($target);
					$this->pagination->currentPage($_GET[$this->pagination->paging]); // Gets and validates the current page
					$this->pagination->calculate(); // Calculates what to show
					$this->pagination->parameterName('paging');
					$this->pagination->adjacents(1); //No. of page away from the current page
			 
					if(!isset($_GET['paging'])) {
						$this->pagination->page = 1;
					} else {
						$this->pagination->page = $_GET['paging'];
					}
			 
					//Query for limit paging
					$query = $query . ' LIMIT ' . ($this->pagination->page - 1) * $this->pagination->limit  . ', ' . $this->pagination->limit;
			}
			return $query;
		}

		/**
		 * show pagination
		 * @return pagination html
		 */
		function showPagination(){
			$htmlPagination = '';
			if(isset($this->pagination)){
				$htmlPagination = $this->pagination->show();
			}
			return $htmlPagination;
		}

		/**
		 * page number parameter
		 * @return pagination url parameter
		 */
		function pageNumberParameter(){
			$pageNumber = '';
			if(isset($this->pagination)){
				$pageNumber = '&paging='.$this->pagination->page;
			}
			return $pageNumber;
		}

		
		/**
		 * delete record/s
		 * @param record id/s
		 * @param table name
		 * @return success flag
		 */
		function delete($ids, $tableName){
			global $wpdb;
			if(!is_array($ids)){
				$ids = array($ids);
			}
			foreach ($ids as $id){
				$deleteQuery = "DELETE FROM {$wpdb->prefix}{$tableName} WHERE id = {$id}";
				$wpdb->query($deleteQuery);
			} 
		}

		
		/**
		 * publish product record/s
		 * @param ids product record id/s
		 * @param tableName name
		 * @param publish flag true/false
		*/
		function publishProduct($ids, $tableName, $publish=true){
			global $wpdb;
			if(!is_array($ids)){
				$ids = array($ids);
			}
			foreach ($ids as $id){
				$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$id}";
				$row = $wpdb->get_row($loadQuery);
				if($row->published!=$publish){
					$wpdb->update($wpdb->prefix.$tableName, array('published'=>$publish), array('id'=>$id), array('%d'), array('%d'));
				}
			} 
		}

		
		/**
		 * deregister record/s
		 * @param record id/s
		 * @param table name
		 * @return success flag
		 */
		function deregister($ids){
			global $wpdb;
			if(!is_array($ids)){
				$ids = array($ids);
			}
			foreach ($ids as $id){
				$this->deregisterConsumer($id);
			} 
		}

		/**
		 * Method to deregister consumer
		 *
		 * @access	public
		 * @param id consumers WSRP registration id
		 * @return	boolean	True on success
		 */
		function deregisterConsumer($id)
		{
			global $wpdb;
			
			$successFlag = true;
			
			$tableName = $this->serverTableName;
			
			$loadQuery = "SELECT * FROM {$wpdb->prefix}{$tableName} WHERE id = {$id}";
			$row = $wpdb->get_row($loadQuery);

			$registrationHandle = $row->wsrpid;
			if(!empty($registrationHandle)){
				$deregisterWSRPConsumerXML = $this->_loadDeregisterTemplate($row->messageTemplatesId);
				if(!empty($deregisterWSRPConsumerXML)){
					
					// set up new helper instance
					$openQuoteServer = $row->openQuoteServer;
					$registrationServiceUrl = $row->registrationServiceUrl;
					$consumerName = $row->consumerName;

					$this->_openquoteconnect = new OpenQuoteConnect();
					$this->_openquoteconnect->initialiser(	null,
										$openQuoteServer,
										$registrationServiceUrl,
										null,
										$consumerName,
										null,
										null,
										null,
										$deregisterWSRPConsumerXML,
										$registrationHandle,
										null,
										null);
					
					// deregister from server
					$this->_openquoteconnect->deregisterConsumer();
					
					// if error occured, flag it
					$logHTML = $this->_openquoteconnect->readLog();
					if($logHTML!=null){
						$this->_updateLog("Server {$openQuoteServer} OpenQuote server deregistration failed<br/>{$logHTML}");
						$successFlag=false;
					}
					
					// if no errors, clear the registration id from the server record and save.
					if($successFlag){
						$wpdb->update($wpdb->prefix.$tableName, array('wsrpid'=>''), array('id'=>$id), array('%s'), array('%d'));
					}
					
				}
				else{
					$this->_updateLog("Server {$this->_data->openQuoteServer} deregisterWSRPConsumerXML message template not found");
					$successFlag=false;
				}
			}
			
			return $successFlag;
		}

		/**
		 * Method to load deregister message template data
		 *
		 * @access	private
		 * @return	boolean	True on success
		 */
		function _loadDeregisterTemplate($templatesId)
		{
			global $wpdb;
			
			// if no id then can't load, but no error
			if(!($templatesId)){
				return false;
			}

			// Lets load the content
			$loadQuery = "SELECT deregisterWSRPConsumerXML FROM {$wpdb->prefix}{$this->messageTemplatesTableName} WHERE id = {$templatesId}";
			$row = $wpdb->get_row($loadQuery);
			if (!isset($row)) {
				return false;
			}

			$_templatedata = $row->deregisterWSRPConsumerXML;
			
			return $_templatedata;
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
				$wpdb->insert($wpdb->prefix.$logTableName, array('message'=>$logDetail), array('%s'));
			}
		}
		
    }

} //End Class OpenQuoteAdminController