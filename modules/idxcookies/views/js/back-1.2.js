/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2019 Innovadeluxe SL

* @license   INNOVADELUXE
*/

class IdxrcookiesBack{

    constructor(){
        this.config = typeof IdxrcookiesConfigBack == 'object' ? IdxrcookiesConfigBack : {};
        if(typeof this.config.urlAjax == 'undefined'){
            throw 'Variables de configuración necesarias no definidas';
        }
        this.configBackButtons = [
            {
                selector: 'form-object-idxcookies_templates',
                urlBack: this.config.urlFormTemplatesBack
            },
            {
                selector: 'form-object-idxcookies_type',
                urlBack: this.config.urlFormCookiesTypeBack
            },
            {
                selector: 'form-object-idxcookies',
                urlBack: this.config.urlFormCookiesBack
            },
        ]
    }

    init(){
        this.initHelperFormBackButtons();
        this.handleCookiesListSelects();
        this.handleSelectorCMS();
    }

    handleSelectorCMS(){
        let prefijo = this.config.prefijoModulo;
        $(document).on('change','#'+prefijo+'COOKIES_SELECTOR',function(){
            var thisselectlinkl=$(this).val();
            if(thisselectlinkl==='0'){
                thisselectlinkl='#';
            }
            $('input[id^="'+prefijo+'COOKIES_URL_"]').each(function(){
                if($(this).hasClass('jselect')){
                    $(this).val(thisselectlinkl);
                }
            });
        });
    }

    handleCookiesListSelects(){
        let clase = this;
        $(document).on('change','.js_cookieType_selector',async function(){
            let datos = {
                id_cookie: $(this).attr('data-idcookie'),
                id_cookie_type: $(this).val(),
                action: 'updateCookieType'
            }
            try{
                let response = await clase.ajaxRequest(clase.config.urlAjax, datos, 'get',  'text');
                if(response == 'ok'){
                    showSuccessMessage('Changes saved');
                }else{
                    showErrorMessage('Error when try to save the configuration');
                }
            }catch(e){
                console.log(e);
                showErrorMessage('Error when try to save the configuration');
            }
        });

        $(document).on('change','.js_cookieModule_selector',async function(){
            let datos = {
                id_cookie: $(this).attr('data-idcookie'),
                module: $(this).val(),
                action: 'updateCookieModule'
            }
            let $elemento = $(this);
            try{
                let response = await clase.ajaxRequest(clase.config.urlAjax, datos, 'get',  'text');
                if(response == 'ok'){
                    if($elemento.val() != '---'){
                        let $selectTemplate = $elemento.parent().parent().find('.js_cookieTemplate_selector');
                        let $optionTemplate = $selectTemplate.find('option:first');
                        $selectTemplate.val($optionTemplate.val());
                    }
                    showSuccessMessage('Changes saved');
                }else{
                    showErrorMessage('Error when try to save the configuration');
                }
            }catch(e){
                console.log(e);
                showErrorMessage('Error when try to save the configuration');
            }
        });

        $(document).on('change','.js_cookieTemplate_selector',async function(){
            let datos = {
                id_cookie: $(this).attr('data-idcookie'),
                id_template: $(this).val(),
                action: 'updateCookieTemplate'
            }
            let $elemento = $(this);
            try{
                let response = await clase.ajaxRequest(clase.config.urlAjax, datos, 'get',  'text');
                if(response == 'ok'){
                    if(Number($elemento.val()) > 0){
                        let $selectModule = $elemento.parent().parent().find('.js_cookieModule_selector');
                        let $optionModule = $selectModule.find('option:first');
                        $selectModule.val($optionModule.val());
                    }
                    showSuccessMessage('Changes saved');
                }else{
                    showErrorMessage('Error when try to save the configuration');
                }
            }catch(e){
                console.log(e);
                showErrorMessage('Error when try to save the configuration');
            }
        });
    }

    initHelperFormBackButtons(){
        let btnBack;
        let urlBack;
        let selector;
        this.configBackButtons.forEach((configBackButton)=>{
            selector = configBackButton.selector;
            urlBack = configBackButton.urlBack;
            btnBack = $('#'+selector)
                .find('[onclick="window.history.back();"]');
            if(btnBack.length == 0){
                btnBack = $('#'+selector)
                    .find('[onclick="javascript:window.history.back();"]');
            }
            btnBack.attr("onclick", null).off("click");
            btnBack.attr('href', urlBack);
        })
    }

    ajaxRequest(endpoint = '', datos = {}, tipo = 'get', datatype= 'json'){
        let promise = new Promise((resolve, reject)=>{
            $.ajax({
                type: tipo,
                data: datos,
                dataType: datatype,
                url: endpoint,
                success: function(response){
                    resolve(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    reject(thrownError);
                }
            });
        });
        return promise;
    }

}

$(function() {
    try{
        let handler;
        if(typeof IdxrcookiesBackOverride == 'function'){
            handler = new IdxrcookiesBackOverride();
        }else{
            handler = new IdxrcookiesBack();
        }
        handler.init();
    }catch(e){
        console.log(e);
    }
});
