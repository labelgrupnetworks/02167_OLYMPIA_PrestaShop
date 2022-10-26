{*
* 2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement
*
* @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
* @copyright 2017 IQIT-COMMERCE.COM
* @license   Commercial license (You can not resell or redistribute this software.)
*
*}

{if !$update}
<div id="iqitcompare-notification" class="ns-box ns-effect-thumbslider ns-text-only">
    <div class="ns-box-inner">
        <div class="ns-content">
            <span class="ns-title"><i class="fa fa-check" aria-hidden="true"></i> <strong>{l s='Product added to compare.' mod='iqitcompare'}</strong></span>
        </div>
    </div>
</div>
{/if}


{if !$comparePage}
<div id="iqitcompare-floating-wrapper">
{if $compareProductsNb}
<div id="iqitcompare-floating" class="">
    <a href="{url entity='module' name='iqitcompare' controller='comparator'}">
        <span>{l s='Compare' mod='iqitcompare'} ({$compareProductsNb}) </span>
        {foreach from=$covers  key=myId item=cover name=compareCovers}
            {if $smarty.foreach.compareCovers.index < 6}
                {if $cover}
                    <img src="{$cover nofilter}" class="img-fluid" alt="{$myId}"  loading="lazy" />
                {else}
                    <img src="{$urls.no_picture_image.bySize.cart_default.url}" class="img-fluid"  alt="{$myId}" loading="lazy" />
                {/if}
            {else}
                <span>...</span>
                {break}
            {/if}
        {/foreach}
    </a>
</div>
{/if}
</div>
{/if}


