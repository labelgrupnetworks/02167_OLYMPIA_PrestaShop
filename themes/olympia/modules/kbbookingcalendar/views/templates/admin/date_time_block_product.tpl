{if $id_booking_product}

    {if !isset($kbajaxcounter)}
        <div class="kb-date-time-block" >
            <div class="form-group">
                <label class="control-label col-lg-1">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enter date/time' mod='kbbookingcalendar'}">
                        {l s='Enter date/time slot' mod='kbbookingcalendar'}
                    </span>
                </label>
                <div class="col-lg-11">
                {/if}
                {if isset($kb_date_data)}
                    {assign var=kb_counter value='1'}
                    {foreach $kb_date_data as $db_data}
                        <div class="kb-datetime-row" id="kb-datetime-block-row_{$kb_counter}" style="clear:both;">
                            <div class="table-danger col-lg-6 kb_booking_dates_range">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="center">{l s='From' mod='kbbookingcalendar'}</th>
                                            <th class="center">{l s='To' mod='kbbookingcalendar'}</th>
                                            <th class="center">{l s='Day of the week' mod='kbbookingcalendar'}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="center">
                                                <div class="input-group">
                                                    <input autocomplete="off" class="form-control kb_date_from" type="text" name="kb_date_from[{$kb_counter}]" value="{$db_data['from_date']}" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="icon-calendar"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="center">
                                                <div class="input-group">
                                                    <input autocomplete="off" class="form-control kb_end_date" type="text" name="kb_date_to[{$kb_counter}]" value="{$db_data['to_date']}" readonly>
                                                    <span class="input-group-addon">
                                                        <i class="icon-calendar"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="center">
                                                <div class="input-group">
                                                    <select class="form-control kb_weekday" multiple name="kb_weekday[{$kb_counter}]" >
                                                        <option value="lunes">
                                                            {l s="Monday" mod="kbbookingcalendar"}
                                                        </option>
                                                        <option value="martes">
                                                            {l s="Tuesday" mod="kbbookingcalendar"}
                                                        </option>
                                                        <option value="miercoles">
                                                            {l s="Wednesday" mod="kbbookingcalendar"}
                                                        </option>
                                                        <option value="jueves">
                                                            {l s="Thursday" mod="kbbookingcalendar"}
                                                        </option>
                                                        <option value="viernes">
                                                            {l s="Friday" mod="kbbookingcalendar"}
                                                        </option>
                                                        <option value="sabado">
                                                            {l s="Saturday" mod="kbbookingcalendar"}
                                                        </option>
                                                        <option value="domingo">
                                                            {l s="Sunday" mod="kbbookingcalendar"}
                                                        </option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-danger col-lg-6 kb_booking_time_range">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="center kb_time_from_th">{l s='Time From' mod='kbbookingcalendar'}</th>
                                            <th class="center kb_time_to_th">{l s='Time To' mod='kbbookingcalendar'}</th>
                                            <th class="center kb_time_price_th">{l s='Price' mod='kbbookingcalendar'}</th>
                                            <th class="center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {if isset($db_data['time'])}
                                            {assign var=kb_time_counter value='1'}
                                            {foreach $db_data['time'] as $db_time}
                                                <tr class="kb-time-tr">
                                                    <td class="center kb_time_from_td">
                                                        <div class="input-group">
                                                            <input autocomplete="off" class="form-control kb_time_from" type="text" name="kb_time_from[{$kb_counter}][{$kb_time_counter }]" value="{$db_time['from_time']}" readonly>
                                                            <span class="input-group-addon">
                                                                <i class="icon-clock-o"></i>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="center kb_time_to_td">
                                                        <div class="input-group">
                                                            <input autocomplete="off" class="form-control kb_time_to" type="text" name="kb_time_to[{$kb_counter}][{$kb_time_counter }]" value="{$db_time['to_time']}" readonly>
                                                            <span class="input-group-addon">
                                                                <i class="icon-clock-o"></i>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="center kb_time_price_td">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                {$currency->sign}
                                                            </span>
                                                            <input class="form-control kb_time_price" type="text" name="kb_time_price[{$kb_counter}][{$kb_time_counter }]" value="{$db_time['price']}">
                                                            <span class="input-group-addon">
                                                                {if !empty($product_type)}
                                                                    {if $product_type == 'hourly_rental'}
                                                                        {l s='/hrs' mod='kbbookingcalendar'}
                                                                    {else}
                                                                        {l s='/day' mod='kbbookingcalendar'}
                                                                    {/if}
                                                                {else}
                                                                    {l s='/day' mod='kbbookingcalendar'}
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="center">
                                                        <div>
                                                            <a href="javascript:void(0);" onclick="removeTimeRow(this)" class="kb-time-slot-row-remove" style="color:#ff0000;"><i class="icon-remove"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                {$kb_time_counter = $kb_time_counter + 1}
                                            {/foreach}
                                        {elseif !empty($db_data)}
                                            {assign var=kb_time_counter value='1'}
                                            <tr class="kb-time-tr">
                                                <td class="center kb_time_from_td">
                                                    <div class="input-group">
                                                        <input autocomplete="off" class="form-control kb_time_from" type="text" name="kb_time_from[{$kb_counter}][{$kb_time_counter }]" value readonly>
                                                        <span class="input-group-addon">
                                                            <i class="icon-clock-o"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="center kb_time_to_td">
                                                    <div class="input-group">
                                                        <input autocomplete="off" class="form-control kb_time_to" type="text" name="kb_time_to[{$kb_counter}][{$kb_time_counter }]" value readonly>
                                                        <span class="input-group-addon">
                                                            <i class="icon-clock-o"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="center kb_time_price_td">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            {$currency->sign}
                                                        </span>
                                                        <input class="form-control kb_time_price" type="text" name="kb_time_price[{$kb_counter}][{$kb_time_counter }]" value="{$db_data['price']}">
                                                        <span class="input-group-addon">
                                                            {if !empty($product_type)}
                                                                {if $product_type == 'hourly_rental'}
                                                                    {l s='/hrs' mod='kbbookingcalendar'}
                                                                {else}
                                                                    {l s='/day' mod='kbbookingcalendar'}
                                                                {/if}
                                                            {else}
                                                                {l s='/day' mod='kbbookingcalendar'}
                                                            {/if}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="center">
                                                    <div>
                                                        <a href="javascript:void(0);" onclick="removeTimeRow(this)" class="kb-time-slot-row-remove" style="color:#ff0000;"><i class="icon-remove"></i></a>
                                                    </div>
                                                </td>
                                            </tr>

                                        {else}
                                            <tr class="kb-time-tr">
                                                <td class="center kb_time_from_td">
                                                    <div class="input-group">
                                                        <input autocomplete="off" class="form-control kb_time_from" type="text" name="kb_time_from[{$kb_counter}][1]" value readonly>
                                                        <span class="input-group-addon">
                                                            <i class="icon-clock-o"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="center kb_time_to_td">
                                                    <div class="input-group">
                                                        <input autocomplete="off" class="form-control kb_time_to" type="text" name="kb_time_to[{$kb_counter}][1]" value readonly>
                                                        <span class="input-group-addon">
                                                            <i class="icon-clock-o"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="center kb_time_price_td">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            {$currency->sign}
                                                        </span>
                                                        <input class="form-control kb_time_price" type="text" name="kb_time_price[{$kb_counter}][1]" value>
                                                        <span class="input-group-addon">
                                                            {if !empty($product_type)}
                                                                {if $product_type == 'hourly_rental'}
                                                                    {l s='/hrs' mod='kbbookingcalendar'}
                                                                {else}
                                                                    {l s='/day' mod='kbbookingcalendar'}
                                                                {/if}
                                                            {else}
                                                                {l s='/day' mod='kbbookingcalendar'}
                                                            {/if}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="center">
                                                    <div>
                                                        <a href="javascript:void(0);" onclick="removeTimeRow(this)" class="kb-time-slot-row-remove" style="color:#ff0000;"><i class="icon-remove"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        {/if}
                                    </tbody>
                                </table>
                                <button class="btn btn-warning" onclick="addTimeSlotRow(this);" name="addkbTimeField">{l s='Add Slots' mod='kbbookingcalendar'}</button>
                            </div>

                        </div>
                        {$kb_counter = $kb_counter + 1}
                    {/foreach}
                {else}
                    {if !isset($is_time_ajax)}
                        <div class="kb-datetime-row" id="kb-datetime-block-row_{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}" style="clear:both;">
                            <div class="table-danger col-lg-6 kb_booking_dates_range">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="center">{l s='From' mod='kbbookingcalendar'}</th>
                                            <th class="center">{l s='To' mod='kbbookingcalendar'}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="center">
                                                <div class="input-group">
                                                    <input autocomplete="off" class="form-control kb_date_from" type="text" name="kb_date_from[{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}]" value readonly>
                                                    <span class="input-group-addon">
                                                        <i class="icon-calendar"></i>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="center">
                                                <div class="input-group">
                                                    <input autocomplete="off" class="form-control kb_end_date" type="text" name="kb_date_to[{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}]" value readonly>
                                                    <span class="input-group-addon">
                                                        <i class="icon-calendar"></i>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-danger col-lg-6 kb_booking_time_range">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="center kb_time_from_th">{l s='Time From' mod='kbbookingcalendar'}</th>
                                            <th class="center kb_time_to_th">{l s='Time To' mod='kbbookingcalendar'}</th>
                                            <th class="center kb_time_price_th">{l s='Price' mod='kbbookingcalendar'}</th>
                                            <th class="center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    {/if}
                                    <tr class="kb-time-tr">
                                        <td class="center kb_time_from_td">
                                            <div class="input-group">
                                                <input autocomplete="off" class="form-control kb_time_from" type="text" name="kb_time_from{if isset($is_time_ajax)}[{if isset($datetime_block) && !empty($datetime_block)}{$datetime_block}{else}1{/if}][{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}]{else}[{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}][1]{/if}" value readonly>
                                                <span class="input-group-addon">
                                                    <i class="icon-clock-o"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="center kb_time_to_td">
                                            <div class="input-group">
                                                <input autocomplete="off" class="form-control kb_time_to" type="text" name="kb_time_to{if isset($is_time_ajax)}[{if isset($datetime_block) && !empty($datetime_block)}{$datetime_block}{else}1{/if}][{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}]{else}[{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}][1]{/if}" value readonly>
                                                <span class="input-group-addon">
                                                    <i class="icon-clock-o"></i>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="center kb_time_price_td">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    {$currency->sign}
                                                </span>
                                                <input class="form-control kb_time_price" type="text" name="kb_time_price{if isset($is_time_ajax)}[{if isset($datetime_block) && !empty($datetime_block)}{$datetime_block}{else}1{/if}][{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}]{else}[{if isset($kbajaxcounter)}{$kbajaxcounter}{else}1{/if}][1]{/if}" value="">
                                                <span class="input-group-addon">
                                                    {if !empty($product_type)}
                                                        {if $product_type == 'hourly_rental'}
                                                            {l s='/hrs' mod='kbbookingcalendar'}
                                                        {else}
                                                            {l s='/day' mod='kbbookingcalendar'}
                                                        {/if}
                                                    {else}
                                                        {l s='/day' mod='kbbookingcalendar'}
                                                    {/if}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <div>
                                                <a href="javascript:void(0);" onclick="removeTimeRow(this)" class="kb-time-slot-row-remove" style="color:#ff0000;"><i class="icon-remove"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    {if !isset($is_time_ajax)}
                                    </tbody>
                                </table>
                                <button class="btn btn-warning" onclick="addTimeSlotRow(this);" name="addkbTimeField">{l s='Add Slots' mod='kbbookingcalendar'}</button>
                            </div>
                        </div>

                    {/if}
                {/if}
                {if !isset($kbajaxcounter)}
                    <div style="clear:both;">
                        <button class="btn btn-success" name="addkbDateTimeField">{l s='Add More' mod='kbbookingcalendar'}</button>
                    </div>

                    <div>
                        <p class="help-block" style="margin-top: 24px;">{l s='If there will be any conflits between date/time range, then first occurence will be applied.' mod='kbbookingcalendar'}</p>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{else}
    <div class="alert alert-warning kb-date-time-warning-alert">
        {l s='You must save this product before adding date/time.' mod='kbbookingcalendar'}
    </div>
{/if}
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2019 Knowband
* @license   see file: LICENSE.txt
*
*}