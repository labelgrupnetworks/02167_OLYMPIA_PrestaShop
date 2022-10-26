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
{block name='product_cover'}
    <div class="product-cover">

        {include file='catalog/_partials/product-flags.tpl'}

        <div id="product-images-large" class="product-images-large swiper-container">
            <div class="swiper-wrapper">
           
                {if $product.images}
                    {foreach from=$product.images item=image name=covers}
                        <div class="product-lmage-large swiper-slide {if $image.id_image == $product.default_image.id_image} js-thumb-selected{/if}">
                            <div class="easyzoom easyzoom-product">
                                <a href="{$image.large.url}" class="js-easyzoom-trigger" rel="nofollow"></a>
                            </div>
                            {if $product.images}<a class="expander" data-toggle="modal" data-target="#product-modal"><span><i class="fa fa-expand" aria-hidden="true"></i></span></a>{/if}
                            <img
                                    data-src="{$image.bySize.large_default.url}"
                                    data-image-large-src="{$image.large.url}"
                                    alt="{if !empty($image.legend)}{$image.legend}{else}{$product.name|truncate:50:'...'}{/if}"
                                    content="{$image.bySize.large_default.url}"
                                    width="{$image.bySize.large_default.width}"
                                    height="{$image.bySize.large_default.height}"
                                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {$image.bySize.large_default.width} {$image.bySize.large_default.height}'%3E%3C/svg%3E"
                                    class="img-fluid swiper-lazy"
                            >
                        </div>
                    {/foreach}
                {else}
                    <div class="swiper-slide">
                        <img src="{$urls.no_picture_image.bySize.large_default.url}"
                             data-image-large-src="{$urls.no_picture_image.large.url}"
                             content="{$urls.no_picture_image.bySize.large_default.url}"
                             width="{$urls.no_picture_image.bySize.large_default.width}"
                             height="{$urls.no_picture_image.bySize.large_default.height}"
                             alt="{$product.name|truncate:40:'...'}"
                             class="img-fluid"
                        >
                    </div>
                {/if}
            </div>
            <div class="swiper-button-prev swiper-button-inner-prev swiper-button-arrow"></div>
            <div class="swiper-button-next swiper-button-inner-next swiper-button-arrow"></div>
        </div>
    </div>
{/block}

