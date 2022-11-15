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

{if $recyclablePackAllowed}
    <div class="row">
        <div class="col-xs-12">
            <label for="recyclable">
                <input type="checkbox" name="recyclable" id="recyclable" value="1"
                    {if $recyclable == 1}checked="checked" {/if}
                    class="carrier_checkbox not_unifrom not_uniform" />
                {l s='I agree to receive my order in recycled packaging' mod='onepagecheckoutps'}
            </label>
        </div>
    </div>
{/if}