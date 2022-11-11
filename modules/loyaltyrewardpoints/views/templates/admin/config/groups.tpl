{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Musaffar Patel <musaffar.patel@gmail.com>
*  @copyright 2016-2021 Musaffar Patel
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*}

<ul class="nav nav-tabs lrp-nav-tabs" id="myTab" role="tablist">
    <li class="nav-item active">
        <a class="nav-link" data-toggle="tab" href="#lrp-configure-tab" role="tab">{l s='Configure' mod='loyaltyrewardpoints'}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#lrp-global-tab" role="tab">{l s='Global Options' mod='loyaltyrewardpoints'}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#lrp-insights-tab" role="tab">{l s='Insights' mod='loyaltyrewardpoints'}</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane pap-tab-pane panel active" id="lrp-configure-tab" role="tabpanel">
        <div class="row">
            <div class="alert alert-info mt-2" role="alert">
                <p class="alert-text">
                    {l s='Start by selecting a group.  Be sure to configure each customer group below'  mod='loyaltyrewardpoints'}
                </p>
            </div>

            <div id="lrp-groups-list" class="col-xs-12 col-sm-12 col-md-6">
                <div class="table-responsive-row clearfix">
                    <table id="table-customer" class="table customer">
                        <thead>
                        <tr class="nodrag nodrop">
                            <th class="fixed-width-xs text-center">
                                <span class="title_box">{l s='ID' mod='loyaltyrewardpoints'}</span>
                            </th>
                            <th class="">
                                <span class="title_box">{l s='Group' mod='loyaltyrewardpoints'}</span>
                            </th>
                        </tr>
                        {foreach from=$customer_groups item=group}
                            <tr class="group nodrag nodrop filter row_hover" data-id_group="{$group.id_group|escape:'htmlall':'UTF-8'}" data-name="{$group.name|escape:'htmlall':'UTF-8'}">
                                <td>
                                    {$group.id_group|escape:'htmlall':'UTF-8'}
                                </td>
                                <td>
                                    {$group.name|escape:'htmlall':'UTF-8'}
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 60px;">
            <h2>{l s='Your Unique Cron URL' mod='loyaltyrewardpoints'}</h2>
            <span>
                Your unique and secure cron url for tasks such as sending out birthday points is:<br>
                <div style="padding: 10px; background-color: #f1f1f1; margin-top: 10px;">
                    <strong>{$cron_url|escape:'htmlall':'UTF-8'}</strong>
                </div>
            </span>
        </div>
    </div>

    <div class="tab-pane pap-tab-pane" id="lrp-global-tab" role="tabpanel">
        {$form_global nofilter}
    </div>

    <div class="tab-pane pap-tab-pane panel" id="lrp-insights-tab" role="tabpanel">
        {$insights nofilter}
    </div>
</div>