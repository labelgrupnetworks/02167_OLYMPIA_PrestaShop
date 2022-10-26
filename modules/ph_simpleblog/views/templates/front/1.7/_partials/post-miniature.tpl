<div class="simpleblog__listing__post
    {if $blogLayout eq 'grid' AND $columns eq '3'}
        col-md-4 col-sm-6 col-xs-12 col-ms-12 {cycle values="first-in-line,second-in-line,last-in-line"}
    {elseif $blogLayout eq 'grid' AND $columns eq '4'}
        col-md-3 col-sm-6 col-xs-12 col-ms-12 {cycle values="first-in-line,second-in-line,third-in-line,last-in-line"}
    {elseif $blogLayout eq 'grid' AND $columns eq '2'}
        col-md-6 col-sm-6 col-xs-12 col-ms-12 {cycle values="first-in-line,last-in-line"}
    {else}
    col-md-12
    {/if}"
    >
    <div class="simpleblog__listing__post__wrapper cardblog">
        {if $post.post_type == 'url'}
        {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/type-url/post-thumbnail.tpl"}
        {else if $post.post_type == 'video'}
        {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/type-video/post-thumbnail.tpl"}
        {else}
        {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/type-default/post-thumbnail.tpl"}
        {/if}
        <div class="simpleblog__listing__post__wrapper__content card-block">
            {if $post.post_type == 'url'}
                {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/type-url/post-headline.tpl"}
            {else}
                {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/type-default/post-headline.tpl"}
            {/if}

            {if Configuration::get('PH_BLOG_DISPLAY_DESCRIPTION')}
                <p itemprop="description">
                    {$post.short_content|strip_tags:'UTF-8'}
                </p>
            {/if}

            
            {if $post.post_type == 'url'}
                {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/type-url/post-bottomline.tpl"}
            {else}
                {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/type-default/post-bottomline.tpl"}
            {/if}
        </div>

        {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature-footer.tpl"}
    </div><!-- .simpleblog__listing__post__wrapper -->
</div><!-- .simpleblog__listing__post -->