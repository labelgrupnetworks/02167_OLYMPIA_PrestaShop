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

{assign var='fieldControlId' value=$field.availableValues.object|cat:'_'|cat:$field.name}
{foreach from=$field.availableValues item="label" key="value" name='f_options'}
    {if in_array($value, $privateAvailableKeys) eq false}
        {append var='availableOptions' value=$label index=$value}
    {/if}
{/foreach}

{assign var='dataValidation' value=$field.availableValues.validation|escape:'htmlall':'UTF-8'}
{if $field.maxLength neq 0}
    {if $dataValidation neq ''}
        {assign var='dataValidation' value=$dataValidation|cat:',length'}
    {else}
        {assign var='dataValidation' value='length'}
    {/if}
{/if}
{if ($field.name eq 'email' and (isset($isRequestConfirmationEmail) and $isRequestConfirmationEmail)) or $field.name eq 'password' and !$isCustomer}
    {if $dataValidation neq ''}
        {assign var='dataValidation' value=$dataValidation|cat:',confirmation'}
    {else}
        {assign var='dataValidation' value='confirmation'}
    {/if}
{/if}

{if $field.type neq 'hidden' and $field.type neq 'checkbox' and $field.type neq 'checkbox-cms'}
    <label for="{$field.name|escape:'htmlall':'UTF-8'}">
        {if ($field.name eq 'email' and (isset($isRequestConfirmationEmail) and $isRequestConfirmationEmail)) or $field.name eq 'password' and !$isCustomer}{l s='Confirm' mod='onepagecheckoutps'}&nbsp;{/if}{$field.label nofilter}:
        <sup>{if $field.required}*{/if}</sup>
    </label>
{/if}
{if $field.type eq 'text' or $field.type eq 'password' or $field.type eq 'email'}
    <input
        id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
        name="{$field.name|escape:'htmlall':'UTF-8'}"
        type="{$field.type|escape:'htmlall':'UTF-8'}"
        class="form-control input-sm {$field.availableValues.object|escape:'htmlall':'UTF-8'} {if $field.availableValues.capitalize}capitalize{/if} {if $field.required}required{/if}"
        value="{$field.value|escape:'htmlall':'UTF-8'}"
        data-validation="{$dataValidation|escape:'htmlall':'UTF-8'}"
        {if !$field.required}
            data-validation-optional="true"
        {/if}
        {if $field.maxLength neq 0}
            data-validation-length="max{$field.maxLength|intval}"
            maxlength="{$field.maxLength|intval}"
        {/if}
        {if isset($field.availableValues.placeholder)}
            placeholder="{$field.availableValues.placeholder|escape:'htmlall':'UTF-8'}"
        {/if}
        {if isset($field.availableValues.format)}
            data-format="{$field.availableValues.format|escape:'htmlall':'UTF-8'}"
        {/if}
        {if isset($field.availableValues.countryIsoCode)}
            data-country-iso-code="{$field.availableValues.countryIsoCode|escape:'htmlall':'UTF-8'}"
        {/if}
    />
{elseif $field.type eq 'hidden'}
    <input
        id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
        name="{$field.name|escape:'htmlall':'UTF-8'}"
        type="{$field.type|escape:'htmlall':'UTF-8'}"
        class="form-control input-sm {$field.availableValues.object|escape:'htmlall':'UTF-8'} {if $field.availableValues.capitalize}capitalize{/if} {if $field.required}required{/if}"
        {if !$field.required}
            data-validation-optional="true"
        {/if}
        {if $field.maxLength neq 0}
            data-validation-length="max{$field.maxLength|intval}"
            maxlength="{$field.maxLength|intval}"
        {/if}
        value="{$field.value|escape:'htmlall':'UTF-8'}"
    />
{elseif $field.type eq 'country'}
    <select
        id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
        name="{$field.name|escape:'htmlall':'UTF-8'}"
        class="{$field.availableValues.object|escape:'htmlall':'UTF-8'} form-control input-sm"
        data-validation="{$field.availableValues.validation|escape:'htmlall':'UTF-8'}"
        data-default-value="{$field.availableValues.defaultValue|escape:'htmlall':'UTF-8'}"
        {if $field.required and isset($availableOptions)}
            data-validation="required"
        {else}
            data-validation-optional="true"
        {/if}
    >
        <option value="" data-text="">--</option>
        {if isset($field.availableValues.availableCountries)}
            {foreach from=$field.availableValues.availableCountries item="country" key="value" name='f_countries'}
                <option
                    value="{$value|intval}"
                    data-text="{$country.name|escape:'htmlall':'UTF-8'}"
                    data-iso-code="{$country.iso_code|escape:'htmlall':'UTF-8'}"
                    {if $field.value eq $value}selected{/if}
                >
                    {$country.name|escape:'htmlall':'UTF-8'}
                </option>
            {/foreach}
        {/if}
    </select>
{elseif $field.type eq 'state'}
    <select
        id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
        name="{$field.name|escape:'htmlall':'UTF-8'}"
        class="{$field.availableValues.object|escape:'htmlall':'UTF-8'} form-control input-sm"
        data-validation="{$field.availableValues.validation|escape:'htmlall':'UTF-8'}"
        data-default-value="{$field.availableValues.defaultValue|escape:'htmlall':'UTF-8'}"
        {if $field.required and isset($availableOptions)}
            data-validation="required"
        {else}
            data-validation-optional="true"
        {/if}
    >
        <option value="" data-text="">--</option>
        {if isset($field.availableValues.availableStates)}
            {foreach from=$field.availableValues.availableStates item="state" name='f_satates'}
                <option
                    value="{$state.id_state|intval}"
                    data-text="{$state.name|escape:'htmlall':'UTF-8'}"
                    data-iso-code="{$state.iso_code|escape:'htmlall':'UTF-8'}"
                    {if $field.value eq $state.id_state}selected{/if}
                >
                    {$state.name|escape:'htmlall':'UTF-8'}
                </option>
            {/foreach}
        {/if}
    </select>
{elseif $field.type eq 'select'}
    <select
        id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
        name="{$field.name|escape:'htmlall':'UTF-8'}"
        class="{$field.availableValues.object|escape:'htmlall':'UTF-8'} form-control input-sm"
        data-validation="{$field.availableValues.validation|escape:'htmlall':'UTF-8'}"
        data-default-value="{$field.availableValues.defaultValue|escape:'htmlall':'UTF-8'}"
        {if $field.required and isset($availableOptions)}
            data-validation="required"
        {else}
            data-validation-optional="true"
        {/if}
    >
        <option value="" data-text="">--</option>
        {if isset($availableOptions)}
            {foreach from=$availableOptions item="label" key="value" name='f_options'}
                <option
                    value="{$value|escape:'htmlall':'UTF-8'}"
                    data-text="{$label|escape:'htmlall':'UTF-8'}"
                    {if $field.value eq $value}selected{/if}
                >
                    {$label|escape:'htmlall':'UTF-8'}
                </option>
            {/foreach}
        {/if}
    </select>
{elseif $field.type eq 'checkbox'}
    <label for="{$fieldControlId|escape:'htmlall':'UTF-8'}">
        <input
            id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
            name="{$field.name|escape:'htmlall':'UTF-8'}"
            type="checkbox"
            class="{$field.availableValues.object|escape:'htmlall':'UTF-8'}"
            {if $field.value}
                checked
            {/if}
            {if !$field.required}
                data-validation-optional="true"
            {else}
                data-validation="required"
            {/if}
        />
        {$field.label nofilter}
        <sup>{if $field.required}*{/if}</sup>
    </label>
{elseif $field.type eq 'checkbox-cms'}
    <label for="{$fieldControlId|escape:'htmlall':'UTF-8'}" class="cms-modal">
        <input
            id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
            name="{$field.name|escape:'htmlall':'UTF-8'}"
            type="checkbox"
            class="{$field.availableValues.object|escape:'htmlall':'UTF-8'}"
            {if $field.value}
                checked
            {/if}
            {if !$field.required}
                data-validation-optional="true"
            {else}
                data-validation="required"
            {/if}
        />
        {$field.label nofilter}
        <sup>{if $field.required}*{/if}</sup>
    </label>
{elseif $field.type eq 'radio-buttons'}
    {if isset($availableOptions)}
        <div class="row">
            {foreach from=$availableOptions item="label" key="value" name='f_options'}
                {math assign='num_col_option' equation='12/a' a=$smarty.foreach.f_options.total}

                <div class="col-xs-{$num_col_option|intval} col-{$num_col_option|intval}">
                    <label for="{$fieldControlId|intval}_{$value|escape:'htmlall':'UTF-8'}">
                        <input
                            id="{$fieldControlId|intval}_{$value|escape:'htmlall':'UTF-8'}"
                            name="{$field.name|escape:'htmlall':'UTF-8'}"
                            type="radio"
                            class="{$field.availableValues.object|escape:'htmlall':'UTF-8'}"
                            value="{$value|escape:'htmlall':'UTF-8'}"
                            {if $field.value eq $value} checked {/if}
                            {if !$field.required}
                                data-validation-optional="true"
                            {else}
                                data-validation="required"
                            {/if}
                        />
                        {$label|escape:'htmlall':'UTF-8'}
                    </label>
                </div>
            {/foreach}
        </div>
    {/if}
{elseif $field.type eq 'textarea'}
    <textarea
        id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
        name="{$field.name|escape:'htmlall':'UTF-8'}"
        class="{$field.availableValues.object|escape:'htmlall':'UTF-8'} form-control"
        data-validation="{$field.availableValues.validation|escape:'htmlall':'UTF-8'}{if $field.maxLength neq 0},length{/if}"
        {if !$field.required}
            data-validation-optional="true"
        {/if}
        {if $field.maxLength neq 0}
            data-validation-length="max{$field.maxLength|intval}"
            maxlength="{$field.maxLength|intval}"
        {/if}
        {if isset($field.availableValues.placeholder)}
            placeholder="{$field.availableValues.placeholder|escape:'htmlall':'UTF-8'}"
        {/if}
    >{$field.value|escape:'htmlall':'UTF-8'}</textarea>
{elseif $field.type eq 'file'}
    <input
        id="{$fieldControlId|escape:'htmlall':'UTF-8'}"
        name="{$field.name|escape:'htmlall':'UTF-8'}"
        type="{$field.type|escape:'htmlall':'UTF-8'}"
        class="form-control input-sm {$field.availableValues.object|escape:'htmlall':'UTF-8'} {if $field.required}required{/if}"
        value="{$field.value|escape:'htmlall':'UTF-8'}"
        {if !$field.required}
            data-validation-optional="true"
        {/if}
    />
{/if}
{if isset($field.availableValues.comment)}
    <span class="form-control-comment">
        <em>{$field.availableValues.comment|escape:'htmlall':'UTF-8'}</em>
    </span>
{/if}