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

{extends file='checkout/checkout.tpl'}

{if !$OPC.General.showNativeHeader}
    {block name='header'}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/header.tpl'}
    {/block}
    {* Support template: transformer *}
    {if $OPC.General.themeName eq 'transformer'}
        {block name='checkout_header'}
            {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/header.tpl'}
        {/block}
    {/if}
{/if}

{block name='notifications'}{/block}
{block name='breadcrumb'}{/block}

{* Support template *}
{block name='right_column'}{/block}

{block name='content'}
    <section id="content">
        {if $OPC.General.Design.style eq 'steps'}
            {include file='module:onepagecheckoutps/views/templates/front/checkout/steps.tpl'}
        {else if $OPC.General.Design.style eq 'three_columns'}
            {include file='module:onepagecheckoutps/views/templates/front/checkout/three_columns.tpl'}
        {else}
            {include file='module:onepagecheckoutps/views/templates/front/checkout/vertical.tpl'}
        {/if}
    </section>
{/block}

{if !$OPC.General.showNativeFooter}
    {block name='footer'}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/footer.tpl'}
    {/block}
    {* Support template: transformer *}
    {if $OPC.General.themeName eq 'transformer'}
        {block name='checkout_footer'}
            {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/footer.tpl'}
        {/block}
    {/if}

    {* Support other templates *}
    {block name='hook_footer_before'}{/block}
    {block name='hook_footer'}
        {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/footer.tpl'}
    {/block}
    {block name='hook_footer_after'}{/block}
{/if}