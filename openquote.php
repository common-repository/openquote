<?php
/**
 * @package OpenQuote
 */

/*
Plugin Name: OpenQuote
Plugin URI: http://openquote4wordpress.appliedindustriallogic.com/
Description: Integrate OpenQuote insurance product quotations into your website. OpenQuote is an open source insurance solution.
Version: 1.0.6
Author: Applied Industrial Logic
Author URI: http://www.appliedindustriallogic.com/
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 

// load admin class
require_once(dirname(__FILE__).'/admin/openquoteAdminController.php' );

// load content class
require_once(dirname(__FILE__).'/admin/openquoteContentController.php' );


if (!class_exists('OpenQuotePlugin')) {

    class OpenQuotePlugin {

	
        function OpenQuotePlugin() { //constructor
        }

		/** 
		 * Insert OpenQuote content into post content if '[openquote product="product name"]' string is found within it
		 * @param attr shortcode parameters
		 * @param content shortcode content
		 * @return OpenQuote content
		 */
		function addOpenQuoteContent($attr, $content) {
		
			// create class
			if (class_exists('OpenQuoteContentController')) {
				 $openquoteContentController = new OpenQuoteContentController();
				 $content = $openquoteContentController->addOpenQuoteContent($attr, $content);
			}
			
			return $content;

		}		 

		/**
		 * Display OpenQuote Admin Page
		 */
        function printOpenQuoteAdminPage() {
			// create class
			if (class_exists('OpenQuoteAdminController')) {
				 $openquoteAdminController = new OpenQuoteAdminController();
				 $openquoteAdminController->printOpenQuoteAdminPage();
			}
		}

		
    }

} //End Class OpenQuotePlugin


// plugin activate setup
include(dirname(__FILE__).'/admin/openquoteActivateSetup.php');

// installation & update plugin functions
register_activation_hook(__FILE__,'openquoteproduct_db_install');
register_activation_hook(__FILE__,'openquoteserver_db_install');
register_activation_hook(__FILE__,'openquotemessagetemplates_db_install');
register_activation_hook(__FILE__,'openquoteuserinformation_db_install');
register_activation_hook(__FILE__,'openquotelog_db_install');

// deactivation
register_deactivation_hook(__FILE__,'openquote_serverdeactivate');


// create class
if (class_exists('OpenQuotePlugin')) {
     $openquotePlugin = new OpenQuotePlugin();
}

//Initialize the admin panel
if (!function_exists('OpenQuotePlugin_ap')) {
	function OpenQuotePlugin_ap() {
		global $openquotePlugin;
		if (!isset($openquotePlugin)) {
			return;
		}
		
		// add administration page
		if (function_exists('add_options_page')) {
			add_options_page( 'OpenQuote Plugin', 'OpenQuote', 9, basename(__FILE__), array(&$openquotePlugin, 'printOpenQuoteAdminPage'));  
		}
		
	}   
}


//Initialize user session
if (!function_exists('openquote_init_session')) {
	function openquote_init_session()
	{
		if ( !session_id() ){
			session_start();
		} 
	}
}

//Initialize user session
if (!function_exists('openquote_contentstyle')) {
	function openquote_contentstyle()
	{
		echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/openquote/css/openquotecontent.css" />' . "\n";
	}
}

//Actions and Filters   
if (isset($openquotePlugin)) {
	add_shortcode('openquote', array(&$openquotePlugin, 'addOpenQuoteContent'));		

    //Actions
	add_action('admin_menu', 'OpenQuotePlugin_ap');    
	add_action('init', 'openquote_init_session', 1);
	add_action('wp_head', 'openquote_contentstyle',0);
}




?>