{*
* 2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement
*
* @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
* @copyright 2017 IQIT-COMMERCE.COM
* @license   Commercial license (You can not resell or redistribute this software.)
*
*}

<div id="iqit-reviews" class="mt-3">

    <div id="iqit-reviews-top">
        <div class="row justify-content-between align-items-center">

            <div class="col mb-3">
                {if $reviews}
                <div id="iqitreviews-snippet">


                {math equation="floor(x)" x=$snippetData.avarageRating assign=stars}
                <span class="iqitreviews-rating">
            {section name="i" start=0 loop=5 step=1}
                {if ($stars - $smarty.section.i.index) >= 1 }
                    <i class="fa fa-star iqit-review-star"></i>
                {elseif $snippetData.avarageRating - $smarty.section.i.index > 0}
                    <i class="fa fa-star-half-o iqit-review-star"></i>
                {else}
                    <i class="fa fa-star-o iqit-review-star"></i>
                {/if}
            {/section}
            </span>

            {$snippetData.reviewsNb} {l s='Reviews' d='Modules.Iqitreviews.Shop'}
            </div>
            {else}
            <div id="iqitreviews-snippet">{l s='No reviews' d='Modules.Iqitreviews.Shop'}</div>
            {/if}
        </div>

        {if $allowGuests || $isLogged}
            <div class="col col-auto mb-3">
                <button type="button" class="btn btn-primary " data-toggle="modal"
                        data-target="#iqit-reviews-modal">
                    <i class="fa fa-pencil-square-o"
                       aria-hidden="true"></i> {l s='Write  review' d='Modules.Iqitreviews.Shop'}
                </button>
            </div>
        {/if}

    </div> <!-- / .row -->
</div><!-- / #iqit-reviews-top -->


<div class="col-xs-12 alert alert-success hidden-xs-up"
     id="iqitreviews-productreview-form-success-alert">
</div>




{if $reviews}

    <div id="iqitreviews-list">

        <script type="application/ld+json">
                {
                    "@context": "http://schema.org",
                    "@type": "Product",
                    "id": "#product-snippet-id",
                    "aggregateRating": {
                        "@type": "AggregateRating",
                        "ratingValue": "{$snippetData.avarageRating|round:1|escape:'html':'UTF-8'}",
                        "reviewCount": "{$snippetData.reviewsNb|escape:'html':'UTF-8'}"
                  }
                }
            </script>

        {foreach from=$reviews item="review"}

            <script type="application/ld+json">
                {
                    "@context": "http://schema.org",
                    "@type": "Product",
                    "id": "#product-snippet-id",
                    "review": {
                        "@type": "Review",
                        "reviewBody": "{$review.comment}",
                        "name": "{$review.title}",
                        "reviewRating": {
                            "@type": "Rating",
                            "ratingValue": "{$review.rating}",
                            "worstRating": "1",
                            "bestRating": "5"
                        },
                        "datePublished": "{dateFormat date=$review.date_add full=0}",
                        "author": {
                            "@type": "Person",
                            "name": "{$review.customer_name}"
                        }
                    }
                }
            </script>

            

            <div class="iqitreviews-review">
                <hr />

                <div class="title"><strong>{$review.title}</strong></div>

                <div class="rating">

                    {section name="i" start=0 loop=5 step=1}
                        {if $review.rating le $smarty.section.i.index}
                            <i class="fa fa-star-o iqit-review-star"></i>
                        {else}
                            <i class="fa fa-star iqit-review-star"></i>
                        {/if}
                    {/section}
                </div>

                <div class="comment">{$review.comment|nl2br nofilter}</div>

                <div class="author">
                   <span >{l s='By' d='Modules.Iqitreviews.Shop'} <span>{$review.customer_name}</span></span>

                    {l s='on' d='Modules.Iqitreviews.Shop'} <span> {dateFormat date=$review.date_add full=0}</span>
                </div>

            </div>
        {/foreach}
    </div>
{/if}

</div>

{if $allowGuests || $isLogged}
    <div class="modal fade" id="iqit-reviews-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{url entity='module' name='iqitreviews' controller='actions' params=['process' => 'addProductReview', 'ajax' => 1]}"
                      method="post" id="iqitreviews-productreview-form">
                    <div class="modal-header">
                        <span class="modal-title">{l s='Write a review' d='Modules.Iqitreviews.Shop'}</span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="col-xs-12 alert alert-success alert-warning hidden-xs-up"
                             id="iqitreviews-productreview-form-alert">
                        </div>


                        <div class="form-fields" id="iqitreviews-productreview-fields">

                            <input type="hidden" name="iqitreviews_id_product" id="iqitreviews_id_product"
                                   value="{$idProduct}"/>

                            {if !$isLogged}
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">{l s='Name' d='Modules.Iqitreviews.Shop'}</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="iqitreviews_customer_name"
                                               id="iqitreviews_customer_name" value=""
                                               placeholder="{l s='John' d='Modules.Iqitreviews.Shop'}"/>
                                    </div>
                                </div>
                            {/if}

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">{l s='Title' d='Modules.Iqitreviews.Shop'}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="iqitreviews_title"
                                           id="iqitreviews_title" value=""
                                           placeholder="{l s='Ex. Very good' d='Modules.Iqitreviews.Shop'}"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">{l s='Rating' d='Modules.Iqitreviews.Shop'}</label>
                                <div class="col-md-6">
                                    <input class="rating " data-max="5" data-min="1" data-empty-value="0"
                                           value="5"
                                           id="iqitreviews_rating" name="iqitreviews_rating" type="number"
                                           data-icon-lib="fa"
                                           data-active-icon="fa-star iqit-review-star" data-inactive-icon="fa-star-o iqit-review-star"/>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">{l s='Comment' d='Modules.Iqitreviews.Shop'}</label>
                                <div class="col-md-6">
                                         <textarea class="form-control" rows="3" name="iqitreviews_comment"
                                                   id="iqitreviews_comment"
                                                   placeholder="{l s='Write your opinion about product' d='Modules.Iqitreviews.Shop'}"></textarea>
                                </div>
                            </div>

                        </div>

                        {hook h='displayGDPRConsent' id_module=$id_module}
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" name="iqitreviews_submit"
                               id="iqitreviews_submit"
                               value=" {l s='Submit review' d='Modules.Iqitreviews.Shop'}">
                    </div>
                </form>
            </div>
        </div>
    </div>
{/if}