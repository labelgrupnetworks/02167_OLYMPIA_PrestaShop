{**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL
* @license   INNOVADELUXE
*}

{* tabs version *}
<div class="col-lg-2">
    <div class='panel heading panladjust'>
        <div>
            <div class="inline-block col-lg-4 imgtab">

                <img class="img-responsive" src="{$module_dir|escape:'htmlall':'UTF-8'}logo.png">
            </div>
            <div class="col-lg-8 modtitle">
                <h4>
                    {$module_name|escape:'htmlall':'UTF-8'}
                </h4>
            </div>
        </div>
        <div class="descriptab">
            <p>{$module_description|escape:'htmlall':'UTF-8'}</p>
        </div>
        <div class="list-group">
            <ul class="nav nav-pills nav-stacked" role="tablist">
                {foreach from=$tabs item=tab}
                    <li class='{if isset($tab.active) && $tab.active}active{/if}'>
                        <a class="list-group-item tabinnova" href="{if $tab.type == 'tab'}#{/if}{$tab.link|escape:'htmlall':'UTF-8'}" {if $tab.type != 'tab'} target="_blank" {/if} {if $tab.type == 'tab'}data-toggle="tab" {/if}>
                            <i {if isset($tab.icon)}class="icon icon-{$tab.icon|escape:'htmlall':'UTF-8'}"{/if}></i> {$tab.title|escape:'htmlall':'UTF-8'}
                        </a>
                    </li>
                    
                {/foreach}
            </ul>
            {if isset($isAddons) && $isAddons}
                <a href="{$isoLinks['certified']|escape:'htmlall':'UTF-8'}" target="blank">
                    <img class="img-responsive center-block" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/modules/certified-agency.png">
                </a>
            {else}
                <a href="{$isoLinks['web']|escape:'htmlall':'UTF-8'}" target="blank">
                    <img class="img-responsive center-block" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/modules/innovatabs_bottom_whmcs.png">
                </a>
            {/if}
        </div>
    </div> 
</div>
<div class="col-lg-10">