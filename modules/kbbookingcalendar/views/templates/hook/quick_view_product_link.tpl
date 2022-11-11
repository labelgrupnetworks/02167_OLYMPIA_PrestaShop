{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2018 Knowband
* @license   see file: LICENSE.txt
*}
    <p class="buttons_bottom_block no-print" id="kb_booking_product_redirect_link_div" style="display:none;">
    <a id="kb_booking_product_redirect_link" class="btn btn-warning exclusive-customize" rel="nofollow" href="{$product_page_link nofilter}"><span>{* variable contains html,url content can not escape*}
        {l s='Click Here to Customize this product.' mod='kbbookingcalendar'}
        </span>
    </a>
</p>
<style>
    #kb_booking_product_redirect_link {
        margin-top: 4%;
    }
</style>