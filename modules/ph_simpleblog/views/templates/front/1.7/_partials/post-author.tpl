<div class="cardblog author-card blog-mb" id="blog-author">
    <div class="card-block">
        <div class="author-card__row">
            {if $author->photo}
            <div class="author-card__block author-card__block--thumb">
                <img src="{$author->photo}" class="img-fluid blog-author-thumb">
            </div>
            {/if}
            <div class="author-card__block author-card__block--desc">
                <h5 class="h3 blog-text-no-transform">
                    {$author}
                </h5>
                {if $author->bio}
                <div class="mb-0">
                    {$author->bio nofilter}
                </div>
                {/if}

                {if $showAllPostsBtn|default:true}
                <div class="clearfix">
                    <a href="{$author->getUrl()}" class="btn btn-primary float-xs-right authorMiniature__btn">
                        {l s='See the author\'s articles' mod='ph_simpleblog'}
                    </a>
                </div>
                {/if}
            </div>

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