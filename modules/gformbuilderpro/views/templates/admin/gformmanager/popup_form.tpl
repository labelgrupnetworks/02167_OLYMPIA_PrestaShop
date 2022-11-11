{*
* Do not edit the file if you want to upgrade the module in future.
*
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2019 Globo JSC
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/
*}

<div style="display:none;">
    <div id="popup_field_config">
        <div id="content"  class="bootstrap"></div>
    </div>
    <div id="popup_field_wp"></div>
    <div id="popup_field_config_hidden" class="bootstrap">
        <div id="popup_field_config_wp">
            <form id="module_form" action="{$gformbuilderpro_submit_link|escape:'html':'UTF-8'}" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="addShortcode" value="1"/>
                <div class="">
                    <div class="panel-heading"><i class="icon-cogs"></i><span class="popup_field_type"></span>{l s='Settings' mod='gformbuilderpro'}</div>
                    <div class="form-wrapper popup_field_content">
                        <div class="form-group hide">
    						<input type="hidden" name="type" id="type" value="" />
                            <input type="hidden" name="id_gformbuilderprofields" id="id_gformbuilderprofields" value="" />
    					</div>
                        <div class="form-group">
                            <label class="control-label col-lg-3 required">{l s='Label' mod='gformbuilderpro'}</label>
                            <div class="col-lg-9">
                                <div class="form-group">
                                    {foreach $languages as $language}
                                        <div class="translatable-field  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if};">
                                            <div class="col-lg-9">
                                                <input type="text" name="label_{$language.id_lang|escape:'html':'UTF-8'}" value="" class="label_{$language.id_lang|escape:'html':'UTF-8'} gvalidate_isRequired" />
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
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                        <div class="form-group gfield_name">
                            <label class="control-label col-lg-3 required">{l s='Field Name' mod='gformbuilderpro'}</label>
                            <div class="col-lg-9">
                                <input type="text" name="name" id="name" value="" class="gvalidate gvalidate_isName gvalidate_isRequired" />
                             </div>
                        </div>

                        <div class="popup_field_content_box"></div>
                        <div class="form-group gfield_idatt">
                            <label class="control-label col-lg-3">{l s='Html id' mod='gformbuilderpro'}</label>
                            <div class="col-lg-9">
                                <input type="text" name="idatt" id="idatt" value="" class="gvalidate gvalidate_isId" />
                                <p class="help-block">{l s='Add your custom id so you can custom css for this field' mod='gformbuilderpro'}</p>
                            </div>
                        </div>
                        <div class="form-group gfield_classatt">
                            <label class="control-label col-lg-3">{l s='Html class' mod='gformbuilderpro'}</label>
                            <div class="col-lg-9">
                                <input type="text" name="classatt" id="classatt" value="" class="gvalidate gvalidate_isClass" />
                                <p class="help-block">{l s='Add your custom class so you can custom css for this field' mod='gformbuilderpro'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="submit" value="1" id="module_form_submit_btn" name="addShortcode" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Save change' mod='gformbuilderpro'}
                        </button>
                        <button type="submit" class="btn btn-default btn btn-default pull-left" name="cancelShortcode"><i class="process-icon-cancel"></i> {l s='Cancel' mod='gformbuilderpro'}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="popup_field_config_item">
        <div class="form-group gfield_labelpos">
            <label class="control-label col-lg-3">
            {l s='Label position' mod='gformbuilderpro'}
            </label>
            <div class="col-lg-9">
                <select name="labelpos" class=" fixed-width-xl" id="labelpos">
                </select>
            </div>
        </div>

        <div class="form-group gfield_dynamicval">
            <label class="control-label col-lg-3">{l s='Dynamic Value' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <div class="form-group">
                    <select name="dynamicval" class=" fixed-width-xl" id="dynamicval">
                    {*
                        <option value=""></option>
                        <option value="customerid">{l s='Customer Id' mod='gformbuilderpro'}</option>
                        <option value="customername">{l s='Custommer Name' mod='gformbuilderpro'}</option>
                        <option value="customeremail">{l s='Custommer Email' mod='gformbuilderpro'}</option>
                        <option value="customerphone">{l s='Custommer Phone Number' mod='gformbuilderpro'}</option>
                        <option value="customercompany">{l s='Custommer Company' mod='gformbuilderpro'}</option>
                        *}
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group gfield_placeholder">
            <label class="control-label col-lg-3">{l s='Placeholder' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <div class="form-group">
                    {foreach $languages as $language}
                        <div class="translatable-field  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if};">
                            <div class="col-lg-9">
                                <input type="text" value="" name="placeholder_{$language.id_lang|escape:'html':'UTF-8'}" class="placeholder_{$language.id_lang|escape:'html':'UTF-8'}" />
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
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="form-group gfield_required">
            <label class="control-label col-lg-3">{l s='Required field' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="required" class="need_change_id" data-id="required_on" value="1" />
                <label for="required_on">{l s='Yes' mod='gformbuilderpro'}</label>
                <input type="radio" name="required" class="need_change_id" data-id="required_off" value="0" checked="checked" />
                <label for="required_off">{l s='No' mod='gformbuilderpro'}</label>
                <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        <div class="form-group gfield_extra_switch">
            <label class="control-label col-lg-3">{l s='Register newsletter if input value is email' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="extra" class="need_change_id" data-id="extra_on" value="1" />
                <label for="extra_on">{l s='Yes' mod='gformbuilderpro'}</label>
                <input type="radio" name="extra" class="need_change_id" data-id="extra_off" value="0" checked="checked" />
                <label for="extra_off">{l s='No' mod='gformbuilderpro'}</label>
                <a class="slide-button btn"></a>
            </span>
            </div>
        </div>
        <div class="form-group gfield_extra_select">
            <label class="control-label col-lg-3">{l s='Country Option' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <select name="extra" class=" fixed-width-xl" id="extra"></select>
            </span>
            </div>
        </div>
        <div class="form-group gfield_extra_colorchoose">
            <label class="control-label col-lg-3">{l s='Colors' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <div class="input-group">
                    <input type="text" name="extra" class="tagify" data-addTagPrompt="{l s='Add Color' mod='gformbuilderpro'}" />
                </div>
                <p class="help-block">{l s='To add "Colors" click in the field, write color(ex: #ABCDEF), and then press "Enter".' mod='gformbuilderpro'}</p>
            </div>
        </div>
        <div class="form-group gfield_extra_tags">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <input type="text" name="extra" class="tagify" data-addTagPrompt="{l s='Add Tag' mod='gformbuilderpro'}" />
                <p class="help-block">{l s='Click in the field, write and then press "Enter".' mod='gformbuilderpro'}</p>
            </div>
        </div>
        <div class="form-group gfield_extra_color">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <div class="input-group">
                    <input data-hex="true" type="text" name="extra" class="gcolor" />
                </div>
            </div>
        </div>
        <div class="form-group gfield_extra_slidervalue">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
        		<div class="row">
                    <input type="hidden" name="extra" class="gvalidate  gvalidate_isRequired3" id="slidervalue" value="" />
                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12" for="minval">{l s='Min' mod='gformbuilderpro'}</label>
                            <div class="col-lg-12">
                                <input type="text" name="minval" class="slidervalue" id="minval" placeholder="{l s='Min value' mod='gformbuilderpro'}" value="0" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12" for="maxval">{l s='Max' mod='gformbuilderpro'}</label>
                            <div class="col-lg-12">
                                <input type="text" name="maxval" class="slidervalue" id="maxval" placeholder="{l s='Max value' mod='gformbuilderpro'}" value="100" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12" for="rangeval">{l s='Range' mod='gformbuilderpro'}</label>
                            <div class="col-lg-12">
                                <input type="text" name="rangeval" class="slidervalue" id="rangeval" placeholder="{l s='Range value' mod='gformbuilderpro'}"  value="1"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            <label class="col-lg-12" for="defaultval">{l s='Default' mod='gformbuilderpro'}</label>
                            <div class="col-lg-12">
                                <input type="text" name="defaultval" class="slidervalue" id="defaultval" placeholder="{l s='Default value' mod='gformbuilderpro'}"  value="100" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group gfield_extra_imagethumb">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
        		<div class="row">
                    <div id="divThumbItems" class="col-lg-12"></div>
                    <br />
                    <input type="hidden" id="thumb_url" value="{$base_uri|escape:'html':'UTF-8'}modules/gformbuilderpro/views/img/thumbs/" />
                    <textarea name="extra" id="thumbchoose" class="hidden gvalidate  gvalidate_isRequired"></textarea>
                    <div class="col-lg-4">
                        <input type="file" name="thumb[]" id="imagethumbupload" class="imagethumbupload" multiple />
                    </div>
                    <div class="col-lg-6">
                        <button type="button" id="add_thumb_item_fromlist" class="btn btn-default">
        					<i class="icon-list"></i> {l s='Or choose from exist image' mod='gformbuilderpro'}
        				</button>

        			</div>
                    <div class="col-lg-12">
                        <div id="thumbs_fromlist"></div>
                        <button type="button" id="add_thumb_item" class="btn btn-default">
        					<i class="icon-plus-sign-alt"></i> {l s='Add thumbs' mod='gformbuilderpro'}
        				</button>
                        <p class="help-block">{l s='Click to button "Add thumbs" after select thumb' mod='gformbuilderpro'}</p>
                    </div>

                </div>
            </div>
        </div>
        <div class="form-group gfield_extra_extraproducts">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
        		<div class="row">
                    <div class="col-lg-12">
                        <ul class="wholesale-product">

                        </ul>
                    </div>
                    <div class="col-lg-12">
                        <button type="button" id="add_products_item_fromlist" class="btn btn-default col-lg-12" disabled="disabled">
                            <span class="gformload_oldpro" style="display: none;"><i class="icon-plus-sign-alt"></i> {l s=' Add Products' mod='gformbuilderpro'}</span>
                            <span class="gformonload_pro"><i class="icon-refresh icon-spin icon-fw"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group gfield_extra_gformproduct">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
        		<div class="row">
        			<div class="col-lg-6">
                        <input type="hidden" class="gvalidate gvalidate_isRequired need_change_id" name="extra" data-id="inputPackItems" value=""/>
                        <input type="hidden" data-id="ajaxaction" class="need_change_id" value="{$ajaxaction|escape:'html':'UTF-8'}" />
        				<input type="text" data-id="curPackItemName" name="curPackItemName" class="need_change_id form-control" />
                        <input type="hidden" data-id="curPackItemId" name="curPackItemId" class="need_change_id form-control" />
        			</div>
        			<div class="col-lg-2">
        				<button type="button" id="add_pack_item" class="btn btn-default">
        					<i class="icon-plus-sign-alt"></i> {l s='Add' mod='gformbuilderpro'}
        				</button>
        			</div>
                    <br />
                    <div  data-id="divPackItems" class="need_change_id col-lg-12 {if isset($loadjqueryselect2) && $loadjqueryselect2 !='1'} get_product_version_old {/if}">
                    </div>
        		</div>
        	</div>
        </div>
        <div class="form-group gfield_description_textarea">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <div class="form-group">
                    {foreach $languages as $language}
                        <div class="translatable-field  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if};">
                            <div class="col-lg-9">
                                <textarea name="description_{$language.id_lang|escape:'html':'UTF-8'}" class="description_{$language.id_lang|escape:'html':'UTF-8'}"></textarea>
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
                        </div>
                    {/foreach}
                </div>
                <div class="shortcode_wp">
                    <div class="help-block">
                        <label>{l s='Shortcode: ' mod='gformbuilderpro'}</label>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customerid}{/literal}">{literal}{$customerid}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customername}{/literal}">{literal}{$customername}{/literal}</span></span>

                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customerfirstname}{/literal}">{literal}{$customerfirstname}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customerlastname}{/literal}">{literal}{$customerlastname}{/literal}</span></span>

                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customeremail}{/literal}">{literal}{$customeremail}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customercompany}{/literal}">{literal}{$customercompany}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customer_address}{/literal}">{literal}{$customer_address}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customer_postcode}{/literal}">{literal}{$customer_postcode}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customer_city}{/literal}">{literal}{$customer_city}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$customer_phone}{/literal}">{literal}{$customer_phone}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$productid}{/literal}">{literal}{$productid}{/literal}</span></span>
                        <span class="copy_group"><span class="shortcode copy_data copy_link" rel="{literal}{$productname}{/literal}">{literal}{$productname}{/literal}</span></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group gfield_description_multival">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <div class="form-group">
                    {foreach $languages as $language}
                        <div class="translatable-field  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if};">
                            <div class="col-lg-9">
                                <textarea name="description_{$language.id_lang|escape:'html':'UTF-8'}" class="description_{$language.id_lang|escape:'html':'UTF-8'}"></textarea>
                                <p class="help-block">{l s='Each option per line' mod='gformbuilderpro'}</p>
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
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="form-group gfield_description_multival2">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <div class="">
                    <div class="multival_box" id="multival_description" rel="description">
                        <div id="description_multival_newval" class="multival_newval">
                            <div style="display:none;" id="value_invalid" class="alert alert-danger" role="alert"><p>{l s='Value invalid' mod='gformbuilderpro'}</p></div>
                            <div class="form-group">
                            {foreach $languages as $language}
                                    <div class="translatable-field col-lg-12  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                                        <div class="col-lg-6">
                                            <input type="text" value="" class="multival_newval_{$language.id_lang|escape:'html':'UTF-8'}" />
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
                                        <div class="col-lg-4">
                                            <a  class="add_multival_newval btn btn-default"><i class="icon-save"></i><span class="addlabel">{l s='Add' mod='gformbuilderpro'}</span><span class="updatelabel">{l s='Update' mod='gformbuilderpro'}</span></a>
                                            <a  class="cancel_multival_newval btn btn-danger" style="display:none;"><i class="icon-remove"></i></a>
                                        </div>
                                    </div>
                            {/foreach}
                            </div>
                        </div>
                        <hr />
                        <div id="description_multival_wp" rel="description"  class="multival_wp">
                        </div>
                        <div style="display:none;" class="multival_action_wp">
                            <div class="multival_action">
                                <a class="multival_move btn btn-default"><i class="icon-move"></i></a>
                                <a class="multival_edit btn btn-default"><i class="icon-edit"></i></a>
                                <a class="multival_delete  btn btn-danger"><i class="icon-trash"></i></a>
                            </div>
                        </div>
                        {foreach $languages as $language}
                            <input type="hidden" id="description_{$language.id_lang|escape:'html':'UTF-8'}" name="description_{$language.id_lang|escape:'html':'UTF-8'}" value="" />
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group gfield_value_text">
            <label class="control-label col-lg-3">{l s='Value' mod='gformbuilderpro'}</label>
            <div class="col-lg-9">
                <div class="form-group">
                    {foreach $languages as $language}
                        <div class="translatable-field  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if};">
                            <div class="col-lg-9">
                                <input type="text" value="" name="value_{$language.id_lang|escape:'html':'UTF-8'}" class="value_{$language.id_lang|escape:'html':'UTF-8'}" />
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
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="form-group gfield_value_multival">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <div class="form-group">
                    {foreach $languages as $language}
                        <div class="translatable-field  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if};">
                            <div class="col-lg-9">
                                <textarea name="value_{$language.id_lang|escape:'html':'UTF-8'}" class="value_{$language.id_lang|escape:'html':'UTF-8'}"></textarea>
                                <p class="help-block">{l s='Each option per line' mod='gformbuilderpro'}</p>
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
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>

        <div class="form-group gfield_value_multival2">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-9">
                <div class="">
                    <div class="multival_box" id="multival_value" rel="value">
                        <div id="value_multival_newval" class="multival_newval">
                            <div style="display:none;" id="value_invalid" class="alert alert-danger" role="alert"><p>{l s='Value invalid' mod='gformbuilderpro'}</p></div>
                            <div class="form-group">
                            {foreach $languages as $language}
                                    <div class="translatable-field col-lg-12  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                                        <div class="col-lg-6">
                                            <input type="text" value="" class="multival_newval_{$language.id_lang|escape:'html':'UTF-8'}" />
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
                                        <div class="col-lg-4">
                                            <a  class="add_multival_newval btn btn-default"><i class="icon-save"></i><span class="addlabel">{l s='Add' mod='gformbuilderpro'}</span><span class="updatelabel">{l s='Update' mod='gformbuilderpro'}</span></a>
                                            <a  class="cancel_multival_newval btn btn-danger" style="display:none;"><i class="icon-remove"></i></a>
                                        </div>
                                    </div>
                            {/foreach}
                            </div>
                        </div>
                        <hr />
                        <div id="value_multival_wp" rel="value"  class="multival_wp">
                        </div>
                        <div style="display:none;" class="multival_action_wp">
                            <div class="multival_action">
                                <a class="multival_move btn btn-default"><i class="icon-move"></i></a>
                                <a class="multival_edit btn btn-default"><i class="icon-edit"></i></a>
                                <a class="multival_delete  btn btn-danger"><i class="icon-trash"></i></a>
                            </div>
                        </div>
                        {foreach $languages as $language}
                            <input type="hidden" id="value_{$language.id_lang|escape:'html':'UTF-8'}" name="value_{$language.id_lang|escape:'html':'UTF-8'}" value="" />
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group gfield_free"></div>
        <div class="form-group gfield_extra_option">
            <label class="control-label col-lg-3">
                {l s='Automatically redirect' mod='gformbuilderpro'}
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="extra_option" class="need_change_id" data-id="extra_option_on" value="1" />
                <label for="extra_option_on">{l s='Yes' mod='gformbuilderpro'}</label>
                <input type="radio" name="extra_option" class="need_change_id" data-id="extra_option_off" value="0" checked="checked" />
                <label for="extra_option_off">{l s='No' mod='gformbuilderpro'}</label>
                <a class="slide-button btn"></a>
                </span>
                <p class="help-block">
                    {l s='Automatically redirect to cart after submitted.' mod='gformbuilderpro'}
                </p>
            </div>
        </div>
        <div class="form-group gfield_multi">
            <label class="control-label col-lg-3">
                {l s='Multi choose' mod='gformbuilderpro'}
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="multi" class="need_change_id" data-id="multi_on" value="1" />
                <label for="multi_on">{l s='Yes' mod='gformbuilderpro'}</label>
                <input type="radio" name="multi" class="need_change_id" data-id="multi_off" value="0" checked="checked" />
                <label for="multi_off">{l s='No' mod='gformbuilderpro'}</label>
                <a class="slide-button btn"></a>
                </span>
                <p class="help-block">
                    {l s='If on, then front-end user can select multi item.' mod='gformbuilderpro'}
                </p>
            </div>
        </div>
        <div class="form-group gfield_validate">
            <label class="control-label col-lg-3">
            {l s='Validation type' mod='gformbuilderpro'}
            </label>
            <div class="col-lg-9">
                <select name="validate" class=" fixed-width-xl" id="validate">
                </select>
            </div>
        </div>
        {* new version *}
        <div class="form-group gfield_condition">
            <label class="control-label col-lg-3">
                {l s='Conditional Logic Options' mod='gformbuilderpro'}
            </label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="condition" class="need_change_id" data-id="condition_on" value="1" />
                <label for="condition_on">{l s='Yes' mod='gformbuilderpro'}</label>
                <input type="radio" name="condition" class="need_change_id" data-id="condition_off" value="0" checked="checked" />
                <label for="condition_off">{l s='No' mod='gformbuilderpro'}</label>
                <a class="slide-button btn"></a>
                </span>
                <p class="help-block">
                    {l s='If on, Creating conditional logic options.' mod='gformbuilderpro'}
                </p>
            </div>
        </div>
        <div class="gfield_condition_display panel col-lg-12" style="display:none;">
            <div class="form-group">
                <div class="gformcondition_display col-lg-3">
                    <select name="condition_display" class="gformcondition_display_extra">
                        <option value="0" selected="selected">{l s='Show' mod='gformbuilderpro'}</option>
                        <option value="1">{l s='Hide' mod='gformbuilderpro'}</option>
                    </select>
                </div>
                <div class="gformcondition_display_text col-lg-2">
                    <label class="control-label">{l s='this field if' mod='gformbuilderpro'}</label>
                </div>
                <div class="gformcondition_display col-lg-3">
                    <select name="condition_must_match" class="gformcondition_value_extra">
                        <option value="0" selected="selected">{l s='All' mod='gformbuilderpro'}</option>
                        <option value="1">{l s='Any' mod='gformbuilderpro'}</option>
                    </select>
                </div>
                <div class="gformcondition_display_text col-lg-4">
                    <label class="control-label">{l s='of the following match:' mod='gformbuilderpro'}</label>
                </div>
            </div>
            <div class="gformcondition_listoption">
            </div>
            <div class="form-group col-lg-12">
                <button type="button" class="btn btn-default gfield_addlistoption" data-number='1'>
                    <i class="icon icon-plus"></i> {l s='Add another condition' mod='gformbuilderpro'}
                </button>
            </div>
        </div>
    </div>
    <div id="listoption_datas">
        <div class="form-group option_condition" id="option_condition_0">
            <div class="col-lg-4">
                <select name="listoptions[0][id_field]" class="gformcondition_listoption_idfield">
                    <option value="0">{l s='Please Select' mod='gformbuilderpro'}</option>
                    {if $condition_fielddatas}
                        {foreach $condition_fielddatas as $fielddata}
                            <option value="{$fielddata['id_gformbuilderprofields']|escape:'html':'UTF-8'}">{$fielddata['name']|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
            <div class="col-lg-4">
                <select name="listoptions[0][condition]" class="gformcondition_listoption_condition">
                    <option value="IS_EQUAL">{l s='is equal to' mod='gformbuilderpro'}</option>
                    <option value="ISNOT_EQUAL" >{l s='is not equal to' mod='gformbuilderpro'}</option>
                    <option value="IS_GREATER" >{l s='is greater than' mod='gformbuilderpro'}</option>
                    <option value="IS_LESS" >{l s='is less than' mod='gformbuilderpro'}</option>
                    <option value="STARTS" >{l s='starts with' mod='gformbuilderpro'}</option>
                    <option value="ENDS" >{l s='ends with' mod='gformbuilderpro'}</option>
                    <option value="IS_CONTAINS" >{l s='contains' mod='gformbuilderpro'}</option>
                    <option value="ISNOT_CONTAINS" >{l s='does not contain' mod='gformbuilderpro'}</option>
                </select>
            </div>
            <div class="col-lg-3">
                <input type="text" class="gformcondition_listoption_conditionvalue" name="listoptions[0][conditionvalue]" value=""/>
            </div>
            <div class="col-lg-1">
                <button type="button" class="btn btn-default gfield_deletelistoption" data-idoption="0">
                    <i class="icon-trash"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="gtext_cutom" class="hidden">
        <input type="hidden" class="gformtext_noneoption" value="{l s='Please Select' mod='gformbuilderpro'}"/>
        <input type="hidden" class="gformtext_nonewholesale" value="{l s='Only one Wholesale widget can be added per form.' mod='gformbuilderpro'}"/>
        <input type="hidden" class="gformtext_check_qtydiscount" value="{l s='Please check the quantity, the lower levels quantity should be smaller than the higher levels quantity.' mod='gformbuilderpro'}"/>
        <input type="hidden" class="text_discount_Percentage" value="{l s='Percentage (%)' mod='gformbuilderpro'}"/>
        <input type="hidden" class="text_discount_Amount" value="{l s='Amount' mod='gformbuilderpro'}"/>
        <input type="hidden" class="text_discount_exctax" value="{l s='Tax excluded' mod='gformbuilderpro'}"/>
        <input type="hidden" class="text_discount_inctax" value="{l s='Tax included' mod='gformbuilderpro'}"/>
        <input type="hidden" class="gfomCurrencies" value="{$Currencies|escape:'html':'UTF-8'}"/>
        <input type="hidden" class="gfomid_currency_default" value="{$id_currency_default|escape:'html':'UTF-8'}"/>
    </div>
    <div id="file_condition_option" class="hidden">
        <select name="" class="gformcondition_file_condition_option">
            <option value="0">{l s='has file' mod='gformbuilderpro'}</option>
            <option value="1" >{l s='no file' mod='gformbuilderpro'}</option>
        </select>
    </div>
    <div id="gfield_datas">
        {if $fielddatas}
            {foreach $fielddatas as $fielddata}
                <div id="gfield_data_{$fielddata.id_gformbuilderprofields|intval}" class="gfield_data">
                    {foreach $fielddata as $field_name=> $field_val}
                        {if $field_name !='id_lang' && $field_name !='id_shop'}
                            {if $field_name !='description' && $field_name !='placeholder' && $field_name !='value' && $field_name !='label'}
                                {if $field_name =='condition_listoptions'}
                                    {if $field_val}
                                        {foreach $field_val as $id_key=> $val}
                                            <input type="text" class="{$field_name|escape:'html':'UTF-8'}_id_field{$id_key|intval}" value="{if $isps17}{$val['id_field']}{else}{$val['id_field']}{/if}">{* $val is html content. no need escape *}
                                            <input type="text" class="{$field_name|escape:'html':'UTF-8'}_conditionvalue{$id_key|intval}" value="{if $isps17}{$val['conditionvalue'] nofilter}{else}{$val['conditionvalue']}{/if}">{* $val is html content. no need escape *}
                                            <input type="text" class="{$field_name|escape:'html':'UTF-8'}_condition{$id_key|intval}" value="{if $isps17}{$val['condition'] nofilter}{else}{$val['condition']}{/if}">{* $val is html content. no need escape *}
                                        {/foreach}
                                    {/if}
                                {else}
                                    <input type="text" class="{$field_name|escape:'html':'UTF-8'}" value="{$field_val|escape:'html':'UTF-8'}" />
                                {/if}
                            {else}
                                {if $field_val}
                                    {foreach $field_val as $id_lang=> $val}
                                        <textarea class="{$field_name|escape:'html':'UTF-8'}_{$id_lang|intval}">{if $isps17}{$val nofilter}{else}{$val}{/if}</textarea>{* $val is html content. no need escape *}
                                    {/foreach}
                                {/if}
                            {/if}
                        {/if}
                    {/foreach}
                </div>
            {/foreach}
        {/if}
    </div>
</div>

<div>
    <div class="box_setting_products">
        <div class="box_setting_products_top box_setting_displayclose"></div>
        <div class="" id="box_setting_display">
            <div class="showin-heading-box">{l s='Product Selector' mod='gformbuilderpro'}</div>
            <div id="drop_conten_display">
                <div class="form-group">
                    <div class="input-group">
                        <input placeholder="{l s='Searchable by product  name, id, Reference code.' mod='gformbuilderpro'}" class="ac_input url_product" type="text" id="gform_search_product" autocomplete="off" name="">
                        <span class="input-group-addon"><i class="icon-search"></i></span>
                    </div>
                    <div clas="gnone">
                        <input type="hidden" class="itempage_product_search" value="0"/>
                        <input type="hidden" class="itempage_text_variants" value="{l s='variants selected' mod='gformbuilderpro'}"/>
                        <textarea style="display:none;" type="hidden" id="gform-product-ids-new" data-type="text" class="form-control"></textarea>
                        <textarea style="display:none;" type="hidden" id="gform-combin-ids-new" data-type="text" class="form-control"></textarea>
                    </div>
                </div>
                <div class="form-group show_selectproduct">
                </div>
                <div class="form-group show_htmlsearch">
                </div>
            </div>
            <div class="drop_footer">
                <div class="drop_footer_row">
                    <button type="button" class="btn btn-default gbtn-default" id="box_setting_showinsavedisplay">{l s='Save' mod='gformbuilderpro'}</button>
                    <button type="button" class="btn btn-default box_setting_displayclose">{l s='Cancel' mod='gformbuilderpro'}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <div class="box_setting_products_discount">
        <div class="box_setting_products_top box_setting_displayclose_discount"></div>
        <div class="" id="box_setting_display_discount">
            <div class="showin-heading-box">{l s='Edit wholesale tiered discount' mod='gformbuilderpro'}</div>
            <div id="drop_conten_display_discount">
                <div class="form-group">
                    <div class="input-group">
                        <input type="hidden" class="itempage_discount_edit" value="0"/>
                    </div>
                </div>
                <div class="form-group show_htmlsearch_discount">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{l s='Qty' mod='gformbuilderpro'}</th>
                                <th>{l s='Discount Type' mod='gformbuilderpro'}</th>
                                <th>{l s='Discount Value' mod='gformbuilderpro'}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="col-lg-12">
                        <a class="btn btn-default plus_newrowdiscount" type="button" data-number="1"><i class="icon-plus-sign"></i>{l s='Add' mod='gformbuilderpro'}</a>
                    </div>
                </div>
            </div>
            <div class="drop_footer">
                <div class="drop_footer_row">
                    <button type="button" class="btn btn-default gbtn-default" id="box_setting_showinsavedisplay_discount">{l s='Save' mod='gformbuilderpro'}</button>
                    <button type="button" class="btn btn-default box_setting_displayclose_discount">{l s='Cancel' mod='gformbuilderpro'}</button>
                </div>
            </div>
        </div>
    </div>
</div>