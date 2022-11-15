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

{if isset($form.actions) and is_array($form.actions) and count($form.actions)}
    {foreach from=$form.actions item='action'}
        <button type="{if isset($form.method) and $form.method eq 'post'}submit{else}button{/if}"
                {if isset($action.name)}
                    name="{$action.name|escape:'htmlall':'UTF-8'}" id="btn-{$action.name|escape:'htmlall':'UTF-8'}"
                {else}
                    name="form-{$key|escape:'htmlall':'UTF-8'}"
                {/if}
                class="btn btn-{if isset($action.type)}{$action.type|escape:'htmlall':'UTF-8'}{else}primary{/if} pull-right has-action {if isset($action.class)}btn-{$action.class|escape:'htmlall':'UTF-8'}{/if}">
            <i class="fa-pts fa-pts-{if isset($action.icon_class)}{$action.icon_class|escape:'htmlall':'UTF-8'}{else}save{/if} nohover"></i>
            {$action.label|escape:'htmlall':'UTF-8'}
        </button>
    {/foreach}
{/if}