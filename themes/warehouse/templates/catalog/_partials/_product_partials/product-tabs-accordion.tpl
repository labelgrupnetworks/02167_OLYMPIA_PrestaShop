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
    <a name="products-tab-anchor" id="products-tab-anchor"> &nbsp;</a>


    <div id="productdaas-accordion" class="iqit-accordion mb-5" role="tablist" aria-multiselectable="true">

        {if $iqitTheme.pp_tabs_placement == 'footer'}
            {capture name="productElementorDescription"}{hook h='displayProductElementor'}{/capture}
        {/if}

        {assign var="isDescriptionExist" value=false}

        {if $product.description || (isset($smarty.capture.productElementorDescription) && $smarty.capture.productElementorDescription != '')}
            {assign var="isDescriptionExist" value=true}
        <div class="card">
            <div class="title" role="tab">
                <a data-toggle="collapse" data-parent="#productdaas-accordion" href="#productdaas-accordion-description" aria-expanded="true">
                    {l s='Description' d='Shop.Theme.Catalog'}
                    <i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i>
                    <i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i>
                </a>
            </div>
            <div id="productdaas-accordion-description" class="content collapse  show" role="tabpanel">
                    {block name='product_description'}
                        <div class="product-description">
                        {if $product.description}<div class="rte-content">{$product.description nofilter}</div>{/if}
                            {if $iqitTheme.pp_tabs_placement == 'footer'}
                                {$smarty.capture.productElementorDescription nofilter}
                            {/if}
                        </div>
                    {/block}
            </div>
        </div>
    {/if}

        <div class="card {if !$product.grouped_features}empty-product-details{/if}" id="product-details-tab-card">
            <div class="title" role="tab">
                <a  {if $isDescriptionExist} class="collapsed"{/if} data-toggle="collapse" data-parent="#productdaas-accordion" href="#productdaas-accordion-details" aria-expanded="true">
                    {l s='Product Details' d='Shop.Theme.Catalog'}
                    <i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i>
                    <i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i>
                </a>
            </div>
            <div id="productdaas-accordion-details" class="content collapse {if !$isDescriptionExist} show{/if}" role="tabpanel">
                <div class="mt-4 mb-3">
                    {block name='product_details'}
                    {include file='catalog/_partials/product-details.tpl'}
                {/block}
                </div>
            </div>
        </div>


        {if $product.attachments}
            <div class="card">
                <div class="title" role="tab">
                    <a class="collapsed" data-toggle="collapse" data-parent="#productdaas-accordion" href="#productdaas-accordion-attachments" aria-expanded="true">
                        {l s='Attachments' d='Shop.Theme.Catalog'}
                        <i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i>
                        <i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i>
                    </a>
                </div>
                <div id="productdaas-accordion-attachments" class="content collapse" role="tabpanel">
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
                </div>
            </div>
        {/if}


        {if $iqitTheme.pp_accesories == 'tab'}
            {if $accessories}
                <div class="card">
                    <div class="title" role="tab">
                        <a class="collapsed" data-toggle="collapse" data-parent="#productdaas-accordion" href="#productdaas-accordion-accessories" aria-expanded="true">
                            {l s='Related products' d='Shop.Theme.Catalog'}
                            <i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i>
                            <i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div id="productdaas-accordion-accessories" class="content collapse" role="tabpanel">
                        {block name='product_accessories_tab'}
                            <div class="products row products-grid">
                                {foreach from=$accessories item="product_accessory" key="position"}
                                    {block name='product_miniature'}
                                        {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory position=$position}
                                    {/block}
                                {/foreach}
                            </div>
                        {/block}
                    </div>
                </div>
            {/if}
        {/if}


        {if $iqitTheme.pp_man_desc}
        {if isset($product_manufacturer)}
            {capture name="manufacturerElementorDescription"}{if $iqitTheme.pp_tabs_placement == 'footer'}{hook h='displayManufacturerElementor' manufacturerId = $product_manufacturer->id}{/if}{/capture}
            {if $smarty.capture.manufacturerElementorDescription != '' || $product_manufacturer->description != ''}
                <div class="card">
                    <div class="title" role="tab">
                        <a class="collapsed" data-toggle="collapse" data-parent="#productdaas-accordion" href="#productdaas-accordion-manufacturer" aria-expanded="true">
                            {l s='About' d='Shop.Warehousetheme'} {$product_manufacturer->name}
                            <i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i>
                            <i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div id="productdaas-accordion-manufacturer" class="content collapse" role="tabpanel">
                        <div class="rte-content">
                            {$product_manufacturer->description nofilter}
                        </div>
                        {$smarty.capture.manufacturerElementorDescription nofilter}
                    </div>
                </div>
            {/if}
        {/if}
        {/if}


        {foreach from=$product.extraContent item=extra key=extraKey}

            <div
            {foreach $extra.attr as $key => $val}
                {if $key == "class"}
                    {$key}="card {$val}"
                {else}
                    {if $val != ""}
                        {$key}="{$val}"
                    {/if}
                {/if}
            {/foreach}
            >
                <div class="title" role="tab">
                    <a class="collapsed" data-toggle="collapse" data-parent="#productdaas-accordion" href="#productdaas-accordion-extra-{$extraKey}" aria-expanded="true">
                        {$extra.title nofilter}
                        <i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i>
                        <i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i>
                    </a>
                </div>
                <div id="productdaas-accordion-extra-{$extraKey}" class="content collapse" role="tabpanel">
                    {$extra.content nofilter}
                </div>
            </div>
        {/foreach}

    </div>

{/block}
