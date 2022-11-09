 <div id="blockcart" class="blockcart cart-preview"
         data-refresh-url="{$refresh_url}">
        <a id="cart-toogle" class="cart-toogle header-btn header-cart-btn" data-toggle="dropdown" data-display="static">
            
            {if $page.page_name == 'index'}
            <img src="{$urls.img_url}carrito_blanco.svg" alt="{l s='My Account' d='Shop.Theme.Actions'}" />
            {else}
            <img src="{$urls.img_url}carrito_negro.svg" alt="{l s='My Account' d='Shop.Theme.Actions'}" />
            {/if}

            <br /><span class="header_option">{l s='Cesta' d='Shop.Theme.Checkout'} ({$cart.products_count})</span>

            
            <span class="info-wrapper">
            <span class="title">{l s='Cart' d='Shop.Theme.Checkout'}</span>
            <span class="cart-toggle-details">
            <span class="text-faded cart-separator"> / </span>
            {if $cart.products_count > 0}
            <span class="cart-products-count">({$cart.products_count})</span>
            {foreach from=$cart.subtotals item="subtotal"}
                {if isset($subtotal.type) && $subtotal.type == 'products'}
                        <span class="value">{$subtotal.value}</span>
                {/if}
            {/foreach}
            {else}
                {l s='Empty' d='Shop.Theme.Checkout'}
            {/if}
            </span>
            </span>
        </a>
        {include 'module:ps_shoppingcart/ps_shoppingcart-content.tpl' class='dropdown'}
 </div>




