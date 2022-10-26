{if $posts}
<section class="related-blog-posts ph_simpleblog block block-section">
	<h4 class="section-title"><span>{l s='Related posts' mod='ph_relatedposts'}</span>
	</h4>
	<div class="block-content">
		<div class="row simpleblog-posts">
			{foreach $posts as $post}
				{include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl" post=$post}
			{/foreach}
		</div>
	</div>
</section>
{/if}