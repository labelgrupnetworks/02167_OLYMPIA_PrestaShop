{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 *}

<div class="row">
    <div class="col-xs-12">
        {if isset($deliveryOptions)}
            <div id="hook-displayBeforeCarrier">
                {$hookDisplayBeforeCarrier nofilter}
            </div>

            {if $deliveryOptions|count}
                <form id="form-shipping_container">
                    {include file='module:onepagecheckoutps/views/templates/front/checkout/shipping/_partials/delivery_options.tpl'}

                    {if $isShowDeliveryMessage}
                        <div id="delivery">
                            <label for="delivery_message">
                                {l s='If you would like to add a comment about your order, please write it in the field below.' mod='onepagecheckoutps'}
                            </label>
                            <textarea rows="2" cols="120" id="delivery_message"
                                name="delivery_message">{$deliveryMessage nofilter}</textarea>
                        </div>
                    {/if}

                    {include file='module:onepagecheckoutps/views/templates/front/checkout/shipping/_partials/recyclable.tpl'}
                    {include file='module:onepagecheckoutps/views/templates/front/checkout/shipping/_partials/gift.tpl'}
                </form>
            {else}
                <p class="alert alert-danger">
                    {l s='Unfortunately, there are no carriers available.' mod='onepagecheckoutps'}
                </p>
            {/if}

            <div id="hook-hookDisplayAfterCarrier">
                {$hookDisplayAfterCarrier nofilter}
            </div>

            <div id="extra_carrier"></div>
        {/if}
    </div>
</div>