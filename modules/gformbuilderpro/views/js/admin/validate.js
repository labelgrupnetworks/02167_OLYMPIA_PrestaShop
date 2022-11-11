/**
* This is validate js file. Don't edit the file if you want to update module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/

function gvalidate_field(that){
    val = $(that).val();
    if ($(that).hasClass('gvalidate_isName')) {
		val = val.replace(/[^0-9a-zA-Z:._-]/g, '').replace(/^[^a-zA-Z]+/, '');
	}
    if ($(that).hasClass('gvalidate_isId')) {
		val = val.replace(/[^-0-9a-zA-Z_]/g, '');
	}
    if ($(that).hasClass('gvalidate_isClass')) {
		val = $.map(val.split(' '), function(n) {
			return n.replace(/[^-0-9a-zA-Z_]/g, '');
		}).join(' ');

		val = $.trim(val.replace(/\s+/g, ' '));
	}
    
    $(that).val(val);
}
$(document).on('focusout', '.gvalidate', function() {
	gvalidate_field(this);
});