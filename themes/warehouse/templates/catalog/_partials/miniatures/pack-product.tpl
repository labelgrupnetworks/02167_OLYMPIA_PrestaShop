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
{block name='pack_miniature_item'}
    <article>
            <div class="pack-product-container">
                <div class="pack-product-quantity">
                    <span>{$product.pack_quantity}x</span>
                </div>
                <div class="pack-product-thumb">
                    <a href="{$product.url}">
                        {if !empty($product.default_image.medium)}
                            <img
                                    src="{$product.default_image.medium.url}"
                                    {if !empty($product.default_image.legend)}
                                        alt="{$product.default_image.legend}"
                                        title="{$product.default_image.legend}"
                                    {else}
                                        alt="{$product.name}"
                                    {/if}
                                    loading="lazy"
                                    data-full-size-image-url="{$product.default_image.large.url}"
                                    class="img-fluid"
                            >
                        {else}
                            <img src="{$urls.no_picture_image.bySize.medium_default.url}"  class="img-fluid"  loading="lazy"/>
                        {/if}

                    </a>
                </div>
                <div class="pack-product-name">
                    <a href="{$product.url}">{$product.name}</a>
                </div>
                {if $showPackProductsPrice}
                    <div class="pack-product-price">
                        <strong>{$product.price}</strong>
                    </div>
                {/if}
            </div>
    </article>
{/block}
