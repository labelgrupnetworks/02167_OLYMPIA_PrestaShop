{*
 * This file allows you to customize your search page.
 * You can safely remove it if you want it to appear exactly like all other product listing pages
 *}
{extends file='catalog/listing/product-list.tpl'}


{block name="error_content"}
    <h4>{l s='No matches were found for your search' d='Shop.Theme.Catalog'}</h4>
    <p>{l s='Please try other keywords to describe what you are looking for.' d='Shop.Theme.Catalog'}</p>
{/block}



{block name='product_list_bottom_static'}

    {if isset($brands) && count($brands)}
        <div class="mt-5">
            <p class="section-title section-title-big mb-5">{l s='Brands' d='Shop.Warehousetheme'}</p>
            <div class="row">

                {foreach from=$brands item=brand name=brand_list}
                    <div class="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 text-center">
                        <a href="{$brand['link']}" class="d-block">
                            <img src="{$brand['image']}" alt="{$brand['name']}" class="img-fluid" />
                        </a>
                        <div class="mt-4">
                            <a href="{$brand['link']}">
                                {$brand['name']}
                            </a>
                        </div>
                    </div>
                {/foreach}

            </div>
        </div>
    {/if}

    {if isset($posts) && count($posts)}
        <div class="mt-5 ">
            <p class="section-title section-title-big mb-5">{l s='Posts' d='Shop.Warehousetheme'}</p>
            <div class="row wider-gutters simpleblog-posts mt-3">
                {foreach from=$posts item=post}
                        {include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl" post=$post}
                {/foreach}
            </div>
        </div>
    {/if}


{/block}

