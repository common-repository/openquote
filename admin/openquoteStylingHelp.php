<?php
/**
 * OpenQuote Styling Help Administration Page
 *
 * @package OpenQuote
 * @subpackage Administration
 */

// don't load directly
if(!defined('ABSPATH')) die('Restricted access'); 
?>

<h2>WordPress OpenQuote Component Styling</h2>
<p align="justify">The presentation layer of this component can be styled using your websites CSS style sheets.</p>
<p align="justify">This help page describes the basic classes used in the OpenQuote output HTML and the context in which they are used.</p>
<p align="justify">It is worth noting that within OpenQuote product definitions (held on the OpenQuote server) class names can also be created for individual elements.  To find out if the product you are accessing have and custom class names you will need to speak to the product developers.</p>

<div class="tool-box">
	<h3 class="title">HTML Classes</h3>
	<table>
		<tr><td><strong>CLASS</strong>         </td><td><strong>USE</strong></td><td><strong>CONTEXT</strong></td></tr>
		<tr><td>div.openquote4wordpress           </td><td>Component Wrapper   </td><td>&lt;div class="openquote4wordpress"&gt;</td></tr>
		<tr><td>tr.portlet-section-header      </td><td>Page Title          </td><td>&lt;tr class="portlet-section-header"&gt;&lt;td&gt;Horse Insurance&lt;/td&gt;&lt;/tr&gt;</td></tr>
		<tr><td>td.portlet-font                </td><td>Text                </td><td>&lt;td class="portlet-font" width="100%"&gt;</td></tr>
		<tr><td>tr.portlet-font                </td><td>Text                </td><td>&lt;tr class="portlet-font"&gt;</td></tr>
		<tr><td>input.portlet-form-input-field </td><td>Submit Button       </td><td>&lt;input class="portlet-form-input-field" type="submit" name="op=Get A Quote:immediate=false" value="Get A Quote"&gt;</td></tr>
		<tr><td>td.portal-form-label           </td><td>Field Label         </td><td>&lt;td class="portal-form-label"&gt;Title:&lt;/td&gt;</td></tr>
		<tr><td>select.pn-normal               </td><td>Select              </td><td>&lt;select id="title" name="title" class="pn-normal"&gt;</td></tr>
		<tr><td>td.portlet-msg-error           </td><td>Error Message       </td><td>&lt;td class="portlet-msg-error"&gt;Field required;&lt;/td&gt;</td></tr>
		<tr><td>input.portlet                  </td><td>Field               </td><td>&lt;input name="otherTitle" id="otherTitle" class="portlet-form-input-field" type="text"&gt;</td></tr>
		<tr><td>tr.portlet-section-subheader   </td><td>Section Title       </td><td>&lt;tr class="portlet-section-subheader"&gt;&lt;td colspan="4"&gt;What Class of Activities will you be participating in:&lt;/td&gt;&lt;/tr&gt;</td></tr>
		<tr><td>tr.portlet-table-subheader     </td><td>Sub Title           </td><td>&lt;tr valign='middle' class='portlet-table-subheader'&gt;&lt;td&gt;Your Quotation: GBP148.18&lt;/td&gt;&lt;/tr&gt;</td></tr>
	</table>
</div>

<div class="tool-box">
	<h3 class="title">Example CSS</h3>
	<pre>
/* A simple example for sytling the OpenQuote component HTML output*/

#content div.openquote4wordpress table{
margin: 0px; 
border: 0px; 
padding: 0px;
}
#content div.openquote4wordpress tr{
margin: 0px; 
border: 0px; 
padding: 0px; 
}
#content div.openquote4wordpress td{
margin: 0px; 
border: 0px; 
padding: 0px; 
}

#content div.openquote4wordpress tr.portlet-section-header{
color: #323232; 
font-size: 120%;
font-weight: bold;
text-transform: uppercase;
letter-spacing: 0px;
}

#content div.openquote4wordpress td.portlet-msg-error{
color: red; 
font-weight: bold;
}

#content div.openquote4wordpress tr.portlet-section-subheader{
color: #323232; 
margin-bottom: 10px;
font-size: 100%;
font-weight: bold;
letter-spacing: 0px;
}

#content div.openquote4wordpress tr.portlet-table-subheader{
color: #323232; 
margin-bottom: 10px;
font-size: 120%;
font-weight: bold;
letter-spacing: 0px;
}
	</pre>
</div>
