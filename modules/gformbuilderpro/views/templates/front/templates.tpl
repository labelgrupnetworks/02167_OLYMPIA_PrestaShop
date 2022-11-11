{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if !isset($codehook) || !$codehook}
{literal}
{extends file='page.tpl'}
{block name='page_content'}
{/literal}
{literal}
<script type="text/javascript">
var baseUri = '{$baseUri|escape:'html':'UTF-8'}';
</script>
{/literal}

{literal}
{if isset($ispopup) && $ispopup}
    <a href="#gformbuilderpro_form_{/literal}{$idform|intval}{literal}" rel="{/literal}{$idform|intval}{literal}" class="btn btn-primary gformbuilderpro_openform">{$popup_label}</a>
{/if}
{/literal}
<input type="hidden" id="gformbuilderpro_formValidity" value="{literal}{$required_warrning}{/literal}" />
<div id="gformbuilderpro_form_{$idform|intval}" class="gformbuilderpro_form gformbuilderpro_form_{$idform|intval} {literal}{if isset($ispopup) && $ispopup} ispopup_form ghidden_form {/if}{/literal}" >
    {literal}
    {if isset($_errors)}
    <div class="alert alert-danger" id="create_account_error">
        <ol>
        {foreach $_errors as $_error}
            <li>{$_error|escape:'html':'UTF-8'}</li>
        {/foreach}
        </ol>
    </div>
    {/if}
    {/literal}
    <form action="{literal}{$actionUrl|escape:'html':'UTF-8'}{/literal}" method="POST" class="{if $ajax}form_using_ajax{/if} row" {if $hasupload} enctype="multipart/form-data" {/if}>
        {if $ajax}
            <input type="hidden" name="usingajax" value="1" />
        {else}
            <input type="hidden" name="usingajax" value="0" />
        {/if}
        <input type="hidden" name="idform" value="{$idform|intval}" />
        <input type="hidden" name="id_lang" value="{$id_lang|intval}" />
        <input type="hidden" name="id_shop" value="{$id_shop|intval}" />
        <input type="hidden" name="Conditions" value="{literal}{$Conditions|escape:'html':'UTF-8'}{/literal}" />
        <input type="hidden" name="ConditionsHide" value="" />
        <input type="hidden" name="gSubmitForm" value="1" />
        <div class="gformbuilderpro_content">
            {$htmlcontent nofilter}{* $htmlcontent is html content, no need to escape*}
            {literal}
            {if isset($id_module_gformbuilderpro) && $id_module_gformbuilderpro > 0}
                {hook h='displayGDPRConsent' id_module=$id_module_gformbuilderpro}
            {/if}
            {/literal}
        </div>
        <div style="clear:both;"></div>
    </form>
    <div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
{literal}
{/block}
{/literal}
{else}
{literal}
{if isset($ispopup) && $ispopup}
    <a href="#gformbuilderpro_form_{/literal}{$idform|intval}{literal}" rel="{/literal}{$idform|intval}{literal}" class="btn btn-primary gformbuilderpro_openform">{$popup_label|escape:'html':'UTF-8'}</a>
{/if}
{/literal}
<input type="hidden" id="gformbuilderpro_formValidity" value="{literal}{$required_warrning}{/literal}" />
<div id="gformbuilderpro_form_{$idform|intval}" class="gformbuilderpro_form gformbuilderpro_form_{$idform|intval} {literal}{if isset($ispopup) && $ispopup} ispopup_form ghidden_form {/if}{/literal}" >
    {literal}
    {if isset($_errors)}
    <div class="alert alert-danger" id="create_account_error">
        <ol>
        {foreach $_errors as $_error}
            <li>{$_error|escape:'html':'UTF-8'}</li>
        {/foreach}
        </ol>
    </div>
    {/if}
    {/literal}
    <form action="{literal}{$actionUrl|escape:'html':'UTF-8'}{/literal}" method="POST" class="{if $ajax}form_using_ajax{/if} row" {if $hasupload} enctype="multipart/form-data" {/if}>
        {if $ajax}
            <input type="hidden" name="usingajax" value="1" />
        {else}
            <input type="hidden" name="usingajax" value="0" />
        {/if}
        <input type="hidden" name="idform" value="{$idform|intval}" />
        <input type="hidden" name="id_lang" value="{$id_lang|intval}" />
        <input type="hidden" name="id_shop" value="{$id_shop|intval}" />
        <input type="hidden" name="Conditions" value="{literal}{$Conditions|escape:'html':'UTF-8'}{/literal}" />
        <input type="hidden" name="gSubmitForm" value="1" />
        <div class="gformbuilderpro_content">
            {$htmlcontent nofilter}{* $htmlcontent is html content, no need to escape*}
            {literal}
            {if isset($id_module_gformbuilderpro) && $id_module_gformbuilderpro > 0}
                {hook h='displayGDPRConsent' id_module=$id_module_gformbuilderpro}
            {/if}
            {/literal}
        </div>
        <div style="clear:both;"></div>
    </form>
    <div style="clear:both;"></div>
</div>
<div style="clear:both;"></div>
{/if}