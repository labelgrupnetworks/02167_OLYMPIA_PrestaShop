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

{foreach from=$option.tooltip item='tooltip' key='type'}
    <span id="tooltip-{$type|escape:'htmlall':'UTF-8'}-{$option.name|escape:'htmlall':'UTF-8'}" type="button"
            class="btn-popover pts-tooltip"
            data-container="#container-{$option.name|escape:'htmlall':'UTF-8'}"
            data-toggle="button popover" {*title="{$tooltip.title|escape:'htmlall':'UTF-8'}"*}>
        {if $type eq 'information'}
            <i class='fa-pts fa-pts-question-circle nohover'></i>
        {else if $type eq 'warning'}
            <i class='fa-pts fa-pts-info-circle nohover'></i>
        {/if}
    </span>
    <div id="tooltip-{$type|escape:'htmlall':'UTF-8'}-{$option.name|escape:'htmlall':'UTF-8'}-content"
            class="tooltip-content {if isset($option.html) and $option.html}popover-html{/if}">{$tooltip.content|escape:'quotes':'UTF-8'}</div>
{/foreach}