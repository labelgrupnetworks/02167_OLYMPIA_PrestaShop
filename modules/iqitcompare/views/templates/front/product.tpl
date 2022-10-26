{*
* 2017 IQIT-COMMERCE.COM
*
* @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
* @copyright 2017 IQIT-COMMERCE.COM
* @license   Commercial license (You can not resell or redistribute this software.)
*
*}

<article class="iqitcompare-product">
    <div class="pack-product-container ">
      <div class="thumb-mask">
          <a href="{$product.url}" > 
          {if $product.default_image}
            <img src="{$product.default_image.bySize.home_default.url}" alt="{$product.name|escape:'quotes'}"  class="img-fluid" loading="lazy">
        {else}
            <img src="{$urls.no_picture_image.bySize.home_default.url}" class="img-fluid"  loading="lazy" />
        {/if}
          
          </a>
      </div>
      <div class="pack-product-name">
        <a href="{$product.url}">{$product.name}</a>
      </div>
      {if $product.show_price}
        <div class="product-price">
          {if $product.has_discount}
            <span class="product-discount"><span class="regular-price">{$product.regular_price}</span></span>
          {/if}
          <span class="product-price">{$product.price}</span>
        </div>
      {/if}

          <a href="#" class="js-iqitcompare-remove iqitcompare-remove"
             data-id-product="{$product.id_product|intval}"
            data-url="{url entity='module' name='iqitcompare' controller='actions'}">
              <i class="fa fa-trash-o" aria-hidden="true"></i>
            </a>

        <div class="product-add-cart mt-3 mb-3">
            {if $product.add_to_cart_url && ($product.quantity > 0 || $product.allow_oosp) && !$configuration.is_catalog}
                <form action="{$product.add_to_cart_url}" method="post">

                    <input type="hidden" name="id_product" value="{$product.id}">
                    <div class="input-group-add-cart ">
                        <div class="d-none">
                        <input
                                type="number"
                                name="qty"
                                value="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity neq ''}{$product.product_attribute_minimal_quantity}{else}{$product.minimal_quantity}{/if}"
                                class="form-control input-qty"
                                min="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity neq ''}{$product.product_attribute_minimal_quantity}{else}{$product.minimal_quantity}{/if}"
                        >
                        </div>
                        <button
                                class="btn btn-product-list add-to-cart"
                                data-button-action="add-to-cart"
                                type="submit"
                                {if !$product.add_to_cart_url}
                                    disabled
                                {/if}
                        ><i class="fa fa-shopping-bag fa-fw bag-icon"
                            aria-hidden="true"></i> <i class="fa fa-circle-o-notch fa-spin fa-fw spinner-icon" aria-hidden="true"></i> {l s='Add to cart' d='Shop.Theme.Actions'}
                        </button>
                    </div>

                </form>
            {else}
                <a href="{$product.canonical_url}"
                   class="btn btn-product-list"
                > {l s='View' d='Shop.Theme.Actions'}
                </a>
            {/if}
        </div>

        {hook h='displayProductRating' product=$product}

      </div>
    </div>
</article>