<?php
/**
 * OpenQuote Help Administration Page
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 
?>

<h2>WordPress OpenQuote Component Help</h2>
<p align="justify">The WordPress OpenQuote component allows OpenQuote configured insurance products to be selected as WordPress menu items.</p>

<p align="justify">The administration area of the component consists of the following pages:</p>

<ul>
	<li><strong>OpenQuote: </strong>An overview of OpenQuote and Applied Industrial Logic</li>
	<li><strong>Products: </strong>OpenQuote products being made available to this site</li>
	<li><strong>Servers: </strong>OpenQuote servers available to provide products</li>
	<li><strong>Message Templates: </strong>Format of messages being used to communicate with the servers</li>
	<li><strong>Display Text: </strong>Text and format of messages to be displayed to users when errors occur</li>
	<li><strong>Log: </strong>Details of any issues or errors that occur when using the component</li>
	<li><strong>Styling Help: </strong>CSS hints and tips for component styling</li>
	<li><strong>Component Help: </strong>This page, how to configure and use the component</li>
</ul>

<div class="tool-box">
	<p align="justify">The two key areas involved in configuring this component can be found on the <strong>Products</strong> tab and the <strong>Servers</strong> tab.  Servers determine what OpenQuote servers are available for this component to access, and Products determine which products on those servers are available for inclusion on this website. All the other configuration tabs can be left as default if required.</p>
	<p align="justify">Once your servers and products are configured, the products can be added to the website as WordPress page by adding a <strong>[openquote product=</strong>product name<strong>]</strong> to the content of a page, where the product name text is the required product as defined on the <a href="admin.php?page=openquote.php&admintab=Products">products administration page</a></p>
</div>

<div class="tool-box">
	<h3 class="title">OpenQuote Tab</h3>
	<p align="justify">A simple high level overview of OpenQuote, and its creators Applied Industrial Logic.</p>
</div>

<div class="tool-box">
	<h3 class="title">Products Tab</h3>
	<p align="justify">The items listed on the products page define what OpenQuote insurance products are available to use. Products can be added to and removed from the list as required by specifiying the server they are distributed from and their id on that server, these can then be selected when configuring a menu item for this component.</p>
	<p align="justify">This administration page allows you to edit the details of an OpenQuote product that you want to make available for use on this website.</p>
	<p align="justify"><strong>Name: </strong>A unique name for this product, designed to help make website administration easier.</p>
	<p align="justify"><strong>Published: </strong>Controls the availability of this product on the website.</p>
	<p align="justify"><strong>Server: </strong>The URL for the OpenQuote server hosting this product. This list is configured using the <a href="admin.php?page=openquote.php&admintab=Servers">Servers administration page</a>.</p>
	<p align="justify"><strong>Product Name: </strong>This is the id used as a reference to the product by the OpenQuote server, for example the id for the motor product example on the OpenQuote demo server is 'AIL.Demo.MotorPlus'.</p>
</div>

<div class="tool-box">
	<h3 class="title">Servers Tab</h3>
	<p align="justify">This administration page allows you to edit the details of an OpenQuote server containing products that you want to make available for use on this website.</p>
	<p align="justify"><strong>Server: </strong> The URL for the OpenQuote server hosting this product, for example the OpenQuote demonstration server URL is 'http://www.appliedindustriallogic.com:8080'.</p>
	<p align="justify"><strong>Registration Service URL: </strong>The relative URL of the WSRP registration service on the OpenQuote server.  Typically this will be '/portal-wsrp/RegistrationService'.</p>
	<p align="justify"><strong>Markup Service URL: </strong>The relative URL of the WSRP registration service on the OpenQuote server.  Typically this will be '/portal-wsrp/MarkupService'.</p>
	<p align="justify"><strong>Consumer Name: </strong>A unique name that you must specify so the OpenQuote server can uniquely identify this website.</p>
	<p align="justify"><strong>WSRP ID: </strong>This value is automatically supplied by the OpenQuote server on first connection.  You should not change this value unless you have a technical understanding of the OpenQuote WSRP server you are accessing.</p>
	<p align="justify"><strong>Message Templates: </strong>WSRP SOAP message XML templates used when creating messages to communicate with the OpenQuote Server, the 'Default' list should work with any OpenQuote server. This list is configured using the <a href="admin.php?page=openquote.php&admintab=Messages">Message Templates administration page</a>.</p>
</div>

<div class="tool-box">
	<h3 class="title">Message Templates Tab</h3>
	<p align="justify">The message templates page allows you to edit the WSRP SOAP XML message templates used for communicating with OpenQuote servers.</p>
	<p align="justify"><strong>Reference: </strong> A unique name to identify this set of templates.</p>
	<p align="justify"><strong>Submit WSRP Form XML: </strong>SOAP message XML template used as a basis for 'Submit Form' messages that are sent to the OpenQuote server.</p>
	<p align="justify"><strong>Request WSRP Form XML: </strong>SOAP message XML template used as a basis for 'Request Form' messages that are sent to the OpenQuote server.</p>
	<p align="justify"><strong>Register WSRP Consumer XML: </strong>SOAP message XML template used as a basis for 'Regisgter WSRP Consumer' messages that are sent to the OpenQuote server.</p>
	<p align="justify"><strong>Deregister WSRP Consumer XML: </strong>SOAP message XML template used as a basis for 'Deregister WSRP Consumer' messages that are sent to the OpenQuote server.</p>
</div>

<div class="tool-box">
	<h3 class="title">Display Text Tab</h3>
	<p align="justify">The display text page allows you to edit the messages displayed to users when problems occur.</p>
	<p align="justify"><strong>Subject: </strong>The name of the issue being reported.</p>
	<p align="justify"><strong>Text to display to user: </strong>The message that should be displayed to users when this issue occurs.</p>
<!--	<p align="justify"><strong>Report as http 404 error: </strong>How the issue should be reported to users, either via a http 404 web page, or by displaying the text directly on the selected web page.</p>-->
	<p align="justify"><strong>Email error to site admin: </strong>Indicates if the error should be emailed to the site administrator.</p>
</div>

<div class="tool-box">
	<h3 class="title">Log Tab</h3>
	<p align="justify">Details of any issues that occur during the use of the OpenQuote component, including a time-stamp.  These are not user issues (i.e. referred or declined quotes), but issues that relate to problems getting the component to function correctly such as server connectivity issues.  Log entries can be deleted when no longer required.</p>
</div>

<div class="tool-box">
	<h3 class="title">Styling Help Tab</h3>
	<p align="justify">Details of the HTML classes used by elements output by OpenQuote, with suggested CSS to help style OpenQuote pages being presented within WordPress</p>
</div>

<div class="tool-box">
	<h3 class="title">Component Help Tab</h3>
	<p align="justify">This page.</p>
</div>
