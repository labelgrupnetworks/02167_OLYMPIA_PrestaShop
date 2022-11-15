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

{foreach from=$formFields key="key_field" item='field_row'}
    {if $key_field neq 'hidden'}
        <div class="row">
    {/if}

    {foreach from=$field_row item='field' name='for_fields'}
        {math assign='numCol' equation='12/a' a=$smarty.foreach.for_fields.total}

        {if $key_field neq 'hidden'}
            <div
                id="field_{$field.availableValues.object|escape:'htmlall':'UTF-8'}_{$field.name|escape:'htmlall':'UTF-8'}"
                class="form-group col-xs-{$numCol|escape:'htmlall':'UTF-8'} col-{$numCol|escape:'htmlall':'UTF-8'} {if $field.required}required{/if} {if $smarty.foreach.for_fields.total == 1}clear clearfix{/if}"
            >
        {/if}

        {include file='module:onepagecheckoutps/views/templates/front/checkout/_partials/form-fields.tpl' field=$field}

        {if $key_field neq 'hidden'}
            </div>
        {/if}
    {/foreach}

    {if $key_field neq 'hidden'}
        </div>
    {/if}
{/foreach}