{*
* Do not edit the file if you want to upgrade the module in future.
*
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2019 Globo JSC
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/
*}

<div class="dashboard_box">
    <div class="top_link">
        <ul class="toplink_ul">
            <li class="col-lg-2"><a href="{$setting_link|escape:'html':'UTF-8'}" title=""><img src="../modules/gformbuilderpro/views/img/icon/setting_ico.png" class="gdashboard_icon gform_icon_setting"></img><span>{l s='General setting' mod='gformbuilderpro'}</span></a></li>
            <li class="col-lg-2"><a href="{$formmanager_link|escape:'html':'UTF-8'}" title=""><img src="../modules/gformbuilderpro/views/img/icon/forms_ico.png" class="gdashboard_icon gform_icon_forms"></img><span>{l s='Forms' mod='gformbuilderpro'}</span></a></li>
            <li class="col-lg-2"><a href="{$request_link|escape:'html':'UTF-8'}" title=""><img src="../modules/gformbuilderpro/views/img/icon/received_data.png" class="gdashboard_icon gform_icon_received"></img><span>{l s='Received data' mod='gformbuilderpro'}</span></a></li>
            <li class="col-lg-2"><a href="{$csv_link|escape:'html':'UTF-8'}" title=""><img src="../modules/gformbuilderpro/views/img/icon/csv_export.png" class="gdashboard_icon gform_icon_csv_export"></img><span>{l s='CSV Export' mod='gformbuilderpro'}</span></a></li>
            <li class="col-lg-2"><a href="{$formmanager_link|escape:'html':'UTF-8'}#gimport_form" title=""><img src="../modules/gformbuilderpro/views/img/icon/import_export.png" class="gdashboard_icon gform_icon_import_forms"></img><span>{l s='Import/Export Forms' mod='gformbuilderpro'}</span></a></li>
            <li class="col-lg-2"><a href="{$analytics_link|escape:'html':'UTF-8'}" title=""><img src="../modules/gformbuilderpro/views/img/icon/analytics_ico.png" class="gdashboard_icon gform_icon_analytics"></img><span>{l s='Analytics' mod='gformbuilderpro'}</span></a></li>
        </ul>
    </div>
    <div class="clear"></div>
    <div class="col-lg-3">
        <div class="publish_box">
            <div class="box-heading"><i class="icon icon-dashboard"></i> {l s='Activity overview' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
                <ul class="gform_overview">
                    <li><a href="{$formmanager_link|escape:'html':'UTF-8'}"><span>{l s='Forms' mod='gformbuilderpro'}</span> <span class="gform_overview_total">{$totalform|intval}</span></a></li>
                    <li><a href="{$request_link|escape:'html':'UTF-8'}"><span>{l s='Received data' mod='gformbuilderpro'}</span> <span class="gform_overview_total">{$totalsubmited|intval}</span></a></li>
                    <li><a href="{$request_link|escape:'html':'UTF-8'}"><span>{l s='Replied ' mod='gformbuilderpro'}</span> <span class="gform_overview_total">{$totalreply|intval}</span></a></li>
                    <li><a href="{$request_link|escape:'html':'UTF-8'}"><span>{l s='Unread' mod='gformbuilderpro'}</span> <span class="gform_overview_total">{$totalunread|intval}</span></a></li>
                    <li><a href="{$request_link|escape:'html':'UTF-8'}"><span>{l s='Starred' mod='gformbuilderpro'}</span> <span class="gform_overview_total">{$totalstar|intval}</span></a></li>
                    <li><a href="{$setting_link|escape:'html':'UTF-8'}"><span>{l s='Banned Ip' mod='gformbuilderpro'}</span> <span class="gform_overview_total">{$totalbanip|intval}</span></a></li>
                </ul>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    {if !isset($isps15) || $isps15 != '-1'}
    <div class="col-lg-6">
        <div class="publish_box">
            <div class="box-heading"><i class="icon icon-area-chart"></i> {l s='Analytics' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
                <div id="mainchart2" class="gchart_box"><svg></svg></div>
                <a href="{$analytics_link|escape:'html':'UTF-8'}" class="btn btn-default pull-right">{l s='View more' mod='gformbuilderpro'}</a>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    {/if}
    <div class="col-lg-3">
        <div class="publish_box">
            <div class="box-heading"><i class="icon icon-bell"></i> {l s='Last submitted' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
                {if $lastestsubmitted}
                    <ul class="lastestsubmitted_ul">
                        {foreach $lastestsubmitted as $submitted}
                            <li>
                                <a href="{$request_link|escape:'html':'UTF-8'}&id_gformrequest={$submitted.id_gformrequest|intval}&viewgformrequest">
                                    <span class="submit_title">{if $submitted.star}<i class="icon-star"></i>{/if}{$submitted.subject|truncate:80:'...'|escape:'html':'UTF-8'}</span>
                                    <span class="submitted_date">{$submitted.date_add|escape:'html':'UTF-8'}</span>
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                    <a href="{$request_link|escape:'html':'UTF-8'}" class="btn btn-default pull-right">{l s='View more' mod='gformbuilderpro'}</a>
                    {/if}
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="col-lg-12">
        <div class="publish_box tutorials_box">
            <div class="box-heading"><i class="icon icon-group"></i> {l s='Tutorials fors biginner' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
                <div class="col-lg-4">
                    <div class="publish_box">
                        <div class="box-heading subbox-heading ">{l s='Get started' mod='gformbuilderpro'}</div>
                        <div class="gbox_content">
                            <ul>
                                <li><p><a href="{$setting_link|escape:'html':'UTF-8'}" title="">{l s='General settings' mod='gformbuilderpro'}</a></p>
                                    <ul class="gsub_ul">
                                        <li><p>{l s='Schema of URLs, Meta keyword...' mod='gformbuilderpro'}</p></li>
                                        <li><p>{l s='reCAPTCHA, Google map Api ' mod='gformbuilderpro'}</p></li>
                                    </ul>
                                </li>
                                <li>
                                    <p><a href="{$formmanager_link|escape:'html':'UTF-8'}">{l s='Create New Form' mod='gformbuilderpro'}</a></p>
                                    <ul class="gsub_ul">
                                        <li><p>{l s='Config General Information' mod='gformbuilderpro'}</p></li>
                                        <li><p>{l s='Add Element, Drag & Drop components ...' mod='gformbuilderpro'}</p></li>
                                        <li><p>{l s='Config Email, Thank you page' mod='gformbuilderpro'}</p></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="publish_box">
                        <div class="box-heading subbox-heading ">{l s='Next step' mod='gformbuilderpro'}</div>
                        <div class="gbox_content">
                            <ul>
                                <li><p>{l s='Publish form' mod='gformbuilderpro'}</p></li>
                                <li><p><a href="{$request_link|escape:'html':'UTF-8'}" title="">{l s='Check received data' mod='gformbuilderpro'}</a></p></li>
                                <li><p>{l s='Reply' mod='gformbuilderpro'}</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="publish_box">
                        <div class="box-heading subbox-heading ">{l s='More action' mod='gformbuilderpro'}</div>
                        <div class="gbox_content">
                            <ul>
                                <li><p>{l s='Import / Export Forms' mod='gformbuilderpro'}</p></li>
                                <li><p><a href="{$csv_link|escape:'html':'UTF-8'}" title="">{l s='Export received data to CSV' mod='gformbuilderpro'}</a></p></li>
                                <li><p><a href="{$analytics_link|escape:'html':'UTF-8'}" title="">{l s='Analytics' mod='gformbuilderpro'}</a></p></li>
                                <li><p><a href="{$setting_link|escape:'html':'UTF-8'}">{l s='Ban Ip' mod='gformbuilderpro'}</a></p></li>
                                <li><p>{l s='...' mod='gformbuilderpro'}</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>