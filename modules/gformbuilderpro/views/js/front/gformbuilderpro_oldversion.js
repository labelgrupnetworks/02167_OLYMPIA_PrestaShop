/**
* This is js file. Don't edit the file if you want to update module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

$(document).ready(function(){
    // fix prestashop version 1.5 missing bootstrap css
    // only load file bootstrap_grid.css when ".gformbuilderpro_form" exists
     if($('.gformbuilderpro_form').length > 0)
        $('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', baseUri+'modules/gformbuilderpro/views/css/front/bootstrap_grid.css'));
     //# fix prestashop version 1.5 missing bootstrap css
});