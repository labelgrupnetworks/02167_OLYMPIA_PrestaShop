{*
* 2019-2021 Labelgrup
*
* !!! DEPRECATED FOR 1.7 !!!
*
* NOTICE OF LICENSE
*
* READ ATTACHED LICENSE.TXT
*
*  @author    Manel Alonso <malonso@labelgrup.com>
*  @copyright 2019-2021 Labelgrup
*  @license   LICENSE.TXT
*}

<p class="payment_module">
    <a href="{$link->getModuleLink('lblcustompayment', 'payment')|escape:'html'}" title="{l s='Pay by' mod='lbpagodirecto'}&nbsp;{$payment_name|escape:'htmlall':'utf-8'}">
        <img alt="{l s='Pay by' mod='lbpagodirecto'}&nbsp;{$payment_name|escape:'htmlall':'utf-8'}" src="{$this_path|escape:'html'}logo.png" width="86" height="49"/>
        {l s='Pay by' mod='lbpagodirecto'}&nbsp;<span>{$payment_name|escape:'htmlall':'utf-8'}</span>
    </a>
</p>