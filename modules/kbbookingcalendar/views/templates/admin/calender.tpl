<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
{*<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />*}
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
<script>
    var path_fold = "{$kb_admin_link}";  {*Variable contains link, escape not required*}
 var kb_cal_data = '{json_encode($calender_data)|addslashes nofilter}{*escape not required as contains html*}';
            var kb_calender_data = JSON.parse(kb_cal_data);
            var initialLocaleCode = '{$lang_iso}';
            var kb_current_date = '{$current_date}';
            var kb_start_date = '{$kb_start_date}';
            var kb_end_date = '{$kb_end_date}';
            var kb_basicWeek = '{l s='basicWeek' mod='kbbookingcalendar'}';
            var kb_basicDay = '{l s='basicDay' mod='kbbookingcalendar'}';
            var kb_agendaWeek = '{l s='agendaWeek' mod='kbbookingcalendar'}';
            var kb_agendaDay = '{l s='agendaDay' mod='kbbookingcalendar'}';
</script>
<style>

    #calendar {
        {*max-width: 900px;*}
        margin: 0 auto;
    }
    .fc-time,.fc-title{
        color: white;
    }
    .fc-event-container a{
        font-size: 12px !important;
    }
    .fc-event-container a:hover{
{*        position: absolute;*}
        z-index: 9999999999999 !important;
        background-color: red !important;
        border-color: red !important;
    }

    #kb_calender_filter_form .form-group{
        display: inline-grid;
    }
</style>
<div>
    <div class='panel kpi-container'>
        <div class='row'>
            <form class="form-inline" method="post" id="kb_calender_filter_form">
                <div class="form-group">
                    <label for="product_id">{l s='Select Product' mod='kbbookingcalendar'}</label>
                     <select name="kb_product_id" id="kb_product_id">
   <option value="0">{l s='Select Product Name' mod='kbbookingcalendar'}</option>
                                                        {foreach from=$kb_available_booking_pros item='pro'}
                                                            <option value="{$pro['id_product']}" {if isset($kb_product_id) && $kb_product_id eq $pro['id_product']} selected {/if}  >{$pro['name']}</option>

                                                        {/foreach}
  </select>
                </div>
                <div class="form-group">
                    <label for="product_type">{l s='Select Product Type' mod='kbbookingcalendar'}</label>
  <select name="kb_product_type" id="kb_product_type">
   <option value="0">{l s='Select Product Type' mod='kbbookingcalendar'}</option>
                                                        {foreach from=$kb_available_booking_type item='type'}
                                                            <option value="{$type['product_type']}" {if isset($kb_product_type) && $kb_product_type eq $type['product_type']} selected {/if} >{$kb_product_type_arr[$type['product_type']]}</option>

                                                        {/foreach}
  </select>
                </div>
                                <div class="form-group">
                    <label>{l s='Select Start Order Date' mod='kbbookingcalendar'}</label>
                    <div id="datepicker" class="input-group date" data-date-format="yyyy-mm-dd" style="width: 65%;">
                                                            <input class="required_entry " type="text" name="kb_start_date" value="" readonly />
                                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                        </div>
                </div>
                    <div class="form-group">
                    <label>{l s='Select End Order Date' mod='kbbookingcalendar'}</label>
                    <div id="datepicker1" class="input-group date" data-date-format="yyyy-mm-dd" style="width: 65%;">
                                                            <input class="required_entry " type="text" name="kb_end_date" value="" readonly />
                                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                                        </div>
                </div>
                <button type="submit" class="btn btn-info btn-lg">{l s='Filter' mod='kbbookingcalendar'}</button>
                <button class="btn btn-default btn-lg" onclick="calenderFilterReset()">{l s='Reset' mod='kbbookingcalendar'}
                    </button>
            </form>
        </div>
    </div>

    <br>
    <div id="calendar" class="panel kpi-container"></div>
</div>
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
    * @copyright 2017 Knowband
    * @license   see file: LICENSE.txt
    *
    * Description
    *
    * Admin tpl file
    *}
