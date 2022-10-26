{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{block name='product_tabs'}
    <div class="tabs product-tabs">
    <a name="products-tab-anchor" id="products-tab-anchor"> &nbsp;</a>
        <ul id="product-infos-tabs" class="nav nav-tabs">
            {if $iqitTheme.pp_tabs_placement == 'footer'}
                {capture name="productElementorDescription"}{hook h='displayProductElementor'}{/capture}
            {/if}

                {assign var="isDescriptionExist" value=false}
            {if $product.description || (isset($smarty.capture.productElementorDescription) && $smarty.capture.productElementorDescription != '')}
                {assign var="isDescriptionExist" value=true}

                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab"
                       href="#description">
                        {l s='Description' d='Shop.Theme.Catalog'}
                    </a>
                </li>
            {/if}
            <li class="nav-item  {if !$product.grouped_features}empty-product-details{/if}" id="product-details-tab-nav">
                <a class="nav-link{if !$isDescriptionExist} active{/if}" data-toggle="tab"
                   href="#product-details-tab">
                    {l s='Product Details' d='Shop.Theme.Catalog'}
                </a>
            </li>
            {if $product.attachments}
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#attachments">
                        {l s='Attachments' d='Shop.Theme.Catalog'}
                    </a>
                </li>
            {/if}

            {if $iqitTheme.pp_accesories == 'tab'}
                {if $accessories}
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#accessories-tab">
                            {l s='Related products' d='Shop.Theme.Catalog'}
                        </a>
                    </li>
                {/if}
            {/if}

            {if $iqitTheme.pp_man_desc}
            {if isset($product_manufacturer)}
                {capture name="manufacturerElementorDescription"}{if $iqitTheme.pp_tabs_placement == 'footer'}{hook h='displayManufacturerElementor' manufacturerId = $product_manufacturer->id}{/if}{/capture}
                {if $smarty.capture.manufacturerElementorDescription != '' || $product_manufacturer->description != ''}
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#brand-tab">
                        {l s='About' d='Shop.Warehousetheme'} {$product_manufacturer->name}
                    </a>
                </li>
            {/if}
            {/if}
            {/if}

            {foreach from=$product.extraContent item=extra key=extraKey}
                <li class="nav-item">
                    <a  data-toggle="tab"
                       href="#extra-{$extraKey}"
                    {foreach $extra.attr as $key => $val}
                    {if $key == "class"}
                        {$key}="nav-link {$val}"
                    {else}
                        {if $val != ""}
                            {$key}="{$val}"
                        {/if}
                    {/if}
                    {/foreach}
                    > {$extra.title nofilter}</a>
                </li>
            {/foreach}
        </ul>


        <div id="product-infos-tabs-content"  class="tab-content">
            {if $isDescriptionExist}
            <div class="tab-pane in active" id="description">
                {block name='product_description'}
                    <div class="product-description">
                        <div class="rte-content">{$product.description nofilter}</div>
                        {if $iqitTheme.pp_tabs_placement == 'footer'}
                            {$smarty.capture.productElementorDescription nofilter}
                        {/if}
                    </div>
                {/block}
            </div>
            {/if}
            <div class="tab-pane {if !$isDescriptionExist} in active{/if}"
                 id="product-details-tab"
            >

                {block name='product_details'}
                    {include file='catalog/_partials/product-details.tpl'}
                {/block}

            </div>

            {block name='product_attachments'}
                {if $product.attachments}
                    <div class="tab-pane in" id="attachments">
                        <section class="product-attachments">
                            {foreach from=$product.attachments item=attachment}
                                <div class="attachment">
                                        <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                            {$attachment.name}
                                        </a>
                                    <p> <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.description}</a></p>
                                    <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                        <i class="fa fa-download" aria-hidden="true"></i> {l s='Download' d='Shop.Theme.Actions'}
                                        ({$attachment.file_size_formatted})
                                    </a>
                                    <hr />
                                </div>
                            {/foreach}
                        </section>
                    </div>
                {/if}
            {/block}

            {if $iqitTheme.pp_accesories == 'tab'}
                {if $accessories}
                    <div class="tab-pane in" id="accessories-tab">
                        {block name='product_accessories_tab'}
                                <div class="products row products-grid">
                                    {foreach from=$accessories item="product_accessory" key="position"}
                                        {block name='product_miniature'}
                                            {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory richData=true position=$position}
                                        {/block}
                                    {/foreach}
                                </div>
                        {/block}
                    </div>
                {/if}
            {/if}

            {if $iqitTheme.pp_man_desc}

            {if isset($product_manufacturer)}
                {if $smarty.capture.manufacturerElementorDescription != '' || $product_manufacturer->description != ''}
                 <div class="tab-pane in" id="brand-tab">
                        <div class="rte-content">
                            {$product_manufacturer->description nofilter}
                        </div>
                            {$smarty.capture.manufacturerElementorDescription nofilter}
                    </div>
            {/if}
            {/if}
            {/if}


            {foreach from=$product.extraContent item=extra key=extraKey}
            <div
            {foreach $extra.attr as $key => $val}
                {if $key == "class"}
                    {$key}="tab-pane  in  {$val}"
                {elseif $key == "id"}
                    {$key}="extra-{$extraKey}"
                {else}
                    {if $val != ""}
                        {$key}="{$val}"
                    {/if}
                {/if}
            {/foreach}




            >
            {$extra.content nofilter}
        </div>
        {/foreach}
    </div>
    </div>

    <div class="iqit-accordion" id="product-infos-accordion-mobile" role="tablist" aria-multiselectable="true"></div>
{/block}
