{if Configuration::get('PH_BLOG_DISPLAY_THUMBNAIL') && (isset($post.banner_wide) || isset($post.banner_thumb))}
<a href="{$post.external_url}" target="_blank" rel="noreferrer noopener" title="{$post.title}">
    {if $blogLayout eq 'full'}
        <img src="{$post.banner_wide}" alt="{$post.title}" class="img-fluid photo mb-3" width="{$post.banner_sizes.wide.width}" height="{$post.banner_sizes.wide.height}" loading="lazy">
    {else}
        <img src="{$post.banner_thumb}" alt="{$post.title}" class="img-fluid photo mb-3" width="{$post.banner_sizes.thumb.width}" height="{$post.banner_sizes.thumb.height}" loading="lazy">
    {/if}
</a>
{/if}

