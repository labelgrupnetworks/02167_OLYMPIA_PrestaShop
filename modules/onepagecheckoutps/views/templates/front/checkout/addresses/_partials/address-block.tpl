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

<div class="address-selector">
    {assign var='nbItemsPerLine' value=$nbItemsPerLine}
    {assign var='nbLi' value=$addresses|@count}

    {if $nbLi > 0}
	    {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}

        {foreach $addresses as $address name=addresses}
            {math equation="(total%perLine)" total=$smarty.foreach.addresses.total perLine=$nbItemsPerLine assign=totModulo}
            <article
                class="js-address-item address-item{if $address.id == $selected and $pageName neq 'addresses'} selected{/if} {if $smarty.foreach.addresses.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.addresses.iteration%$nbItemsPerLine == 1} first-in-line{/if}"
                data-id-address="{$address.id|intval}"
                data-type-address="{$typeAddress|escape:'htmlall':'UTF-8'}"
                id="{$name|classname}-address-{$address.id|intval}"
            >
                <header class="address-header">
                    <div class="address-title">
                        {if $address.id == $selected and $pageName neq 'addresses'}
                            <i class="material-icons">check_circle_outline</i>
                        {/if}
                        <span class="address-alias h4">{$address.alias|escape:'htmlall':'UTF-8'}</span>
                    </div>
                    <hr>
                    <div class="address">{$address.formatted nofilter}</div>
                </header>
                <hr>
                {if $showFooter}
                    <footer class="address-footer">
                        <button
                            class="edit btn btn-outline-primary btn-sm"
                            data-id-address="{$address.id|intval}"
                            data-type-address="{$typeAddress|escape:'htmlall':'UTF-8'}"
                        >
                            <i class="material-icons md-18">edit</i>
                            {l s='Edit' mod='onepagecheckoutps'}
                        </button>
                        <button class="delete btn btn-outline-primary btn-sm"
                        data-id-address="{$address.id|intval}"
                        data-type-address="{$typeAddress|escape:'htmlall':'UTF-8'}"
                        data-token="{$token|escape:'htmlall':'UTF-8'}"
                        >
                            <i class="material-icons md-18">delete</i>
                            {l s='Delete' mod='onepagecheckoutps'}
                        </button>
                    </footer>
                {/if}
            </article>
        {/foreach}
    {else}
        <div class="alert alert-info w-100">
            {l s='There are no addresses available.' mod='onepagecheckoutps'}
        </div>
    {/if}
</div>
