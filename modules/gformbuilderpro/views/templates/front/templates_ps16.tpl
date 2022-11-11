{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{literal}
{if isset($ispopup) && $ispopup}
    <a href="#gformbuilderpro_form_{/literal}{$idform|intval}{literal}" rel="{/literal}{$idform|intval}{literal}" class="btn btn-primary gformbuilderpro_openform">{$popup_label}</a>
{/if}
{/literal}
<input type="hidden" id="gformbuilderpro_formValidity" value="{literal}{$required_warrning|escape:'html':'UTF-8'}{/literal}" />
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
    <form action="{literal}{$actionUrl|escape:'html':'UTF-8'}{/literal}" method="POST" class="{if $ajax}form_using_ajax{/if}" {if $hasupload} enctype="multipart/form-data" {/if}>
        {if $ajax}
            <input type="hidden" name="usingajax" value="1" />
        {else}
            <input type="hidden" name="usingajax" value="0" />
        {/if}
        <input type="hidden" name="idform" value="{$idform|escape:'html':'UTF-8'}" />
        <input type="hidden" name="id_lang" value="{$id_lang|escape:'html':'UTF-8'}" />
        <input type="hidden" name="id_shop" value="{$id_shop|escape:'html':'UTF-8'}" />
        <input type="hidden" name="gSubmitForm" value="1" />
        <input type="hidden" name="Conditions" value="{literal}{$Conditions|escape:'html':'UTF-8'}{/literal}" />
        <div class="gformbuilderpro_content row">
            {$htmlcontent}{* $htmlcontent is html content, no need to escape*}
            {literal}
            {if isset($id_module_gformbuilderpro) && $id_module_gformbuilderpro > 0}
                {hook h='displayGDPRConsent' id_module=$id_module_gformbuilderpro}
            {/if}
            {/literal}
        </div>
    </form>
</div>
{if !isset($old_psversion_15) || !$old_psversion_15}
{literal}
{addJsDefL name='contact_fileDefaultHtml'}{l s='No file selected' js=1 mod='gformbuilderpro'}{/addJsDefL}
{addJsDefL name='contact_fileButtonHtml'}{l s='Choose File' js=1  mod='gformbuilderpro'}{/addJsDefL}
{/literal}
{/if}