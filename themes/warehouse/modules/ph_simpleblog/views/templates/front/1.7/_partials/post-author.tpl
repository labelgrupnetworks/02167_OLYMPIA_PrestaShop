<div class="cardblog author-card blog-mb" id="blog-author">
    <div class="card-block">
        <div class="author-card__row">
            {if $author->photo}
            <div class="author-card__block author-card__block--thumb">
                <img src="{$author->photo}" class="img-fluid blog-author-thumb">
            </div>
            {/if}
            <div class="author-card__block author-card__block--desc">
                {if $showAllPostsBtn|default:true}

                    <a href="{$author->getUrl()}" class="btn btn-primary float-xs-right ml-3 authorMiniature__btn">
                        {l s='See the author\'s articles' d='Modules.Simpleblog.Shop'}
                    </a>

                {/if}
                <h5 class="h3 blog-text-no-transform">
                    {$author}
                </h5>
                {if $author->bio}
                <div class="mb-0">
                    {$author->bio nofilter}
                </div>
                {/if}

                <ul class="authorMiniature__links blogsocial">
                    {if $author->twitter}
                        <li class="blogsocial__elem">
                            <a  href="{$author->twitter}"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                        </li>
                    {/if}
                    {if $author->instagram}
                        <li class="blogsocial__elem">
                            <a  href="{$author->instagram}"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                        </li>
                    {/if}
                    {if $author->linkedin}
                        <li class="blogsocial__elem">
                            <a href="{$author->linkedin}"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                        </li>
                    {/if}
                    {if $author->facebook}
                        <li class="">
                            <a class="" href="{$author->facebook}"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                        </li>
                    {/if}
                </ul>

            </div>

        </div>
    </div>

</div>