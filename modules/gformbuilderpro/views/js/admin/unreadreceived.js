/**
* This is main js file. Don't edit the file if you want to update module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2019 Globo JSC
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/

function getUnReadReceived(){
    if($('#subtab-AdminGformrequest').length > 0){
        if (typeof gformbuilderpro_module_url != 'undefined' && gformbuilderpro_module_url !='') {
            $.ajax({
                 type: 'POST',
                 url: gformbuilderpro_module_url,
                 data: 'getUnReadReceived=1',
                 dataType: "json",
                 success: function (data, textStatus, request) {
                    if(data.error == 0 && data.nbr > 0){
                        if($('#subtab-AdminGformrequest .nbr_unread').length == 0)
                        {
                            if($('#subtab-AdminGformrequest span').length){
                                $('#subtab-AdminGformrequest span').append('<span class="nbr_unread">'+data.nbr+'</span>'); 
                            }else
                                $('#subtab-AdminGformrequest a').append('<span class="nbr_unread ">'+data.nbr+'</span>');
                        }else{
                            $('#subtab-AdminGformrequest .nbr_unread').html(data.nbr);
                        }
                    }else{
                        if($('#subtab-AdminGformrequest .nbr_unread').length > 0)
                            $('#subtab-AdminGformrequest .nbr_unread').remove();
                    }
                 }
            });
        }
    }
}
$(document).ready(function(){
    getUnReadReceived();
});