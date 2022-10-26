<div class="simpleblog__listing__post__wrapper__footer pt-3 mt-3 text-muted">

        {if Configuration::get('PH_BLOG_DISPLAY_DATE')}
        <div class="simpleblog__listing__post__wrapper__footer__block d-inline-block mr-2">
            <i class="fa fa-calendar"></i>
            <time datetime="{$post.date_add|date_format:'c'}">
                {$post.date_add|date_format:Configuration::get('PH_BLOG_DATEFORMAT')}
            </time>
        </div>
        {/if}
        {if $is_category eq false && Configuration::get('PH_BLOG_DISPLAY_CATEGORY')}
        <div class="simpleblog__listing__post__wrapper__footer__block d-inline-block mr-2">
            <i class="fa fa-tags"></i>
            <a href="{$post.category_url}" title="{$post.category}" rel="category" class="text-muted">{$post.category}</a>
        </div>
        {/if}
        {if isset($post.author) && !empty($post.author) && Configuration::get('PH_BLOG_DISPLAY_AUTHOR')}
        <div class="simpleblog__listing__post__wrapper__footer__block d-inline-block mr-2">
            <i class="fa fa-user"></i>
            <span>
                {$post.author}
            </span>
        </div>
        {/if}
        {if $post.allow_comments eq true && Configuration::get('PH_BLOG_COMMENTS_SYSTEM') == 'native' && Configuration::get('PH_BLOG_DISPLAY_COMMENTS')}
        <div class="simpleblog__listing__post__wrapper__footer__block d-inline-block mr-2">
            <i class="fa fa-comments"></i>
            <span>
                {$post.comments} {l s='comments'  d='Modules.Simpleblog.Shop'}
            </span>
        </div>
        {/if}
        {if Configuration::get('PH_BLOG_DISPLAY_VIEWS') && $post.post_type != 'url'}
        <div class="simpleblog__listing__post__wrapper__footer__block d-inline-block mr-2">
            <i class="fa fa-eye"></i>
            <span>
                {$post.views} {l s='views'  d='Modules.Simpleblog.Shop'}
            </span>
        </div>
        {/if}
</div><!-- .simpleblog__listing__post__wrapper__footer -->