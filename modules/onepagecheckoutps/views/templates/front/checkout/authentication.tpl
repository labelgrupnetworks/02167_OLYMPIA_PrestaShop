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
{extends file='page.tpl'}

{block name='notifications'}{/block}

{block name='page_content'}
    <div id="opc_main" class="opc-bootstrap-513">
        {include
            file = 'module:onepagecheckoutps/views/templates/front/checkout/steps/step.tpl'
            name = 'my_account'
            accordion = false
            render = $stepMyAccountRendered
        }
    </div>
{/block}