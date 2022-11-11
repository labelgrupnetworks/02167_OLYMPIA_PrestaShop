{**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2018 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<select name="cookieType_selector" class='js_cookieType_selector' data-idcookie="{$cookie.id_cookie|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}">
    <option value="-1">{l s='Not selected' mod='idxcookies'}</option>
    {foreach from=$cookie_types item=type}
    <option value="{$type.id_cookie_type|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}" {if $type.id_cookie_type == $cookie.id_cookie_type}selected="selected"{/if}>{$type.name|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}</option>
    {/foreach}
</select>
