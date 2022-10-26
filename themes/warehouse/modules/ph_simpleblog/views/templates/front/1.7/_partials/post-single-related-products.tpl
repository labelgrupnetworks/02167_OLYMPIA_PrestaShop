<div class="simpleblog__featuredProducts block block-section" id="products">
	<h3 class="section-title"><span>{l s='Related products' d='Modules.Simpleblog.Shop'}</span></h3>
	<div class="block-content mt-4">
		<div class="products  products-grid  row">
		{foreach from=$related_products item="product"}
			{block name='product_miniature'}
				{include file='catalog/_partials/miniatures/product.tpl' product=$product}
			{/block}
		{/foreach}
		</div>
	</div>
</div>