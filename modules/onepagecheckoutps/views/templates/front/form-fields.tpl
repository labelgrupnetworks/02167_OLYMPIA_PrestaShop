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

{if $field.type == 'hidden'}
    {block name='form_field_item_hidden'}
        <input type="hidden" name="{$field.name|escape:'htmlall':'UTF-8'}" value="{$field.value|escape:'htmlall':'UTF-8'}">
    {/block}
{else}
    <div class="row {if !empty($field.errors)}has-error{/if}">
        <div id="field_customer_{$field.name|escape:'htmlall':'UTF-8'}" class="form-group col-xs-12 col-12">
            {if $field.type === 'checkbox'}
                {block name='form_field_item_checkbox'}
                    <label>
                        <input name="{$field.name|escape:'htmlall':'UTF-8'}" class="not_unifrom not_uniform" type="checkbox" value="1" {if $field.value}checked="checked"{/if} {if $field.required}required{/if}>
                        {$field.label nofilter}
                    </label>
                {/block}
            {else}
                <label class="col-md-4 form-control-label{if $field.required} required{/if}">
                    {$field.label nofilter}
                </label>

                <div class="col-md-5{if ($field.type === 'radio-buttons')} form-control-valign{/if}">
                    {if $field.type === 'select'}
                        {block name='form_field_item_select'}
                            <select class="form-control form-control-select" name="{$field.name|escape:'htmlall':'UTF-8'}" {if $field.required}required{/if}>
                                <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
                                    {foreach from=$field.availableValues item="label" key="value"}
                                        <option value="{$value|escape:'htmlall':'UTF-8'}" {if $value eq $field.value} selected {/if}>{$label|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                            </select>
                        {/block}
                    {elseif $field.type === 'countrySelect'}
                        {block name='form_field_item_country'}
                            <select class="form-control form-control-select js-country" name="{$field.name|escape:'htmlall':'UTF-8'}" {if $field.required}required{/if}>
                                <option value disabled selected>{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
                                {foreach from=$field.availableValues item="label" key="value"}
                                    <option value="{$value|escape:'htmlall':'UTF-8'}" {if $value eq $field.value} selected {/if}>{$label|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        {/block}
                    {elseif $field.type === 'radio-buttons'}
                        {block name='form_field_item_radio'}
                            {foreach from=$field.availableValues item="label" key="value"}
                                <label class="radio-inline">
                                    <span class="custom-radio">
                                        <input name="{$field.name|escape:'htmlall':'UTF-8'}" type="radio" value="{$value|escape:'htmlall':'UTF-8'}" {if $field.required}required{/if} {if $value eq $field.value} checked {/if}>
                                        <span></span>
                                    </span>
                                    {$label|escape:'htmlall':'UTF-8'}
                                </label>
                            {/foreach}
                        {/block}
                    {elseif $field.type === 'date'}
                        {block name='form_field_item_date'}
                            <input name="{$field.name|escape:'htmlall':'UTF-8'}" class="form-control" type="date" value="{$field.value|escape:'htmlall':'UTF-8'}" placeholder="{if isset($field.availableValues.placeholder)}{$field.availableValues.placeholder|escape:'htmlall':'UTF-8'}{/if}">
                            {if isset($field.availableValues.comment)}
                                <span class="form-control-comment">
                                    {$field.availableValues.comment|escape:'htmlall':'UTF-8'}
                                </span>
                            {/if}
                        {/block}
                    {elseif $field.type === 'birthday'}
                        {block name='form_field_item_birthday'}
                            <div class="js-parent-focus">
                              {html_select_date
                              field_order=DMY
                              time={$field.value|escape:'htmlall':'UTF-8'}
                              field_array={$field.name|escape:'htmlall':'UTF-8'}
                              prefix=false
                              reverse_years=true
                              field_separator='<br>'
                              day_extra='class="form-control form-control-select"'
                              month_extra='class="form-control form-control-select"'
                              year_extra='class="form-control form-control-select"'
                              day_empty={l s='-- day --' d='Shop.Forms.Labels'}
                              month_empty={l s='-- month --' d='Shop.Forms.Labels'}
                              year_empty={l s='-- year --' d='Shop.Forms.Labels'}
                              start_year={'Y'|date|escape:'htmlall':'UTF-8'}-100 end_year={'Y'|date|escape:'htmlall':'UTF-8'}
                              }
                            </div>
                        {/block}
                    {elseif $field.type === 'password'}
                        {block name='form_field_item_password'}
                          <div class="input-group js-parent-focus">
                                <input
                                  class="form-control js-child-focus js-visible-password"
                                  name="{$field.name|escape:'htmlall':'UTF-8'}"
                                  type="password"
                                  value=""
                                  pattern=".{literal}{{/literal}5,{literal}}{/literal}"
                                  {if $field.required}required{/if}
                                >
                                <span class="input-group-btn">
                                    <button
                                      class="btn"
                                      type="button"
                                      data-action="show-password"
                                      data-text-show="{l s='Show' d='Shop.Theme.Actions'}"
                                      data-text-hide="{l s='Hide' d='Shop.Theme.Actions'}"
                                    >
                                      {l s='Show' d='Shop.Theme.Actions'}
                                    </button>
                                </span>
                          </div>
                        {/block}
                    {else}
                      {block name='form_field_item_other'}
                          <input
                            class="form-control"
                            name="{$field.name|escape:'htmlall':'UTF-8'}"
                            type="{$field.type|escape:'htmlall':'UTF-8'}"
                            value="{$field.value|escape:'htmlall':'UTF-8'}"
                            {if isset($field.availableValues.placeholder)}placeholder="{$field.availableValues.placeholder|escape:'htmlall':'UTF-8'}"{/if}
                            {if $field.maxLength}maxlength="{$field.maxLength|intval}"{/if}
                            {if $field.required}required{/if}
                          >
                          {if isset($field.availableValues.comment)}
                              <span class="form-control-comment">
                                {$field.availableValues.comment|escape:'htmlall':'UTF-8'}
                              </span>
                          {/if}
                      {/block}
                    {/if}

                    {block name='form_field_errors'}
                      {include file='_partials/form-errors.tpl' errors=$field.errors}
                    {/block}
                </div>
            {/if}

            {if (!$field.required && !in_array($field.type, ['radio-buttons', 'checkbox']))}
                <div class="col-md-3 form-control-comment">
                    {block name='form_field_comment'}
                       {l s='Optional' d='Shop.Forms.Labels'}
                    {/block}
                </div>
            {/if}
        </div>
    </div>
{/if}
