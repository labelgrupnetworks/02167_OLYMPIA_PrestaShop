{if !empty($booking_facilities)}
    <div class="kb-product-facilities-block col-lg-12">
        <h5 class="h5">
            {l s='Facilities' mod='kbbookingcalendar'}
        </h5>
        <div class="kb_slider kb-center slider-nav">
            {foreach $booking_facilities as $facilities}
                <div class="kb-slick-block col-lg-4" style="">
                    {if $facilities['image_type'] == 'font'}
                        <i class="fa fa-4x {$facilities['font_awesome_icon']}"></i>
                    {else}
                        <img src="{$facilities['upload_image']}" height="62" width="62">
                    {/if}
                    <div>
                        <label>
                            <span>{$facilities['name']}</span>
                        </label>
                    </div>
                </div>
            {/foreach}
        </div>
        <script>
            var kb_slider_item_count = '{$booking_facilities|count}';

        </script>
    </div>
    <div style="clear: both;"></div>
{/if}

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2019 Knowband
* @license   see file: LICENSE.txt
*
*}
