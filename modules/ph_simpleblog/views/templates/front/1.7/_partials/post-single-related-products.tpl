<div class="simpleblog__featuredProducts blog-mb" id="products">
	<h3 class="h2 mb-2">{l s='Related products' mod='ph_simpleblog'}</h3>
	<div class="products row">
    {foreach from=$related_products item="product"}
        {block name='product_miniature'}
            {include file='catalog/_partials/miniatures/product.tpl' product=$product}
        {/block}
    {/foreach}
    </div>
</div>