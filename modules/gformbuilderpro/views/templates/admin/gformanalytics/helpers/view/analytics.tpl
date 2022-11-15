{*
* Do not edit the file if you want to upgrade the module in future.
*
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2019 Globo JSC
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/
*}

<div class="publish_box">
    {if !isset($isps15) || $isps15 != '-1'}
    <div class="box-heading"><i class="icon icon-area-chart"></i> {l s='Analytics' mod='gformbuilderpro'}</div>
    <div class="gbox_content">
        <div class="col-lg-9">
            <div class="gchoose_time_report_wp">
                {if isset($calendar)}
                <form action="" method="post" id="calendar_form" name="calendar_form" class="form-inline gcalendar_chart_form">
                    <div class="btn-group">
                        {if isset($gforms) &&  $gforms}
                        <select name="id_gformbuilderpro" class="select_form_calender">
                            <option value="" {if $id_gformbuilderpro == 0 || $id_gformbuilderpro == ''} selected="selected"{/if}>{l s='All' mod='gformbuilderpro'}</option>
                            {foreach $gforms as $form}
                                 <option value="{$form.id_gformbuilderpro|intval}" {if $id_gformbuilderpro == $form.id_gformbuilderpro} selected="selected"{/if}>
                                     {$form.title|escape:'html':'UTF-8':'html'}
                                 </option>
                            {/foreach}
                        </select>
                        {/if}
                        <button type="button" name="submitDateMonth" class="btn btn-default submitDateMonth{if (!isset($fields_value['preselect_date_range']) || !$fields_value['preselect_date_range']) || (isset($fields_value['preselect_date_range']) && $fields_value['preselect_date_range'] == 'month')} active{/if}">
                            {l s='Month' mod='gformbuilderpro'}
                        </button>
                        <button type="button" name="submitDateYear" class="btn btn-default submitDateYear{if isset($fields_value['preselect_date_range']) && $fields_value['preselect_date_range'] == 'year'} active{/if}">
                            {l s='Year' mod='gformbuilderpro'}
                        </button>
                        <button type="button" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev{if isset($fields_value['preselect_date_range']) && $fields_value['preselect_date_range'] == 'prev-month'} active{/if}">
                            {l s='Month' mod='gformbuilderpro'}-1
                        </button>
                        <button type="button" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev{if isset($fields_value['preselect_date_range']) && $fields_value['preselect_date_range'] == 'prev-year'} active{/if}">
                            {l s='Year' mod='gformbuilderpro'}-1
                        </button>
                    </div>
                    <input type="hidden" name="datepickerFrom" id="datepickerFrom" value="{if isset($fields_value['date_from'])}{$fields_value['date_from']|escape:'html':'UTF-8'}{/if}" class="form-control">
                    <input type="hidden" name="datepickerTo" id="datepickerTo" value="{if isset($fields_value['date_to'])}{$fields_value['date_to']|escape:'html':'UTF-8'}{/if}" class="form-control">
                    <input type="hidden" name="preselectDateRange" id="preselectDateRange" value="{if isset($fields_value['preselect_date_range'])}{$fields_value['preselect_date_range']|escape:'html':'UTF-8':'html'}{/if}" class="form-control">
                    <div class="form-group pull-right">
                        <button id="datepickerExpand" class="btn btn-default" type="button">
                            <i class="icon-calendar-empty"></i>
                            <span class="hidden-xs">
							{l s='From' mod='gformbuilderpro'}
							<strong class="text-info" id="datepicker-from-info">{if isset($fields_value['date_from'])}{$fields_value['date_from']|escape:'html':'UTF-8'}{/if}</strong>
							{l s='To' mod='gformbuilderpro'}
							<strong class="text-info" id="datepicker-to-info">{if isset($fields_value['date_to'])}{$fields_value['date_to']|escape:'html':'UTF-8'}{/if}</strong>
							<strong class="text-info" id="datepicker-diff-info"></strong>
						</span>
                            <i class="icon-caret-down"></i>
                        </button>
                    </div>
                    {if isset($isps17) && $isps17}
                        {$calendar nofilter} {* Build by HelperCalendar, no need to escape *}
                    {else}
                        {$calendar} {* Build by HelperCalendar, no need to escape *}
                    {/if}
                </form><div class="clear"></div>
                {/if}
            </div>
            <div class="publish_box">
                <div class="gbox_content">
                    <div id="mainchart2" class="gchart_box"><svg></svg></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 gchart_box_wp">
            <div class="publish_box">
                <div class="box-heading"><i class="icon icon-area-chart"></i> {l s='Browser' mod='gformbuilderpro'}</div>
                <div class="gbox_content">
                    <div id="browser_chart" class="gchart_box"><svg></svg></div>
                </div>
            </div>
            <div class="publish_box">
                <div class="box-heading"><i class="icon icon-area-chart"></i> {l s='Platform' mod='gformbuilderpro'}</div>
                <div class="gbox_content">
                    <div id="platform_chart" class="gchart_box"><svg></svg></div>
                </div>
            </div>
        </div>
    </div>
    {/if}
    <div class="publish_box analytics_data_history">
        <div class="box-heading"><i class="icon icon-area-chart"></i> {l s='View History' mod='gformbuilderpro'}</div>
        <div class="gbox_content">
            <table class="table analytics_datas_table">
                <thead>
                    <tr>
                        <th>{l s='Form' mod='gformbuilderpro'}</th>
                        <th>{l s='Customer' mod='gformbuilderpro'}</th>
                        <th>{l s='Ip' mod='gformbuilderpro'}</th>
                        <th>{l s='Platform' mod='gformbuilderpro'}</th>
                        <th>{l s='Browser' mod='gformbuilderpro'}</th>
                        <th>{l s='Version' mod='gformbuilderpro'}</th>
                        <th>{l s='User agent' mod='gformbuilderpro'}</th>
                        <th>{l s='Date' mod='gformbuilderpro'}</th>
                        <th>{l s='Action' mod='gformbuilderpro'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if isset($analytics_datas) && $analytics_datas}
                        {foreach $analytics_datas as $data}
                            <tr>
                                <td>{$data.title|escape:'html':'UTF-8'}</td>
                                <td>{if isset($data.id_customer) && $data.id_customer > 0}<a href="{$customer_link|escape:'html':'UTF-8'}{$data.id_customer|intval}" target="_blank"> {$data.firstname|escape:'html':'UTF-8'} {$data.lastname|escape:'html':'UTF-8'}</a>{else}--{/if}</td>
                                <td><a href="//whatismyipaddress.com/ip/{$data.ip_address|escape:'html':'UTF-8'}" target="_blank" title="{l s='Click to view location' mod='gformbuilderpro'}">{$data.ip_address|escape:'html':'UTF-8'}</a></td>
                                <td>{$data.platform|escape:'html':'UTF-8'}</td>
                                <td>{$data.browser|escape:'html':'UTF-8'}</td>
                                <td>{$data.browser_version|escape:'html':'UTF-8'}</td>
                                <td><div class="user_agent_box">{$data.user_agent|escape:'html':'UTF-8'}</div></td>
                                <td>{$data.date_add|escape:'html':'UTF-8'}</td>
                                <td>
                                    <a href="" data-shop="{$data.id_shop|intval}" rel="{$data.ip_address|escape:'html':'UTF-8'}" class="block_this_ip btn btn-default {if isset($data.banned) && $data.banned == 1} gbanned {/if}">
                                        <span class="gbantitle">{l s='Ban Ip' mod='gformbuilderpro'}</span>
                                        <span class="gunbantitle">{l s='UnBan Ip' mod='gformbuilderpro'}</span>
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                    {/if}
                </tbody>
            </table>
            <div class="gpagination_wp">
                <div class="gpagination pull-left">
                    {if isset($isps17) && $isps17}
                        {$pagination nofilter} {* $pagination is html content, no need to escape *}
                    {else}
                        {$pagination} {* $pagination is html content, no need to escape *}
                    {/if}
                </div>
                <a class="gremove_analytics_datas btn btn-default pull-right" href="">{l s='Remove history' mod='gformbuilderpro'}</a>
            </div>
        </div>
    </div>
</div>