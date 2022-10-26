{extends file='page.tpl'}


{block name='page_title'}
    {l s='Authors' d='Modules.Simpleblog.Shop'}
{/block}

{block name='head_seo_title'}{strip}{$meta_title}{/strip}{/block}

{block name='page_content'}
{if !$authors|@count}
<div class="alert alert-info">{l s='There are no authors' d='Modules.Simpleblog.Shop'}</div>
{else}
<div class="simpleblog__listing">
    <div class="row">
        {foreach $authors as $author}
         <div class="col-md-6 col-xs-12">
            <div class="cardblog authorMiniature">
                <div class="card-block">
                    {if $author->photo}
                    <img src="{$author->photo}" class="img-fluid blog-author-thumb authorMiniature__thumb mb-2">
                    {/if}
                    <h5 class="h3 authorMiniature__name blog-text-no-transform mb-1">
                        {$author}
                    </h5>

                    {if $author->bio}
                    {$author->bio nofilter}
                    {/if}

                    <div class="clearfix">
                        <a href="{$author->getUrl()}" class="btn btn-primary float-xs-right authorMiniature__btn">
                            {l s='See the author\'s articles' d='Modules.Simpleblog.Shop'}
                        </a>
                    </div>
                </div>
                <div class="card-footer">
                    <ul class="authorMiniature__links blogsocial">
                        {if $author->twitter}
                        <li class="blogsocial__elem">
                            <a class="btn btn-blog-social btn-blog-social--twitter" href="{$author->twitter}">Twitter</a>
                        </li>
                        {/if}
                        {if $author->instagram}
                        <li class="blogsocial__elem">
                            <a class="btn btn-blog-social btn-blog-social--instagram" href="{$author->instagram}">Instagram</a>
                        </li>
                        {/if}
                        {if $author->linkedin}
                        <li class="blogsocial__elem">
                            <a class="btn btn-blog-social btn-blog-social--linkedin" href="{$author->linkedin}">Linkedin</a>
                        </li>
                        {/if}
                        {if $author->facebook}
                        <li class="blogsocial__elem">
                            <a class="btn btn-blog-social btn-blog-social--facebook" href="{$author->facebook}">Facebook</a>
                        </li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>   
        {/foreach}            
    </div>
</div>
{/if}
{/block}