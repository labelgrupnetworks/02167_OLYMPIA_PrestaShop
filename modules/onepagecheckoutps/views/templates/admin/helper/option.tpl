{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 *}

{if isset($option.multiple) and $option.multiple}
    <option value="{$value}" {if isset($option.selected_options) and not empty($option.selected_options)
        and in_array($value, ","|explode:$option.selected_options)}selected="true"{/if}
        {foreach from=$item key="key" item="value"}
            data-{$key|escape:'htmlall':'UTF-8'}="{$value|escape:"quotes"}"
        {/foreach}
        >{$text|escape:'htmlall':'UTF-8'}</option>
{else}
    <option value="{$value|escape:'htmlall':'UTF-8'}" {if isset($option.default_option) and $option.default_option eq $value}selected="true"{/if}
    {foreach from=$item key="key" item="value"}
        data-{$key|escape:'htmlall':'UTF-8'}="{$value|escape:"quotes"}"
    {/foreach}
    >{$text|escape:'htmlall':'UTF-8'}</option>
{/if}