{*
* Do not edit the file if you want to upgrade the module in future.
*
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}

{if $input.type == 'customtags'}
    <div class="col-lg-6 customtags">	
        <input type="hidden" class="addtagprompt" value="{l s='Add hook' mod='gformbuilderpro'}" />
        {literal}
            <script type="text/javascript">
                $().ready(function () {
                    var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}{literal}';
                    $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add hook' mod='gformbuilderpro'}{literal}'});
                    $({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
                        $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                    });
                });
            </script>
        {/literal}
        {assign var='value_text' value=$fields_value[$input.name]}
            {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
            {/if}
            {if isset($input.maxchar) && $input.maxchar}
            <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval}</span></span>
            {/if}
            {if isset($input.prefix)}
            <span class="input-group-addon">
                {$input.prefix|escape:'html':'UTF-8'}
            </span>
            {/if}
            <input type="text"
                name="{$input.name|escape:'html':'UTF-8'}"
                id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}tagify"
                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                {if isset($input.required) && $input.required } required="required" {/if}
                {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}
                />
            {if isset($input.suffix)}
            <span class="input-group-addon">
                {$input.suffix|escape:'html':'UTF-8'}
            </span>
            {/if}
                <p class="help-block">
                 {l s='To add "Hook" click in the field, write hook name(ex: displayHome), and then press "Enter".Learn more about Prestashop Front-office hook: ' mod='gformbuilderpro'} 
                 <a target="_blank" href="https://devdocs.prestashop.com/1.7/modules/concepts/hooks/list-of-hooks/">https://devdocs.prestashop.com/1.7/modules/concepts/hooks/list-of-hooks/</a>  
                </p>
    </div>
    <div class="col-lg-3">
        <a href="" class="dropdown-toggle btn btn-default" tabindex="-1" data-toggle="dropdown">
            <i class="icon-list"></i>
        </a>
        <ul class="dropdown-menu choose_hook" rel="{$input.name|escape:'htmlall':'UTF-8'}">
            <li><a data-shortcode="displayHome">{l s='displayHome: Home page.' mod='gformbuilderpro'}</a></li>
            <li><a data-shortcode="displayLeftColumn">{l s='displayLeftColumn: Left column' mod='gformbuilderpro'}</a></li>
            <li><a data-shortcode="displayRightColumn">{l s='displayRightColumn: Right column' mod='gformbuilderpro'}</a></li>
            <li><a data-shortcode="displayFooter">{l s='displayFooter: Footer' mod='gformbuilderpro'}</a></li>
            <li><a data-shortcode="displayFooterProduct">{l s='displayFooterProduct: Under the product\'s description' mod='gformbuilderpro'}</a></li>
            <li><a data-shortcode="displayShoppingCartFooter">{l s='displayShoppingCartFooter: Shopping cart footer' mod='gformbuilderpro'}</a></li>
        </ul>
    </div>
{elseif $input.type == 'mailchimpmap'}
    <div class="col-lg-12 mailchimpmap_wp" style="{if !isset($fields_value['mailchimp']) ||  $fields_value['mailchimp'] == '0'} display:none;{/if}">
        

        {if isset($fields_value['mailchimp_apikey_empty']) && $fields_value['mailchimp_apikey_empty']}
            <div class="alert alert-danger" role="alert">
                <p class="alert-text">
                    {l s='Looks like your Mailchimp API key that you provide is not right or empty, Please go to "General Settings > Integrations" to correct it' mod='gformbuilderpro'}
                    <br/>
                    <a class="btn btn-default"  target="_blank" href="{if isset($fields_value['config_link'])}{$fields_value['config_link']|escape:'html':'UTF-8'}{/if}" title="{l s='General Settings' mod='gformbuilderpro'}">
                        {l s='General Settings' mod='gformbuilderpro'}
                    </a>
                </p>
            </div>
        {/if}
        <div class="form-group">										
            <label class="control-label col-lg-3">{l s='Select mailchimp list' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <select name="mailchimplist">
                    <option value=""></option>
                    {if isset($fields_value['mailchimp_lists']) && $fields_value['mailchimp_lists']}
                        {foreach $fields_value['mailchimp_lists'] as $mailchimp_list}
                            <option value="{$mailchimp_list.id|escape:'html':'UTF-8'}" {if isset($fields_value['mailchimp_list']) && $fields_value['mailchimp_list'] == $mailchimp_list.id}selected="selected"{/if}>{$mailchimp_list.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {else}
                        {if isset($fields_value['mailchimp_list']) && $fields_value['mailchimp_list'] !=''}
                            <option value="{$fields_value['mailchimp_list']|escape:'html':'UTF-8'}">{$fields_value['mailchimp_list']|escape:'html':'UTF-8'}</option>
                        {/if}
                    {/if}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <p class="alert alert-info" role="alert">
                    {l s='Fill in following inputs by mailchimp field tag' mod='gformbuilderpro'}<br/>
                    {l s='Ex: ' mod='gformbuilderpro'} EMAIL , FNAME , LNAME , ADDRESS[addr1] , ADDRESS[addr2] , ADDRESS[city], ADDRESS[state], ADDRESS[zip], ADDRESS[country], PHONE, MESSAGE ...
                </p>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{l s='Mailchimp Field tag' mod='gformbuilderpro'}
                             <a class="gfancybox_btn" href="#mailchimpmap_img" title="{l s='What is mailchimp field tag?' mod='gformbuilderpro'}"><i class="icon icon-question"></i></a>
                             <div style="display:none;" >
                                    <img id="mailchimpmap_img" src="../modules/gformbuilderpro/views/img/mailchimp_fieldtag.jpg" />
                                </div>
                             
                             </th>
                        <tr>
                    </thead>
                    <tbody>
                        {if isset($fields_value['shortcodes'][$fields_value['idlang_default']]) && $fields_value['shortcodes'][$fields_value['idlang_default']]}
                            {foreach $fields_value['shortcodes'][$fields_value['idlang_default']] as $shortcode}
                                {if $shortcode.id_gformbuilderprofields != 'user_ip' && $shortcode.id_gformbuilderprofields !='date_add'}
                                    <tr  class="{cycle values="odd,even"} mailchimpmap_{$shortcode.id_gformbuilderprofields|intval}">
                                        <td>{$shortcode.label|escape:'html':'UTF-8'}</td>
                                        <td><input type="text" name="mailchimpmap[{$shortcode.id_gformbuilderprofields|escape:'html':'UTF-8'}]" value="{if isset($fields_value['mailchimp_tag']) && isset($fields_value['mailchimp_tag'][$shortcode.id_gformbuilderprofields])}{$fields_value['mailchimp_tag'][$shortcode.id_gformbuilderprofields]|escape:'html':'UTF-8'}{/if}"/></td>
                                    </tr>
                                {/if}
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{elseif $input.type == 'klaviyomap'}
    <div class="col-lg-12 klaviyomap_wp"  style="{if !isset($fields_value['klaviyo']) ||  $fields_value['klaviyo'] == '0'} display:none;{/if}">
        {if isset($fields_value['klaviyo_apikey_empty']) && $fields_value['klaviyo_apikey_empty']}
            <div class="alert alert-danger" role="alert">
                <p class="alert-text">
                    {l s='Looks like your Klaviyo API key that you provide is not right or empty, Please go to "General Settings > Integrations" to correct it' mod='gformbuilderpro'}
                    <br/>
                    <a class="btn btn-default" target="_blank" href="{if isset($fields_value['config_link'])}{$fields_value['config_link']|escape:'html':'UTF-8'}{/if}" title="{l s='General Settings' mod='gformbuilderpro'}">
                        {l s='General Settings' mod='gformbuilderpro'}
                    </a>
                </p>
            </div>
        {/if}
        
        <div class="form-group">										
            <label class="control-label col-lg-3">{l s='Select klaviyo list' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <select name="klaviyolist">
                    <option value=""></option>
                    {if isset($fields_value['klaviyo_lists']) && $fields_value['klaviyo_lists']}
                        {foreach $fields_value['klaviyo_lists'] as $klaviyo_list}
                            <option value="{$klaviyo_list.id|escape:'html':'UTF-8'}" {if isset($fields_value['klaviyo_list']) && $fields_value['klaviyo_list'] == $klaviyo_list.id}selected="selected"{/if}>{$klaviyo_list.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {else}
                        {if isset($fields_value['klaviyo_list']) && $fields_value['klaviyo_list'] !=''}
                            <option value="{$fields_value['klaviyo_list']|escape:'html':'UTF-8'}">{$fields_value['klaviyo_list']|escape:'html':'UTF-8'}</option>
                        {/if}
                    {/if}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <div class="alert alert-info" role="alert">
                    <p class="alert-text">{l s='In order to integrate form elements with Klaviyo, you must input "email" to the Email field (corresponding field).' mod='gformbuilderpro'}</p>
                    <p class="alert-text">{l s='Regarding other fields, you can name anything according to your needs.' mod='gformbuilderpro'}</p>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Map' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Field label in Klaviyo' mod='gformbuilderpro'}">{l s='Klaviyo Field Label' mod='gformbuilderpro'}</span>
                                 <a class="gfancybox_btn" href="#klaviyomap_img" title=""><i class="icon icon-question"></i></a>
                                <div style="display:none;" >
                                    <img id="klaviyomap_img" src="../modules/gformbuilderpro/views/img/klaviyomap.jpg" />
                                </div>
                            </th>
                        <tr>
                    </thead>
                    <tbody>
                        {if isset($fields_value['shortcodes'][$fields_value['idlang_default']]) && $fields_value['shortcodes'][$fields_value['idlang_default']]}
                            {foreach $fields_value['shortcodes'][$fields_value['idlang_default']] as $shortcode}
                                {if $shortcode.id_gformbuilderprofields != 'user_ip' && $shortcode.id_gformbuilderprofields !='date_add'}
                                    <tr  class="{cycle values="odd,even"}  klaviyomap_{$shortcode.id_gformbuilderprofields|intval}">
                                        <td>{$shortcode.label|escape:'html':'UTF-8'}</td>
                                        <td><input type="text" name="klaviyomap[{$shortcode.id_gformbuilderprofields|escape:'html':'UTF-8'}]" value="{if isset($fields_value['klaviyo_label']) && isset($fields_value['klaviyo_label'][$shortcode.id_gformbuilderprofields])}{$fields_value['klaviyo_label'][$shortcode.id_gformbuilderprofields]|escape:'html':'UTF-8'}{/if}"/></td>
                                    </tr>
                                {/if}
                            {/foreach}
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>     
{elseif $input.type == 'zapiermap'}
    <div class="col-lg-12 zapiermap_wp"  style="{if !isset($fields_value['zapier']) ||  $fields_value['zapier'] == '0'} display:none;{/if}">
        <div class="form-group">										
            <label class="control-label col-lg-3">{l s='Webhook url' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <input type="text" name="zapierwebhook" class="" value="{if isset($fields_value['webhook_url'])}{$fields_value['webhook_url']|escape:'html':'UTF-8'}{/if}" />
            </div>
        </div>
    </div>    
{elseif $input.type == 'formbuilder'}
    <div class="clear"></div>
    <script type="text/javascript">
        var empty_danger = "{l s=' field is required' mod='gformbuilderpro'}";
    </script>
    <div  class="col-lg-12 formbuilder_new_design ">
        <div class="row">

            <div class="col-lg-12">
                <div id="formbuilder" class="row">
                    {if isset($fields_value['formtemplate'])  && $fields_value['formtemplate'] !=''}
                        {if isset($isps17) && $isps17}
                            {$fields_value['formtemplate'] nofilter}{* $fields_value['formtemplate'] is html content, no need to escape*}
                        {else}
                            {$fields_value['formtemplate']}{* $fields_value['formtemplate'] is html content, no need to escape*}
                        {/if}
                    {/if}

                </div>
                <div class="add_new_group">
                    <a href="#itemfieldparent_wp" class="add_element btn btn-default">
                        {if isset($psversion15) && $psversion15 == '-1'}
                            {l s='Add new' mod='gformbuilderpro'}
                        {else}
                            <i class="icon icon-plus"></i>
                        {/if}
                    </a>
                </div>
                <div style="display:none;">
                    <a id="popup_field_config_link" href="#popup_field_config"></a>
                    <input id="ajaxurl" value="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=gformbuilderpro&getFormTypeConfig=1" />
                    
                    <div id="control_box">
                        <div class="control_box_wp control_box_wp1">
                            <ul>
                                <li><a class="formbuilder_move"  title="{l s='Drag to move this field' mod='gformbuilderpro'}"><i class="icon-move"></i></a></li>
                                <li class="formbuilder_edit_wp"><a class="formbuilder_edit"  title="{l s='Edit' mod='gformbuilderpro'}"><i class="icon-pencil"></i></a></li>
                                <li class="formbuilder_duplicate_wp"><a class="formbuilder_duplicate"  title="{l s='Clone this field' mod='gformbuilderpro'}">
                                        {if isset($psversion15) && $psversion15 == '-1'}
                                            {l s='Clone' mod='gformbuilderpro'}
                                        {else}
                                            <i class="icon-copy"></i>
                                        {/if}
                                    </a></li>
                                <li class="delete_btn_background"><a class="formbuilder_delete"  title="{l s='Delete' mod='gformbuilderpro'}"><i class="icon-trash"></i></a></li>
                            </ul>

                        </div>
                    </div>
                    <div id="control_group">
                        <div class="control_group_wp control_box_wp">
                            <ul class="left_control_box">
                                <li><a class="formbuilder_move" title="{l s='Drag row to reorder' mod='gformbuilderpro'}"><i class="icon-move"></i></a></li>
                                <li class="formbuilder_nbr_cl_wp">
                                    <a class="formbuilder_change_nbr_cl" title="{l s='Change number column' mod='gformbuilderpro'}">
                                        {if isset($psversion15) && $psversion15 == '-1'}
                                            {l s='Change number column' mod='gformbuilderpro'}
                                        {else}
                                            <i class="icon-list"></i>
                                        {/if}
                                    </a>
                                    <ul class="formbuilder_nbr_cl_config">
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_12" data-cells="12" title="12"></a></li>
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_6_6" data-cells="6_6" title="6:6"></a></li>
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_8_4" data-cells="8_4" title="8:4"></a></li>
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_4_8" data-cells="4_8" title="4:8"></a></li>
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_9_3" data-cells="9_3" title="9:3"></a></li>
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_4_4_4" data-cells="4_4_4" title="4:4:4"></a></li>
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_3_6_3" data-cells="3_6_3" title="3:6:3"></a></li>
                                        <li><a class="formbuilder_set_columns formbuilder_set_column_3_3_3_3" data-cells="3_3_3_3" title="3:3:3:3"></a></li>
                                        <li><a href="#formbuilder_set_column_custom" class="formbuilder_set_columns formbuilder_set_column_custom" data-cells="custom" title="{l s='Custom' mod='gformbuilderpro'}">{l s='Custom' mod='gformbuilderpro'}</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="formbuilder_add_more" title="{l s='Add column' mod='gformbuilderpro'}">
                                        {if isset($psversion15) && $psversion15 == '-1'}
                                            {l s='Add new' mod='gformbuilderpro'}
                                        {else}
                                            <i class="icon icon-plus"></i>
                                        {/if}
                                    </a>
                                </li>
                            </ul>
                            <ul class="right_control_box">
                                <li><a class="formbuilder_minify" title="{l s='Togge row' mod='gformbuilderpro'}">
                                        {if isset($psversion15) && $psversion15 == '-1'}
                                            {l s='Togge' mod='gformbuilderpro'}
                                        {else}
                                            <i class="icon-minify"></i>
                                        {/if}
                                    </a></li>
                                <li>
                                    <a class="formbuilder_duplicate_group" title="{l s='Clone this row' mod='gformbuilderpro'}">
                                        {if isset($psversion15) && $psversion15 == '-1'}
                                            {l s='Clone' mod='gformbuilderpro'}
                                        {else}
                                            <i class="icon-copy"></i>
                                        {/if}
                                    </a>
                                </li>
                                <li  class="delete_btn_background"><a class="formbuilder_delete formbuilder_delete_group"   title="{l s='Delete this row' mod='gformbuilderpro'}"><i class="icon-trash"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="control_column">
                        <a class="add_new_element" href="#itemfieldparent_wp" title="{l s='Prepend to this column' mod='gformbuilderpro'}">
                            {if isset($psversion15) && $psversion15 == '-1'}
                                {l s='Add new' mod='gformbuilderpro'}
                            {else}
                                <i class="icon icon-plus"></i>
                            {/if}
                        </a>
                    </div>
                    <div class="control_column_top_wp">
                        <div class="control_column_top">
                            <a class="formbuilder_column_move"  title="{l s='Drag to move this column' mod='gformbuilderpro'}"><i class="icon-move"></i></a>
                            <a class="add_new_element_top" href="#itemfieldparent_wp" title="{l s='Prepend to this column' mod='gformbuilderpro'}">
                                {if isset($psversion15) && $psversion15 == '-1'}
                                    {l s='Add new' mod='gformbuilderpro'}
                                {else}
                                    <i class="icon icon-plus"></i>
                                {/if}
                            </a>
                            <a class="edit_control_column_top"  href="#edit_column_wp" title="{l s='Edit' mod='gformbuilderpro'}"><i class="icon icon-pencil"></i></a>
                            <a class="formbuilder_delete delete_control_column_top " href="" title="{l s='Delete this column' mod='gformbuilderpro'}"><i class="icon icon-trash"></i></a>
                        </div>
                    </div>

                    <div id="edit_column_wp" class="bootstrap">
                        <div class="panel-heading">{l s='Column Settings' mod='gformbuilderpro'}</div>
                        <div class="edit_column_data defaultForm form-horizontal">
                            <div class="form-wrapper">
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Element ID' mod='gformbuilderpro'}</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="element_id" value="" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Extra class name' mod='gformbuilderpro'}</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="element_extra_class" value="" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Responsive Options' mod='gformbuilderpro'}</label>
                                    <div class="col-lg-9">
                                        <div class="form-group">
                                            <label class="control-label col-lg-2"><i class="icon-laptop"></i></label>
                                            <div class="col-lg-10">
                                                <select rel="md" class="formbuilder_column_width">
                                                    <option  value="1">{l s='1 column - 1/12' mod='gformbuilderpro'}</option>
                                                    <option  value="1">{l s='2 columns - 1/6' mod='gformbuilderpro'}</option>
                                                    <option  value="3">{l s='3 columns - 1/4' mod='gformbuilderpro'}</option>
                                                    <option  value="4">{l s='4 columns - 1/3' mod='gformbuilderpro'}</option>
                                                    <option  value="5">{l s='5 columns - 5/12' mod='gformbuilderpro'}</option>
                                                    <option  value="6">{l s='6 columns - 1/2' mod='gformbuilderpro'}</option>
                                                    <option  value="7">{l s='7 columns - 7/12' mod='gformbuilderpro'}</option>
                                                    <option  value="8">{l s='8 columns - 2/3' mod='gformbuilderpro'}</option>
                                                    <option  value="9">{l s='9 columns - 3/4' mod='gformbuilderpro'}</option>
                                                    <option  value="10">{l s='10 columns - 5/6' mod='gformbuilderpro'}</option>
                                                    <option  value="11">{l s='11 columns - 11/12' mod='gformbuilderpro'}</option>
                                                    <option  value="12">{l s='12 columns - 1/1' mod='gformbuilderpro'}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-lg-2"><i class="icon-tablet"></i></label>
                                            <div class="col-lg-10">
                                                <select rel="sm" class="formbuilder_column_width_sm">
                                                    <option  value="1">{l s='1 column - 1/12' mod='gformbuilderpro'}</option>
                                                    <option  value="1">{l s='2 columns - 1/6' mod='gformbuilderpro'}</option>
                                                    <option  value="3">{l s='3 columns - 1/4' mod='gformbuilderpro'}</option>
                                                    <option  value="4">{l s='4 columns - 1/3' mod='gformbuilderpro'}</option>
                                                    <option  value="5">{l s='5 columns - 5/12' mod='gformbuilderpro'}</option>
                                                    <option  value="6">{l s='6 columns - 1/2' mod='gformbuilderpro'}</option>
                                                    <option  value="7">{l s='7 columns - 7/12' mod='gformbuilderpro'}</option>
                                                    <option  value="8">{l s='8 columns - 2/3' mod='gformbuilderpro'}</option>
                                                    <option  value="9">{l s='9 columns - 3/4' mod='gformbuilderpro'}</option>
                                                    <option  value="10">{l s='10 columns - 5/6' mod='gformbuilderpro'}</option>
                                                    <option  value="11">{l s='11 columns - 11/12' mod='gformbuilderpro'}</option>
                                                    <option  value="12">{l s='12 columns - 1/1' mod='gformbuilderpro'}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-lg-2"><i class="icon-mobile"></i></label>
                                            <div class="col-lg-10">
                                                <select rel="xs" class="formbuilder_column_width_xs">
                                                    <option  value="1">{l s='1 column - 1/12' mod='gformbuilderpro'}</option>
                                                    <option  value="1">{l s='2 columns - 1/6' mod='gformbuilderpro'}</option>
                                                    <option  value="3">{l s='3 columns - 1/4' mod='gformbuilderpro'}</option>
                                                    <option  value="4">{l s='4 columns - 1/3' mod='gformbuilderpro'}</option>
                                                    <option  value="5">{l s='5 columns - 5/12' mod='gformbuilderpro'}</option>
                                                    <option  value="6">{l s='6 columns - 1/2' mod='gformbuilderpro'}</option>
                                                    <option  value="7">{l s='7 columns - 7/12' mod='gformbuilderpro'}</option>
                                                    <option  value="8">{l s='8 columns - 2/3' mod='gformbuilderpro'}</option>
                                                    <option  value="9">{l s='9 columns - 3/4' mod='gformbuilderpro'}</option>
                                                    <option  value="10">{l s='10 columns - 5/6' mod='gformbuilderpro'}</option>
                                                    <option  value="11">{l s='11 columns - 11/12' mod='gformbuilderpro'}</option>
                                                    <option  value="12">{l s='12 columns - 1/1' mod='gformbuilderpro'}</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" value="1" id="change_column_btn" class="btn btn-default pull-right module_form_submit_btn">{l s='Save change' mod='gformbuilderpro'}</button>
                                <button type="submit" class="btn btn-default btn btn-default pull-left cancel_column_btn" >{l s='Cancel' mod='gformbuilderpro'}</button>
                            </div>

                        </div>
                    </div>
                    <div id="formbuilder_set_column_custom" class="bootstrap">
                        <div class="panel-heading">{l s='Row layout' mod='gformbuilderpro'}</div>
                        <div class="defaultForm form-horizontal popup_content_wp">
                            {foreach $row_layouts as $row_layout}
                                <div class="row_layout_choose">
                                    <p class="row_layout_choose_title"><strong>{$row_layout.title|escape:'html':'UTF-8'}</strong></p>
                                    <ul class="formbuilder_nbr_cl_popup_config">
                                        {foreach $row_layout.layouts as $layout}
                                            <li><a class="formbuilder_popup_set_columns formbuilder_set_column_{$layout|escape:'html':'UTF-8'}" data-cells="{$layout|escape:'html':'UTF-8'}" title="{$layout|escape:'html':'UTF-8'}"></a></li>
                                        {/foreach}
                                    </ul>
                                </div>
                                <div class="clear"></div>
                            {/foreach}
                            <p>{l s='Change particular row layout manually by specifying number of columns and their size value' mod='gformbuilderpro'}</p>
                            <p><strong>{l s='Enter custom layout for your row'  mod='gformbuilderpro'}</strong></p>
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <input type="text" value="" class="set_column_custom_data">
                                    <p class="help-block">{l s='Separate each value by the _ character. Sum of these values must equal to 12 (Ex  6_3_3)'  mod='gformbuilderpro'}</p>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" value="1" id="change_row_btn" class="btn btn-default pull-right module_form_submit_btn">{l s='Apply' mod='gformbuilderpro'}</button>
                            <button type="submit" class="btn btn-default btn btn-default pull-left cancel_column_btn" >{l s='Cancel' mod='gformbuilderpro'}</button>
                        </div>
                    </div>

                </div>
            </div>
            <textarea class="hidden" name="formtemplate" id="formbuilder_content">{if isset($fields_value['formtemplate'])}{if isset($isps17) && $isps17}{$fields_value['formtemplate'] nofilter}{else}{$fields_value['formtemplate']}{/if}{/if}</textarea>{* $fields_value['formtemplate'] is html content, no need to escape*}
            <input type="hidden" name="deletefields" id="deletefields" value="" />

            <div class="blank_page">
                {if isset($fields_value['blank_img'])}
                    <img src="{$fields_value['blank_img']|escape:'html':'UTF-8'}" alt=""/>
                {/if}
                <p class="blank_page_title">{l s='You have a blank page' mod='gformbuilderpro'}</p>
                <p class="blank_page_desc"></p>
                <a href="#itemfieldparent_wp" class="add_element btn btn-default new_design_bt">
                    {if isset($psversion15) && $psversion15 == '-1'}
                    {else}
                        <i class="icon icon-plus"></i>
                    {/if}
                    {l s='Add Element' mod='gformbuilderpro'}</a>
            </div>
            <div style="display:none;">
                <div  id="itemfieldparent_wp">
                    <div class="panel-heading">{l s='Add Element'  mod='gformbuilderpro'}</div>
                    <div class="form-wrapper">
                        <div id="itemfieldparent">
                            <div data-type="newrow" class="itemfield">
                                <div class="field_content">
                                    <span class="field_icon"><img src="../modules/gformbuilderpro/views/img/add_row.png" alt="" /></span>
                                    <span class="field_desc_wp">
                                        <span class="feildlabel">{l s='Row'  mod='gformbuilderpro'}</span> <span class="feildname"></span>
                                        <span class="field_desc">{l s='Place field elements inside the row'  mod='gformbuilderpro'}</span>
                                    </span>
                                </div>
                            </div>
                            {if isset($fields_value['allfieldstype'])}
                                {foreach $fields_value['allfieldstype'] as $key=>$field}
                                    <div data-type="{$key|escape:'html':'UTF-8'}" data-newitem="1" class="itemfield">
                                        <div class="field_content">
                                            {if isset($field.icon) && $field.icon != ''}
                                                <span class="field_icon"><img src="{$field.icon|escape:'html':'UTF-8'}" alt="" /></span>
                                            {/if}
                                            <span class="field_desc_wp">
                                                    <span class="feildlabel">{$field.label|escape:'html':'UTF-8'}</span> <span class="feildname"></span>
                                                    {if isset($field.desc) && $field.desc != ''}
                                                        <span class="field_desc">{$field.desc|escape:'html':'UTF-8'}</span>
                                                    {/if}
                                                </span>
                                        </div>
                                        <span class="shortcode"></span>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-default btn btn-default pull-left cancel_column_btn" >{l s='Cancel' mod='gformbuilderpro'}</button>
                    </div>
                </div>
                <input type="hidden" value="{$fields_value['group_width_default']|escape:'html':'UTF-8'}" id="group_width_default" />
                <input type="hidden" value="{$fields_value['idlang_default']|escape:'html':'UTF-8'}" id="idlang_default" />
                <input type="hidden" value="{$fields_value['loadjqueryselect2']|escape:'html':'UTF-8'}" id="loadjqueryselect2" />
                
                
                
                
            </div>
        </div>
    </div>
    
    
    
    
    {else if $input.type =='publish'}
    {if isset($gshortcode)}
        <div class="col-lg-4">
            <div class="publish_box">
                <div class="box-heading"><i class="icon icon-link"></i>{l s='Direct link of your form' mod='gformbuilderpro'}</div>
                <div class="gbox_content">
                    <p class="form-group gbox_content_help">{l s='This is the form link. You can put this link to the Menu, add to CMS page content, send to customer, etc.' mod='gformbuilderpro'}</p>
                    <p class="form-group flex_box copy_group">
                        <input class="copy_data" type="text" value="{$formlink|escape:'html':'UTF-8'}">
                        <a class="copy_link btn btn-default pull-right gdefault_btn"  href="{$formlink|escape:'html':'UTF-8'}">{l s='Copy' mod='gformbuilderpro'}</a>
                    </p>
                    <p class="form-group">
                        <a class="btn btn-default pull-right gprimary_btn"  href="{$formlink|escape:'html':'UTF-8'}" target="_blank">{l s='Open in new tab' mod='gformbuilderpro'}</a>

                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="publish_box">
                <div class="box-heading"><i class="icon icon-code"></i>{l s='Shortcodes , smarty hook' mod='gformbuilderpro'}</div>
                <div class="gbox_content">
                    <p class="form-group highlight_text">{l s='SHORTCODES' mod='gformbuilderpro'}</p>
                    <p class="form-group gbox_content_help">{l s='You can use the short code to add the form to content of cms page or product description' mod='gformbuilderpro'}</p>
                    <p class="form-group flex_box copy_group">
                        <input class="gshortcode copy_data" type="text" value="{$gshortcode|escape:'html':'UTF-8'}" />
                        <a href="" class="copy_link btn btn-default pull-right gdefault_btn">{l s='Copy' mod='gformbuilderpro'}</a>
                    </p>
                    <p class="form-group highlight_text">{l s='SMARTY HOOK' mod='gformbuilderpro'}</p>
                    <p class="form-group gbox_content_help">{l s='It is prestashop custom hook. You can add the code to any .tpl file if you want to display the form' mod='gformbuilderpro'}</p>
                    <p class="form-group flex_box copy_group">
                        <input class="gsmartycode copy_data" type="text" value="{$smartycode|escape:'html':'UTF-8'}">
                        <a href="" class="copy_link btn btn-default pull-right gdefault_btn">{l s='Copy' mod='gformbuilderpro'}</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="publish_box">
                <div class="box-heading"><i class="icon icon-download"></i>{l s='Export' mod='gformbuilderpro'}</div>
                <div class="gbox_content">
                    <p class="form-group gbox_content_help">{l s='You can export the form, then import it to another stores.' mod='gformbuilderpro'}</p>
                    <p class="form-group">
                        <a class="btn btn-default pull-right gprimary_btn"  href="{$export_link|escape:'html':'UTF-8'}" target="_blank">{l s='Export' mod='gformbuilderpro'}</a>
                    </p>
                </div>
            </div>
        </div>
    {/if}
    {else if $input.type =='autoredirect'}
    <div  class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="autoredirect" id="autoredirect_on" value="1"{if $fields_value['autoredirect'] == '1'} checked="checked"{/if}/>
				{strip}
                    <label for="autoredirect_on">
						{l s='Yes' mod='gformbuilderpro'}
				</label>
                {/strip}
                <input type="radio" name="autoredirect"  id="autoredirect_off" value="0"{if $fields_value['autoredirect'] == '0'} checked="checked"{/if}/>
				{strip}
                    <label for="autoredirect_off">
						{l s='No' mod='gformbuilderpro'}
				</label>
                {/strip}
				<a class="slide-button btn"></a>
			</span>
        <div style="clear:both;"></div>
        <div class="autoredirect_config publish_box" style="{if $fields_value['autoredirect'] == '0'} display:none;{/if}margin-top: 10px;">
            <div class="box-heading"><i class="icon-cogs"></i>{l s='Redirect config' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
                <div class="col-xs-3">
                    <p>{l s='Time delay' mod='gformbuilderpro'}</p>
                    <div class="input-group col-lg-12">
                        <input maxlength="14" id="timedelay" name="timedelay" type="text" value="{if isset($fields_value['timedelay']) && $fields_value['timedelay'] > 0}{$fields_value['timedelay']|intval}{/if}" />
                        <span class="input-group-addon">{l s='ms' mod='gformbuilderpro'}</span>
                    </div>
                </div>
                <div class="col-xs-8">
                    <p>{l s='Redirect link' mod='gformbuilderpro'}</p>
                    <div class="input-group col-lg-12">
                        <input id="redirect_link" name="redirect_link" type="text" value="{if isset($fields_value['redirect_link']) && $fields_value['redirect_link'] !=''}{$fields_value['redirect_link']|escape:'html':'UTF-8'}{/if}" />
                    </div>
                    {if $languages|count > 1}
                        <p class="redirect_link_lang_title">{l s='Or Redirect link by language' mod='gformbuilderpro'}</p>
                        {foreach from=$languages item=language}
                            <div class="redirect_link_lang input-group col-lg-12">
                                <span class="input-group-addon">{$language.iso_code|escape:'html':'UTF-8'}</span>
                                <input name="redirect_link_lang_{$language.id_lang|escape:'html':'UTF-8'}" type="text" value="{if isset($fields_value['redirect_link_lang']) && isset($fields_value['redirect_link_lang'][$language.id_lang]) && $fields_value['redirect_link_lang'][$language.id_lang] !=''}{$fields_value['redirect_link_lang'][$language.id_lang]|escape:'html':'UTF-8'}{/if}" />
                            </div>
                        {/foreach}
                    {/if}
                </div><div style="clear:both;"></div>
            </div>
        </div>
    </div>
    {else if $input.type =='openviapopup'}
    <div  class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="ispopup" id="ispopup_on" value="1"{if $fields_value['ispopup'] == '1'} checked="checked"{/if}/>
				{strip}
                    <label for="ispopup_on">
						{l s='Yes' mod='gformbuilderpro'}
				</label>
                {/strip}
                <input type="radio" name="ispopup"  id="ispopup_off" value="0"{if $fields_value['ispopup'] == '0'} checked="checked"{/if}/>
				{strip}
                    <label for="ispopup_off">
						{l s='No' mod='gformbuilderpro'}
				</label>
                {/strip}
				<a class="slide-button btn"></a>
			</span>
        <div style="clear:both;"></div>
        <div class="ispopup_config publish_box" style="{if $fields_value['ispopup'] == '0'}display:none;{/if}margin-top: 10px;">
            <div class="box-heading"><i class="icon-cogs"></i>{l s='Popup config' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
                <div class="col-xs-3">
                    <p>{l s='Button label' mod='gformbuilderpro'}</p>
                </div>
                <div class="col-xs-8">
                    {foreach from=$languages item=language}
                        {if $languages|count > 1}
                            <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                        {/if}
                        <div class="col-lg-6">
                            <input id="popup_label_{$language.id_lang|intval}" type="text" name="popup_label_{$language.id_lang|intval}" class="" value="{if isset($fields_value['popup_label']) && isset($fields_value['popup_label'][$language.id_lang]) && $fields_value['popup_label'][$language.id_lang] !=''}{$fields_value['popup_label'][$language.id_lang]|escape:'html':'UTF-8'}{/if}" />
                        </div>
                        {if $languages|count > 1}
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code|escape:'html':'UTF-8'}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=lang}
                                        <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                                    {/foreach}
                                </ul>
                            </div>
                        {/if}
                        {if $languages|count > 1}
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    {else if $input.type =='attachfiles'}
    <div  class="col-lg-9">
        <div id="{$input.name|escape:'html':'UTF-8'}_box" class="attachfiles_box">
            <div class="attachfiles_dynamic">
                {if isset($fields_value['filefields']) && isset($fields_value['filefields'][$input.name]) && $fields_value['filefields'][$input.name]}
                    {foreach $fields_value['filefields'][$input.name] as $filefield}
                        <p class="file_{$filefield.name|escape:'html':'UTF-8'}"><input class="gformattachfile" id="admin_attachfiles_{$filefield.name|escape:'html':'UTF-8'}" type="checkbox" value="{$filefield.name|escape:'html':'UTF-8'}" {if isset($filefield.checked) && $filefield.checked} checked="checked"{/if} ><label for="admin_attachfiles_{$filefield.name|escape:'html':'UTF-8'}"><code>{literal}{{/literal}{$filefield.name|escape:'html':'UTF-8'}{literal}}{/literal}</code></label></p>
                    {/foreach}
                {/if}
            </div>
            <input type="hidden" id="{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}" value="{if isset($fields_value[$input.name])}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}">
        </div>
    </div>
    {else if $input.type =='using_email_template'}
    <div  class="col-lg-9">
        <div class="emailshortcode_action">
            <a class="btn btn-default choose_email_template_box" href="#choose_email_template_box"><i class="icon-random"></i>{l s='Choose email template' mod='gformbuilderpro'}</a>
        </div>
        <div style="display:none;">
            <div id="choose_email_template_box">
                <div class="panel-heading">{l s='Email templates' mod='gformbuilderpro'}</div>
                <div class="form-wrapper">
                    <div class="list_email_template">
                        <ul class="ul_list_email_templates">
                            <li class="gemail_template" rel="emaildefault"><a class="gemail_template_select" href="emaildefault"><img src="../modules/gformbuilderpro/views/img/emaildefault.png" /></a></li>
                            <li class="gemail_template" rel="template1"><a class="gemail_template_select" href="template1"><img src="../modules/gformbuilderpro/views/img/template1.png" /></a></li>
                            <li class="gemail_template" rel="template2"><a class="gemail_template_select" href="template2"><img src="../modules/gformbuilderpro/views/img/template2.png" /></a></li>
                            <li class="gemail_template" rel="template3" style="clear: left;"><a class="gemail_template_select" href="template3"><img src="../modules/gformbuilderpro/views/img/template3.png" /></a></li>
                            <li class="gemail_template" rel="template4"><a class="gemail_template_select" href="template4"><img src="../modules/gformbuilderpro/views/img/template4.png" /></a></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-default module_form_submit_btn" id="gfromloaddefault">{l s='Apply' mod='gformbuilderpro'}</button>
                    <button type="submit" class="btn btn-default btn btn-default pull-left cancel_column_btn">{l s='Cancel' mod='gformbuilderpro'}</button>
                </div>
            </div>
        </div>
    </div>
    {else if $input.type =='using_condition'}
    <div  class="col-lg-9">
        <div class="form-group">
            <label class="control-label col-lg-2">{l s='Using Condition' mod='gformbuilderpro'}</label>
            <div  class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
        				<input type="radio" name="using_condition" id="using_condition_on" value="1"{if isset($fields_value['using_condition']) && $fields_value['using_condition'] == '1'} checked="checked"{/if}/>
        				{strip}
                            <label for="using_condition_on">
        						{l s='Yes' mod='gformbuilderpro'}
        				</label>
                        {/strip}
                        <input type="radio" name="using_condition"  id="using_condition_off" value="0"{if !isset($fields_value['using_condition']) || $fields_value['using_condition'] == '0'} checked="checked"{/if}/>
        				{strip}
                            <label for="using_condition_off">
        						{l s='No' mod='gformbuilderpro'}
        				</label>
                        {/strip}
        				<a class="slide-button btn"></a>
        			</span>
            </div>
        </div>
        <div class="using_condition_config publish_box" style="{if !isset($fields_value['using_condition']) || $fields_value['using_condition'] == '0'}display:none;{/if}margin-top: 10px;">
            <div class="box-heading"><i class="icon-random"></i> {l s='Condition' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
                <table id="using_condition_config_wp" class="table">
                    <thead>
                    <tr>
                        <td>{l s='If' mod='gformbuilderpro'}</td>
                        <td>{l s='State' mod='gformbuilderpro'}</td>
                        <td>
                            {l s='Value' mod='gformbuilderpro'}
                            <div class="form-group pull-right" style="position: relative;">
                                {foreach from=$languages item=language}
                                    {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                    {/if}
                                    {if $languages|count > 1}
                                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                            {$language.iso_code|escape:'html':'UTF-8'}
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            {foreach from=$languages item=lang}
                                                <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                                            {/foreach}
                                        </ul>
                                    {/if}
                                    {if $languages|count > 1}
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        </td>
                        <td>{l s='Send email to' mod='gformbuilderpro'}</td>
                        <td>

                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    {if isset($fields_value['condition_configs']) && $fields_value['condition_configs']}
                        {foreach $fields_value['condition_configs'] as $condition_config}
                            <tr class="condition_form_item">
                                <td>
                                    <div class="form-group">
                                        <select class="condition_config_if">
                                            <option value="" selected="selected"></option>
                                            {if isset($fields_value['shortcodes'][$language.id_lang]) && $fields_value['shortcodes'][$language.id_lang]}
                                                {foreach $fields_value['shortcodes'][$language.id_lang] as $shortcode}
                                                    <option {if isset($condition_config['if']) && $condition_config['if'] == $shortcode.shortcode} selected="selected" {/if} value="{$shortcode.shortcode|escape:'html':'UTF-8'}">{$shortcode.label|escape:'html':'UTF-8'}:{$shortcode.shortcode|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select class="condition_config_state">
                                            <option value="1" {if !isset($condition_config['state']) || $condition_config['state'] == 1} selected="selected" {/if} >{l s='is equal to ' mod='gformbuilderpro'}</option>
                                            <option value="2" {if isset($condition_config['state']) && $condition_config['state'] == 2} selected="selected" {/if}>{l s='is not' mod='gformbuilderpro'}</option>
                                            <option value="3" {if isset($condition_config['state']) && $condition_config['state'] == 3} selected="selected" {/if}>{l s='is greater than' mod='gformbuilderpro'}</option>
                                            <option value="4" {if isset($condition_config['state']) && $condition_config['state'] == 4} selected="selected" {/if}>{l s='is less than' mod='gformbuilderpro'}</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        {foreach from=$languages item=language}
                                            {if $languages|count > 1}
                                                <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            {/if}
                                            <input type="text" class="condition_config_value" rel="{$language.id_lang|escape:'html':'UTF-8'}" value="{if isset($condition_config['value']) && isset($condition_config['value'][$language.id_lang]) && $condition_config['value'][$language.id_lang] != ''}{$condition_config['value'][$language.id_lang]|escape:'html':'UTF-8'}{/if}" />
                                            {if $languages|count > 1}
                                                </div>
                                            {/if}
                                        {/foreach}
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="condition_config_email_to" value="{if isset($condition_config['email'])}{$condition_config['email']|escape:'html':'UTF-8'}{/if}" />
                                    </div>
                                </td>
                                <td><div class="form-group"><a class="gremove_condition btn btn-default"><i class="icon-trash"></i></a></div></td>
                            </tr>
                        {/foreach}
                    {/if}
                    </tbody>
                </table>

                <div style="display:none;">
                    <textarea id="condition_config_data" name="condition_configs">{if isset($fields_value['condition_configs_json'])}{$fields_value['condition_configs_json']|escape:'html':'UTF-8'}{/if}</textarea>
                </div>
                <table id="using_condition_to_clone" class="table" style="display:none;">
                    <tr class="condition_form_item">
                        <td>
                            <div class="form-group">
                                <select class="condition_config_if">
                                    <option value="" selected="selected"></option>
                                    {if isset($fields_value['shortcodes'][$language.id_lang]) && $fields_value['shortcodes'][$language.id_lang]}
                                        {foreach $fields_value['shortcodes'][$language.id_lang] as $shortcode}
                                            <option value="{$shortcode.shortcode|escape:'html':'UTF-8'}">{$shortcode.label|escape:'html':'UTF-8'}:{$shortcode.shortcode|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <select class="condition_config_state">
                                    <option value="1">{l s='is equal to ' mod='gformbuilderpro'}</option>
                                    <option value="2">{l s='is not' mod='gformbuilderpro'}</option>
                                    <option value="3">{l s='is greater than' mod='gformbuilderpro'}</option>
                                    <option value="4">{l s='is less than' mod='gformbuilderpro'}</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                {foreach from=$languages item=language}
                                    {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                    {/if}
                                    <input type="text" class="condition_config_value" rel="{$language.id_lang|escape:'html':'UTF-8'}" value="" />
                                    {if $languages|count > 1}
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="text" class="condition_config_email_to" value="" />
                            </div>
                        </td>
                        <td><div class="form-group"><a class="gremove_condition btn btn-default"><i class="icon-trash"></i></a></div></td>
                    </tr>
                </table>
                <button id="new_condition_config" class="btn btn-default">
                    {if isset($psversion15) && $psversion15 == '-1'}
                    {else}
                        <i class="icon icon-plus"></i>
                    {/if}
                    {l s='New' mod='gformbuilderpro'}</button>
            </div>
        </div>
    </div>
    {else if $input.type == 'formbuildertabopen2'}
    {if !isset($fields_value['psoldversion15']) || $fields_value['psoldversion15'] != -1}
        </div>
    {/if}
    <div id="{$input.name|escape:'html':'UTF-8'}" class="{if $input.name == 'mailchimp' || $input.name == 'klaviyo' || $input.name == 'zapier'}formbuilder_integration_tab{else}formbuilder_email_tab{/if} {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} {if $input.name == 'adminemail' || $input.name == 'mailchimp'}activetab{/if}">
        {if !isset($fields_value['psoldversion15']) || $fields_value['psoldversion15'] != -1}
        <div>
        {/if}
        {else if $input.type == 'formbuildertabopen'}
            {if !isset($fields_value['psoldversion15']) || $fields_value['psoldversion15'] != -1}
                </div>
            {/if}
            <div id="{$input.name|escape:'html':'UTF-8'}" class="formbuilder_tab {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
            {if $input.name == 'tabemail'}
                <div class="col-lg-2">
                    <div class="productTabs gformbuilderpro_admintab_level2">
                		<ul class="tab nav nav-tabs">
                            <li class="tab-row active">
                				<a class="tab-page tab_email" href="#adminemail">{l s='Admin Email' mod='gformbuilderpro'}</a>
                			</li>
                			<li class="tab-row">
                				<a class="tab-page tab_email" href="#senderemail">{l s='Sender Email' mod='gformbuilderpro'}</a>
                			</li>
                            <li class="tab-row">
                				<a class="tab-page tab_email" href="#replyemail">{l s='Reply Email' mod='gformbuilderpro'}</a>
                			</li>
                		</ul>
                	</div>
                    <div class="emailshortcode_wp">
                        {foreach from=$languages item=language}
                            {if $languages|count > 1}
                                <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                            {/if}
                            <div class="publish_box emailshortcode_panel">
                                <div class="box-heading"><i class="icon-code"></i>{l s='Variables' mod='gformbuilderpro'}</div>
                                    <div class="gbox_content">
                                    <p class="help-block">{l s='Click to copy variable' mod='gformbuilderpro'}</p>
                                    <div class="emailshortcode">
                                        {if isset($fields_value['shortcodes'][$language.id_lang]) && $fields_value['shortcodes'][$language.id_lang]}
                                            <table>
                                                <tbody>
                                                {foreach $fields_value['shortcodes'][$language.id_lang] as $shortcode}
                                                    <tr class="{cycle values="odd,even"} copy_group"><td><span data-toggle="tooltip" class="glabel-tooltip copy_data copy_link" data-original-title="{$shortcode.label|escape:'html':'UTF-8'}">{$shortcode.shortcode|escape:'html':'UTF-8'}</span></td></tr>
                                                {/foreach}
                                                </tbody>
                                            </table>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            {if $languages|count > 1}
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                </div>
                <div class="col-lg-10 sub_tab_wp">

                


            {elseif $input.name == 'tabintegration'}
                <div class="col-lg-2">
                    <div class="productTabs gformbuilderpro_admintab_level2">
                		<ul class="tab nav nav-tabs">
                            <li class="tab-row active">
                				<a class="tab-page tab_integration" href="#mailchimp">{l s='Mailchimp' mod='gformbuilderpro'}</a>
                			</li>
                            <li class="tab-row">
                				<a class="tab-page tab_integration" href="#klaviyo">{l s='Klaviyo' mod='gformbuilderpro'}</a>
                			</li>
                            <li class="tab-row">
                				<a class="tab-page tab_integration" href="#zapier">{l s='Zapier' mod='gformbuilderpro'}</a>
                			</li>
                		</ul>
                	</div>
                </div>
                <div class="col-lg-10 sub_tab_wp">
            {/if}
            {if !isset($fields_value['psoldversion15']) || $fields_value['psoldversion15'] != -1}
                <div>
            {/if}
        {else if $input.type == 'formbuildertabclose'}
            {if $input.name == 'closetab3'}
                </div>
            {/if}
            </div>
        {else if $input.type == 'tags' && $fields_value['psoldversion15'] == -1}
            <div class="margin-form">
                {block name="input"}
                    {if isset($input.lang) AND $input.lang}
                        <div class="translatable">
                            {foreach $languages as $language}
                                <div class="lang_{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                                    {if $input.type == 'tags'}
                                    {literal}
                                        <script type="text/javascript">
                                            $().ready(function () {
                                                var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}{literal}';
                                                $('#'+input_id).tagify({addTagPrompt: '{/literal}{l s='Add tag' js=1 mod='gformbuilderpro'}{literal}'});
                                                $({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
                                                    $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                                });
                                            });
                                        </script>
                                    {/literal}
                                    {/if}
                                    {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                                    <input type="text"
                                           name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
                                           id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                                           value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                                           class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                                           {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                            {if isset($input.maxlength)}maxlength="{$input.maxlength|escape:'html':'UTF-8'}"{/if}
                                            {if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
                                            {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                                            {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
                                    {if !empty($input.hint)}<span class="hint" name="help_box">
                                        {if is_array($input.hint)}
                                            {foreach $input.hint as $hint}
                                                {$hint|escape:'htmlall':'UTF-8'}
                                            {/foreach}
                                        {else}
                                            {$input.hint|escape:'htmlall':'UTF-8'}
                                        {/if}
                                        </span>{/if}
                                </div>
                            {/foreach}
                        </div>
                    {else}
                    {if $input.type == 'tags'}
                    {literal}
                        <script type="text/javascript">
                            $().ready(function () {
                                var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}{literal}';
                                $('#'+input_id).tagify({addTagPrompt: '{/literal}{l s='Add tag' mod='gformbuilderpro'}{literal}'});
                                $({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
                                    $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                });
                            });
                        </script>
                    {/literal}
                    {/if}
                        {assign var='value_text' value=$fields_value[$input.name]}
                        <input type="text"
                               name="{$input.name|escape:'html':'UTF-8'}"
                               id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                               value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                               class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                               {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                {if isset($input.maxlength)}maxlength="{$input.maxlength|escape:'html':'UTF-8'}"{/if}
                                {if isset($input.class)}class="{$input.class|escape:'html':'UTF-8'}"{/if}
                                {if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
                                {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                                {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
                        {if isset($input.suffix)}{$input.suffix|escape:'html':'UTF-8'}{/if}
                        {if !empty($input.hint)}<span class="hint" name="help_box">
                            {if is_array($input.hint)}
                                {foreach $input.hint as $hint}
                                    {$hint|escape:'htmlall':'UTF-8'}
                                {/foreach}
                            {else}
                                {$input.hint|escape:'htmlall':'UTF-8'}
                            {/if}
                            </span>

                        {/if}
                    {/if}
                {/block}
            </div>
            {/if}
            {if ($input.type == 'tags' && $fields_value['psoldversion15'] == -1) || $input.type == 'customtags'}
            {else}
                {$smarty.block.parent}
            {/if}
{/block}