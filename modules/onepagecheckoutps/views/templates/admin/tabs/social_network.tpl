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
{if 'checkout50'|array_key_exists:$paramsBack.HELPER_FORM.tabs}
    <div class="tab-pane{if (isset($current_form) && $current_form eq 'checkout50SocialNetwork')} active{/if}" id="tab-checkout50SocialNetwork">
        <div class="row">
            <div class="alert alert-info">
                {l s='The following return URL must be configured in each social network' mod='onepagecheckoutps'}:&nbsp;
                <b>{$paramsBack.LINK->getBaseLink()|escape:'htmlall':'UTF-8'}checkout/myaccount/loginSocialCustomer</b>
            </div>
            <form method="ajax" class="form form-horizontal clearfix col-xs-12" id="form-socialNetwork" autocomplete="off">
                <div class="form-group">
                    <label class="col-lg-2 control-label">{l s='Social network' mod='onepagecheckoutps'}</label>
                    <div class="col-lg-10">
                        <div class="col-xs-12 col-sm-4 nopadding input-group-sm">
                            <select autocomplete="off" class="form-control" id="lst-name" name="name">
                                <option value="" selected>{l s='Choose social network' mod='onepagecheckoutps'}</option>
                            {foreach from=$paramsBack.socialNetworkAvailables key="name" item="keys"}
                                <option value="{$name|escape:'htmlall':'UTF-8'}">{$name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group enabled">
                    <label class="col-lg-2 control-label">{l s='Enabled' mod='onepagecheckoutps'}</label>
                    <div class="col-lg-10">
                        {include file=$paramsBack.MODULE_TPL|cat:'views/templates/admin/helper/form.tpl'
                            option = [
                                name => 'enabled',
                                prefix => 'chk',
                                type => $paramsBack.GLOBALS->type_control->checkbox,
                                check_on => true,
                                label_on => {l s='YES' mod='onepagecheckoutps'},
                                label_off => {l s='NO' mod='onepagecheckoutps'}
                            ]
                            global = $paramsBack.GLOBALS
                        }
                    </div>
                </div>
                {foreach from=$paramsBack.socialNetworkAvailables key="name" item="keysSocialNetwork"}
                    <div id="network-{$name|escape:'htmlall':'UTF-8'}" class="social-networks">
                    {foreach from=$keysSocialNetwork key="nameKey" item="valueKey"}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{$nameKey|escape:'htmlall':'UTF-8'}</label>
                            <div class="col-lg-10">
                                {include file=$paramsBack.MODULE_TPL|cat:'views/templates/admin/helper/form.tpl'
                                    option = [
                                        name => $nameKey,
                                        prefix => 'txt',
                                        type => $paramsBack.GLOBALS->type_control->textbox,
                                        value => $valueKey
                                    ]
                                    global = $paramsBack.GLOBALS
                                }
                            </div>
                        </div>
                    {/foreach}
                    </div>
                {/foreach}

                <div class="form-group actions">
                    <div class="col-xs-12 col-xs-offset-0 col-md-10 col-md-offset-2">
                        <div class="col-xs-12 col-md-6 nopadding action-buttons-container">
                            <button type="button" name="socialNetwort-save" id="btn-socialNetwort-save" class="btn btn-primary has-action">
                                <i class="fa-pts fa-pts-save"></i>
                                {l s='Save' mod='onepagecheckoutps'}
                            </button>
                            <button type="button" name="socialNetwort-cancel" id="btn-socialNetwort-cancel" class="btn btn-default has-action">
                                <i class="fa-pts fa-pts-eraser"></i>
                                {l s='Cancel' mod='onepagecheckoutps'}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="panel col-lg-12 col-sm-12 table-container-list">
                <div class="panel-heading">
                    {l s='Available social networks' mod='onepagecheckoutps'}
                    <span class="badge" id="badge-socialNetwort"></span>
                    <span class="panel-heading-actions">
                        <a class="btn-socialNetwort-reload">
                            <span class="label-tooltip" data-placement="top" data-html="true" data-original-title=" {l s='Refresh' mod='onepagecheckoutps'}" data-toggle="tooltip" title="">
                                <i class="fa-pts fa-pts-refresh"></i>
                            </span>
                        </a>
                        <a class="list-toolbar-btn btn-socialNetwort-add">
                            <span class="label-tooltip" data-placement="top" data-html="true" data-original-title=" {l s='Add new' mod='onepagecheckoutps'}" data-toggle="tooltip" title="">
                                <i class="fa-pts fa-pts-plus"></i>
                            </span>
                        </a>
                    </span>
                </div>
                <div class="col-xs-12 nopadding table-responsive-row clearfix">
                    <table id="table-form-list-socialNetwort" class="table table-hover table-striped">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/if}