{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if isset($action)}
    {if $action == 'printStar'}
        <span rel="{$id_gformrequest|intval}" class="giconstar {if isset($star) && $star} gstarred {/if} label-tooltip" data-toggle="tooltip" data-placement="top" data-original-title="{if isset($star) && $star}{l s='Starred' mod='gformbuilderpro'}{else}{l s='Not starred' mod='gformbuilderpro'}{/if}" data-notstar="{l s='Not starred' mod='gformbuilderpro'}" data-iststar="{l s='Starred' mod='gformbuilderpro'}"  ><i class="icon-star"></i></span>
    {elseif $action == 'printShortcode'}
        <div class="copy_group flex_box printshortcode_inlist">
            <input class="printshortcode copy_data" type="text" value="{$shortcode|escape:'html':'UTF-8'}" />
            <a href="" class="copy_link btn btn-default pull-right gdefault_btn">{l s='Copy' mod='gformbuilderpro'}</a>
        </div>
    {elseif $action == 'printSmartyhook'}
        <div class="copy_group flex_box printshortcode_inlist">
            <input class="printshortcode copy_data" type="text" value="{$shortcode|escape:'html':'UTF-8'}" />
            <a href="" class="copy_link btn btn-default pull-right gdefault_btn">{l s='Copy' mod='gformbuilderpro'}</a>
        </div>
    {elseif $action == 'printSubject'}
        <span class="gform_subject {if !isset($viewed) || $viewed != 1}gunread{/if}">{$subject|escape:'html':'UTF-8'}</span>
    {elseif $action == 'printFrontlink'}
        <a href="{$url_rewrite|escape:'html':'UTF-8'}" target="_blank" class="btn btn-default btn_hover_new_style"><i class="icon-external-link"></i></a>
    {elseif $action == 'exportFormsToXml'}   
        {if $type == 'form'} 
        {literal}
        <?xml version="1.0" encoding="UTF-8"?>
            <gforms module_version="{/literal}{$module_version|escape:'html':'UTF-8'}{literal}">
        {/literal}
        {else}
        <datafields>
        {/if}
                {if isset($gforms) && $gforms}
                    {foreach $gforms as $key=>$gform}
                        {if $type == 'form'} 
                        <gform id="{$key|intval}">
                        {else}
                        <field id="{$key|intval}">
                        {/if}
                            {foreach $fields as $_key=>$field}
                                {if isset($gform[$_key]) && isset($field.lang) && $field.lang == 1}
                                    {foreach $gform[$_key] as $idlang=>$data_lang}
                                        {if isset($langs_iso[$idlang])}
                                            {if isset($field.validate) && $field.validate == 'isCleanHtml'}
                                                <{$_key|escape:'html':'UTF-8'} lang="{$langs_iso[$idlang]|escape:'html':'UTF-8'}">{if $data_lang !=''}<![CDATA[{$data_lang nofilter}]]>{else}{/if}</{$_key|escape:'html':'UTF-8'}>{* Html content. No need escape.*}
                                            {else}
                                                <{$_key|escape:'html':'UTF-8'}  lang="{$langs_iso[$idlang]|escape:'html':'UTF-8'}">{if $data_lang !=''}<![CDATA[{$data_lang|escape:'html':'UTF-8'}]]>{else}{/if}</{$_key|escape:'html':'UTF-8'}>
                                            {/if}
                                        {/if}
                                    {/foreach}
                                {else}
                                    {if isset($field.validate) && $field.validate == 'isCleanHtml'}
                                        <{$_key|escape:'html':'UTF-8'}>{if isset($gform[$_key]) && $gform[$_key] !=''}<![CDATA[{$gform[$_key] nofilter}]]>{else}{/if}</{$_key|escape:'html':'UTF-8'}>{* Html content. No need escape.*}
                                    {else}
                                        <{$_key|escape:'html':'UTF-8'}>{if isset($gform[$_key]) && $gform[$_key] !=''}{$gform[$_key]|escape:'html':'UTF-8'}{else}{/if}</{$_key|escape:'html':'UTF-8'}>
                                    {/if}
                                {/if}
                            {/foreach}
                            {if isset($type) && $type == 'form' && isset($gform['datafields']) && $gform['datafields'] !=''}
                                {if isset($isps17) && $isps17}
                                    {$gform['datafields'] nofilter}{* xml content. No need escape.*}
                                {else}
                                    {$gform['datafields']}{* xml content. No need escape.*}
                                {/if}
                            {/if}
                        {if $type == 'form'} 
                        </gform>
                        {else}
                        </field>
                        {/if}
                    {/foreach}
                {/if}
            {if $type == 'form'} 
            </gforms>
            {else}
            </datafields>
            {/if}
    {elseif $action == 'import_export_form'}
        {if isset($psversion15) && $psversion15 == '-1'}
        <script type="text/javascript">
        // <![CDATA[
            var copyToClipboard_success = '{l s='Copy to clipboard successfully' mod='gformbuilderpro'}';
        //]]>
        </script>
        {/if}
        <div class="gimport_export_form">

            <div class="col-lg-6">
                    <form action="" method="POST" id="gimport_form" name="gimport_form" enctype="multipart/form-data" >
                        <div  class="publish_box">
                            <div class="box-heading"><i class="icon-upload"></i> {l s='Import Forms' mod='gformbuilderpro'}</div>
                            <div class="gbox_content">
                                <input type="hidden" name="submitGimport_form" value="1"/>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='File' mod='gformbuilderpro'}</label>
                                    <div class="col-lg-9">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input id="file" type="file" name="zipfile" class="hide" />
                                                <div class="dummyfile input-group">
                                                    <span class="input-group-addon"><i class="icon-file"></i></span>
                                                    <input id="file-name" type="text" name="zipfile" readonly="" />
                                                    <span class="input-group-btn">
                                                        <button id="file-selectbutton" type="button" name="submitAddGForm" class="btn btn-default">
                                                            <i class="icon-folder-open"></i> {l s='Add file' mod='gformbuilderpro'}
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Delete old form' mod='gformbuilderpro'}</label>
                                    <div class="col-lg-9">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="delete_old_form" id="delete_old_form_on" value="1"  />
                                            <label for="delete_old_form_on">{l s='Yes' mod='gformbuilderpro'}</label>
                                            <input type="radio" name="delete_old_form" id="delete_old_form_off" value="0" checked="checked" />
                                            <label for="delete_old_form_off">{l s='No' mod='gformbuilderpro'}</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Override old form' mod='gformbuilderpro'}</label>
                                    <div class="col-lg-9">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="override_old_form" id="override_old_form_on" value="1"  />
                                            <label for="override_old_form_on">{l s='Yes' mod='gformbuilderpro'}</label>
                                            <input type="radio" name="override_old_form" id="override_old_form_off" value="0" checked="checked" />
                                            <label for="override_old_form_off">{l s='No' mod='gformbuilderpro'}</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                    </div>
                                </div>
                                {literal}
                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        $('#file-selectbutton').click(function(e) {
                                            $('#file').trigger('click');
                                        });
                                        $('#file-name').click(function(e) {
                                            $('#file').trigger('click');
                                        });
                                        $('#file-name').on('dragenter', function(e) {
                                            e.stopPropagation();
                                            e.preventDefault();
                                        });
                                        $('#file-name').on('dragover', function(e) {
                                            e.stopPropagation();
                                            e.preventDefault();
                                        });
                                        $('#file-name').on('drop', function(e) {
                                            e.preventDefault();
                                            var files = e.originalEvent.dataTransfer.files;
                                            $('#file')[0].files = files;
                                            $(this).val(files[0].name);
                                        });
                                        $('#file').change(function(e) {
                                            if ($(this)[0].files !== undefined)
                                            {
                                                var files = $(this)[0].files;
                                                var name  = '';

                                                $.each(files, function(index, value) {
                                                    name += value.name+', ';
                                                });

                                                $('#file-name').val(name.slice(0, -2));
                                            }
                                            else /* Internet Explorer 9 Compatibility */
                                            {
                                                var name = $(this).val().split(/[\\/]/);
                                                $('#file-name').val(name[name.length-1]);
                                            }
                                        });
                                    });
                                </script>
                                {/literal}
                                <div style="clear:both;"></div>
                                <div class="panel-footer">
                                    <button type="submit" value="1" id="gformbuilderpro_form_import" class="btn btn-default pull-right action_btn_hover_new_style">
                                        <i class="process-icon-upload"></i> {l s='Import' mod='gformbuilderpro'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

            </div>
            <div class="col-lg-6">
                <form action="" method="POST" name="gexport_form">
                    <div class="publish_box">
                        <div class="box-heading"><i class="icon icon-download"></i>{l s='Export Forms' mod='gformbuilderpro'}</div>
                        <div class="gbox_content">
                            <input type="hidden" name="submitGexport_form" value="1"/>
                            <input name="gid_forms" type="hidden" value="" id="gid_forms" autocomplete="off" />
                            <div class="export_warrning alert alert-info" role="alert">
                                <p class="alert-text">{l s='You must select at least one element to export.' mod='gformbuilderpro'}</p>
                            </div>
                            <div style="clear:both;"></div>
                            <div class="panel-footer">
                                <button type="button" value="1" id="gformbuilderpro_form_export" class="btn btn-default pull-right action_btn_hover_new_style">
                                    <i class="process-icon-download"></i> {l s='Export' mod='gformbuilderpro'}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div style="clear:both;"></div>
        </div>
        {* new version 2021 - Jun*}
        <div class="form-group hidden">
            <a href="#itemfieldparent_wp_addnew" class="add_element btn btn-default new_design_bt">
            </a>
            <div style="display:none;">
                <div  id="itemfieldparent_wp_addnew">
                    <div class="panel-heading">{l s='Explore pre-built forms'  mod='gformbuilderpro'}</div>
                    <div class="form-wrapper">
                        <div id="itemfieldparent_addnew">
                            <section>
                                <div class="template-list">
                                    <a class="item"  href="{$url_controller|escape:'html':'UTF-8'}&addgformbuilderpro&gformdefault=1">
                                        <img class="thumb" src="../modules/gformbuilderpro/views/img/Contact-formTemplate.png"/>
                                        <div class="title">{l s='Contact form'  mod='gformbuilderpro'}</div>
                                    </a>
                                    <a class="item"  href="{$url_controller|escape:'html':'UTF-8'}&addgformbuilderpro&gformdefault=2">
                                        <img class="thumb" src="../modules/gformbuilderpro/views/img/customerFormTemplate.png"/>
                                        <div class="title">{l s='Customer feedback form'  mod='gformbuilderpro'}</div>
                                    </a>
                                    <a class="item"  href="{$url_controller|escape:'html':'UTF-8'}&addgformbuilderpro&gformdefault=3">
                                        <img class="thumb" src="../modules/gformbuilderpro/views/img/discountCodeTemplate.png"/>
                                        <div class="title">{l s='Discount Code Form'  mod='gformbuilderpro'}</div>
                                    </a>
                                    <a class="item"  href="{$url_controller|escape:'html':'UTF-8'}&addgformbuilderpro&gformdefault=4">
                                        <img class="thumb" src="../modules/gformbuilderpro/views/img/orderFormTemplate.png"/>
                                        <div class="title">{l s='Order Form'  mod='gformbuilderpro'}</div>
                                    </a>
                                    <a class="item"  href="{$url_controller|escape:'html':'UTF-8'}&addgformbuilderpro&gformdefault=5">
                                        <img class="thumb" src="../modules/gformbuilderpro/views/img/wholesaleTemplate.png"/>
                                        <div class="title">{l s='Wholesale Order Form'  mod='gformbuilderpro'}</div>
                                    </a>
                                    <a class="item" href="{$url_controller|escape:'html':'UTF-8'}&addgformbuilderpro&gformdefault=6">
                                        <img class="thumb" src="../modules/gformbuilderpro/views/img/blankTemplate.jpg"/>
                                        <div class="title">{l s='Blank form'  mod='gformbuilderpro'}</div>
                                    </a>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" class="btn btn-default btn btn-default pull-left cancel_column_btn" >{l s='Cancel' mod='gformbuilderpro'}</button>
                    </div>
                </div>
            </div>
        </div>
        {literal}
        <script type="text/javascript">
            /*show template select */
            $('#desc-gformbuilderpro-new').click(function() {
                $('.add_element.new_design_bt').trigger('click');
                return false;
            });
        </script>
        {/literal}
    {elseif $action == 'printRequest'}
        {if isset($showrequest) && $showrequest}
            <a href="#gformrequest_quickview_{$id_gformrequest|intval}" rel="{$id_gformrequest|intval}" class="btn btn-default gformrequest_quickview "><i class="icon-eye"></i></a>
            <div style="display:none;">
                <div id="gformrequest_quickview_{$id_gformrequest|intval}" class="gformrequest_quickview_box bootstrap">
                    <div class="form-group">
                		<label class="control-label col-lg-12">#{$id_gformrequest|intval}: {$subject|escape:'html':'UTF-8'}</label>
                	</div><div style="clear:both;"></div><hr />
                    {if isset($isps17) && $isps17}
                        {$request nofilter}{* $request is html content, no need to escape*}
                    {else}
                        {$request}{* $request is html content, no need to escape*}
                    {/if}
                    <div class="panel-footer">
            			<a href="{$link_request|escape:'html':'UTF-8'}#replyform" class="btn btn-primary pull-right" target="_blank"><i class="icon-reply"></i>{l s='Reply' mod='gformbuilderpro'}</a>
                    </div>
                </div>
            </div>
        {/if}
    {/if}
{/if}