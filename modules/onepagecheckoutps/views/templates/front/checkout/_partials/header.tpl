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
<div id="opc_header" class="container">
    {if $OPC.General.showButtonContinueShopping}
    <div class="row">
    {/if}

    {if $OPC.General.showButtonContinueShopping}
        <div class="col-xs-1 col-md-4" id="continue_shopping">
            <a href="{if $OPC.General.linkContinueShopping neq ''}{$OPC.General.linkContinueShopping|escape:'htmlall':'UTF-8'}{else}{$urls.base_url|escape:'htmlall':'UTF-8'}{/if}">
                <i class="material-icons mr-1">chevron_left</i>
                <span class="hidden-sm-down">
                    {l s='Continue shopping' mod='onepagecheckoutps'}
                </span>
            </a>
        </div>
    {/if}

    <div id="header_logo" class="text-center {if $OPC.General.showButtonContinueShopping}col-xs-10 col-md-4{/if}">
        <a href="{$urls.base_url|escape:'htmlall':'UTF-8'}">
            <img class="logo img-responsive" src="{$shop.logo|escape:'htmlall':'UTF-8'}" alt="{$shop.name|escape:'htmlall':'UTF-8'}">
        </a>
    </div>

    {if $OPC.General.showButtonContinueShopping}
    </div>
    {/if}
</div>