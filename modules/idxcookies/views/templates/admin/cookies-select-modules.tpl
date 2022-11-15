{**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2018 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<select name="cookieModule_selector" class='js_cookieModule_selector' data-idcookie="{$cookie.id_cookie|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}">
    {foreach from=$optionModuleList item=module}
    <option value="{$module.id|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}" {if $module.id == $cookie.module}selected="selected"{/if}>{$module.name|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}</option>
    {/foreach}
</select>
