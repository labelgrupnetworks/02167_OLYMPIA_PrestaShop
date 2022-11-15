{**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2018 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<select name="cookieTemplate_selector" class='js_cookieTemplate_selector' data-idcookie="{$cookie.id_cookie|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}">
    {foreach from=$optionTemplateList item=template}
    <option value="{$template.id|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}" {if $template.id == $cookie.id_template}selected="selected"{/if}>{$template.name|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}</option>
    {/foreach}
</select>
