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

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 container-another-modules">
    {foreach from=$modules item='module'}
        <div class="pts-square{if $module->active} installed{/if}">
            <div class="pts-square-content">
                <a href="{if $paramsBack.ISO_LANG eq 'es'}{$module->url->es|escape:'htmlall':'UTF-8'}{else}{$module->url->en|escape:'htmlall':'UTF-8'}{/if}" target="_blank">
                    {if $module->active}
                        {*<span class="pts-square-module-check">
                            <i class="fa-pts fa-pts-check"></i>
                        </span>*}
                    {else}
                        <img class="rs" src="{$module->image|escape:'htmlall':'UTF-8'}"/>
                    {/if}
                    <span class="text-center">{$module->title|escape:'htmlall':'UTF-8'}</span>
                </a>
            </div>
        </div>
    {/foreach}
</div>