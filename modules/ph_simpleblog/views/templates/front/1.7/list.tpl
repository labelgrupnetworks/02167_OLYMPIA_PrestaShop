{extends file='page.tpl'}

{block name='page_header_container'}
<div class="page-heading">
    <h1 class="h1">
    	{if $is_category eq true}
			{$blogCategory->name}
		{else}
			{$blogMainTitle}
		{/if}
    </h1>
	</div><!--.page-heading-->
{/block}

{block name='head_seo_title'}{strip}{$meta_title}{/strip}{/block}
{block name='head_seo_description'}{strip}{$meta_description}{/strip}{/block}

{block name='page_content'}
	{block name='category_elements'}
		{block name='category_heading'}
		{if $is_category eq true}
			{if Configuration::get('PH_BLOG_DISPLAY_CATEGORY_IMAGE') && isset($blogCategory->image) || !empty($blogCategory->description) && Configuration::get('PH_BLOG_DISPLAY_CAT_DESC')}
				<div class="cardblog">
					{if Configuration::get('PH_BLOG_DISPLAY_CATEGORY_IMAGE') && isset($blogCategory->image)}
						<div class="simpleblog-category-image">
							<img src="{$blogCategory->image}" alt="{$blogCategory->name}" class="img-fluid" />
						</div>
					{/if}

					{if !empty($blogCategory->description) && Configuration::get('PH_BLOG_DISPLAY_CAT_DESC')}
						<div class="ph_cat_description rte card-block pb-1">
							{$blogCategory->description nofilter}
						</div>
					{/if}
				</div>
			{/if}
		{/if}
		{/block}

		{block name='category_children'}
		{if Configuration::get('PH_BLOG_DISPLAY_CATEGORY_CHILDREN')}
			{if $is_category eq true}
				{assign var="subcategories" value=SimpleBlogCategory::getChildrens($blogCategory->id_simpleblog_category)}
			{else}
				{assign var="subcategories" value=SimpleBlogCategory::getCategories($language.id, true, true)}
			{/if}
			{if $subcategories && $subcategories|count > 0}
			<div class="blogSubcat mb-2">
				<ul class="blogSubcat__list my-1">
					{foreach from=$subcategories item=category}
						<li class="blogSubcat__item">
							<a
								class="blogSubcat__link cardblog btn mb-0"
								href="{$category.url}"
								>	
								{$category.name}
							</a>
						</li>
					{/foreach}
				</ul>
			</div>
			{/if}
		{/if}
		{/block}
	{/block}

	{block name='listing'}
	<div class="simpleblog__listing">
		<div class="{if $useMasonry}blog-masonry-list{else}row{/if}">
			{if isset($posts) && count($posts)}
			{foreach from=$posts item=post}
	        	{include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl" masonry=$useMasonry}
		    {/foreach}
	        {else}
			<div class="warning alert alert-warning col-xs-12">{l s='There are no posts' mod='ph_simpleblog'}</div>
	        {/if}
		</div><!-- .row -->
	</div><!-- .simpleblog__listing -->
	{/block}

	{block name='pagination'}
	{if isset($posts) && count($posts)}
	    {if $is_category}
	    	{include file="module:ph_simpleblog/views/templates/front/1.7/pagination.tpl" rewrite=$blogCategory->link_rewrite type='category'}
	    {else}
	    	{include file="module:ph_simpleblog/views/templates/front/1.7/pagination.tpl" rewrite=false type=false}
	    {/if}
	{/if}
	{/block}
{/block}