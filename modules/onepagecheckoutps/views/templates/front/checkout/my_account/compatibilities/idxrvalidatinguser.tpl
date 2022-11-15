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

{extends file='module:onepagecheckoutps/views/templates/front/checkout/my_account/my_account.tpl'}

{block name='checkout_my_account_content_item_tab'}
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" data-target="#tab_idxrvalidatinguser" role="tab"
            aria-selected="true">
            {l s='Professional' mod='onepagecheckoutps'}
        </a>
    </li>
{/block}
{block name='checkout_my_account_content_item_content_tab'}
    <div class="tab-pane fade" id="tab_idxrvalidatinguser" role="tabpanel">
        <div id="idxrvalidatinguser">
            <p>
                {l s='Register as a professional and benefit from all the advantages and discounts.' mod='onepagecheckoutps'}
            </p>
            <a id="deluxeb2b" class="professional-rg btn btn-primary w-100 d-block mb-1 mt-1 float-none" href="{$urls.pages.register|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}&idxb2b=1">
                {l s='Professional registration' mod='onepagecheckoutps'}
            </a>
        </div>
    </div>
{/block}
