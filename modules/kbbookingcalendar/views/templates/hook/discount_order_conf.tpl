{if isset($discount_code)}
<div class='vss-discount-box' style="box-shadow: 2px 2px 8px 0 rgb(0 0 0 / 20%);
    margin-top: 2rem;
    background: chartreuse;
    padding: 1.25rem 1.875rem;">
                    <div class="vss-discount-heading">
                        <span class="vss-underline">{l s='Use Coupon Code' mod='kbbookingcalendar'}</span>&nbsp;:
                        <span style="color:orange;">{$discount_code}</span>
                        <span>{l s='to get' mod='kbbookingcalendar'}</span>
                        <span style="color:orange;">{if isset($discount_percentage)}
                            {$discount_percentage}%
                        {else}
                            {$discount}
                        {/if}</span>
                    <span>{l s='discount on your next order. Offer valid till' mod='kbbookingcalendar'}&nbsp;{$offer_valid_date}.</span>
                </div>
                </div>
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
                    * @copyright 2015 Knowband
                    * @license   see file: LICENSE.txt
                    *
                    * Description
                    *
                    * Admin tpl file
                    *}

