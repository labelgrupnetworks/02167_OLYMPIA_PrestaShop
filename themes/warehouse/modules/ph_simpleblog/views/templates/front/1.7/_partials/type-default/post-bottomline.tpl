{if Configuration::get('PH_BLOG_DISPLAY_MORE')}
    <a href="{$post.url}" class="text-muted simpleblog__listing__post__wrapper__content__readmore">
        <span class="text-underline">{l s='Read more' d='Modules.Simpleblog.Shop'}</span> <i class="fa fa-chevron-right text-smaller"></i>
    </a>
{/if}