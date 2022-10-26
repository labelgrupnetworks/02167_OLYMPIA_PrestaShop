<div class="related-blog-posts ph_simpleblog">
	<div class="row simpleblog-posts">
		{foreach $posts as $post}
		{include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl" post=$post}
		{/foreach}
	</div>
</div>