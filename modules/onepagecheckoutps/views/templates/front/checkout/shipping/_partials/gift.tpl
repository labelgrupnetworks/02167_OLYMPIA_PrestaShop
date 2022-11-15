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

{if $Gift.allowed}
    <div class="row">
        <div class="col-xs-12">
            <label for="gift">
                <input
                    type="checkbox"
                    name="gift"
                    id="gift"
                    value="1"
                    class="carrier_checkbox"
                    {if $Gift.isGift}checked{/if}
                />
                {l s='I would like the order to be gift-wrapped.' mod='onepagecheckoutps'}&nbsp;{$Gift.label|escape:'htmlall':'UTF-8'}
            </label>
        </div>

        <div class="col-xs-12">
            <p id="gift_message_container" class="textarea" {if !$Gift.isGift}style="display: none;"{/if}>
                <label for="gift_message">
                    {l s='If you\'d like, you can add a note to the gift:' mod='onepagecheckoutps'}
                </label>
                <textarea rows="2" cols="120" id="gift_message" name="gift_message" class="form-control">{$Gift.message|escape:'htmlall':'UTF-8'}</textarea>
            </p>
        </div>
    </div>
{/if}