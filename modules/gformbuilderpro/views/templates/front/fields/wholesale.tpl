{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if $labelpos == 0 || $labelpos == 3}
    <div class="form-group {literal}{if $isps17}ps17{/if} wholesale_box" data-token="{$tokenCart|escape:'html':'UTF-8'}{/literal}">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
        {/if}
        <div class="gformwholesale_boxproducts">
            <input type="hidden" name="{$name|escape:'html':'UTF-8'}" class="wholesale_box_idcart" value="0"/>
            <div class="gformwholesale-list">
                {literal}
                    {if isset({/literal}${$name|escape:'html':'UTF-8'}wholesale{literal})}
                        {foreach {/literal}${$name|escape:'html':'UTF-8'}wholesale{literal} as $product}
                            <div class="gform_card">
                                <div class="gform_card_header">
                                    <div class="gform_card_header_default">
                                        <div class="gform_card_header_left_default">
                                            <input type="checkbox" id="wholesale_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}" value="{$product.id|escape:'html':'UTF-8'}" class="gform-checkbox checkbox">
                                            <div class="gform-product">
                                                <span class="gform-product-image">
                                                    <img src="{$product.image_link|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                                                </span>
                                                <span class="gform-product-title"><a target="_blank" href="{$product.link|escape:'html':'UTF-8'}"><label>{$product.name|escape:'html':'UTF-8'}</label></a></span>
                                            </div>
                                        </div>
                                        <div class="gform_card_header_right_default">
                                            <span class="pull-right icon_click_opend icon-plus">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="gform_card_body gformnone">
                                    <div class="gform-discounts">
                                        {if $product.vouchers}
                                            {foreach from=$product.vouchers key=key_voucher item=vouchers}
                                                {math equation='x + y' x=$key_voucher y=1 assign='next_key_voucher'}
                                                {math equation='x + y' x=$vouchers.qty y=1 assign='next_qty'}
                                                <div class="gformdiscount-desc" data-min="{$vouchers.qty|escape:'html':'UTF-8'}" data-max="{if isset($product.vouchers.$next_key_voucher)}{if $next_qty < $product.vouchers.$next_key_voucher.qty}{$product.vouchers.$next_key_voucher.qty|escape:'html':'UTF-8'}{elseif $next_qty = $product.vouchers.$next_key_voucher.qty}{$vouchers.qty|escape:'html':'UTF-8'}{else}99999{/if}{else}99999{/if}" data-value="{$vouchers.value|escape:'html':'UTF-8'}" data-type="{$vouchers.type|escape:'html':'UTF-8'}" data-currency="{$vouchers.currency|escape:'html':'UTF-8'}"  data-tax="{$vouchers.tax|escape:'html':'UTF-8'}" data-type="{$vouchers.type|escape:'html':'UTF-8'}">
                                                    <span class="gformdiscount-desc-qty">{if isset($product.vouchers.$next_key_voucher)} {if $next_qty < $product.vouchers.$next_key_voucher.qty}{$vouchers.qty|escape:'html':'UTF-8'} - {$product.vouchers.$next_key_voucher.qty|escape:'html':'UTF-8'}{elseif $next_qty = $product.vouchers.$next_key_voucher.qty} {$vouchers.qty|escape:'html':'UTF-8'} - {$vouchers.qty|escape:'html':'UTF-8'}{else} >= {$vouchers.qty|escape:'html':'UTF-8'} {/if}{else} >= {$vouchers.qty|escape:'html':'UTF-8'}{/if}</span>
                                                    <span class="gformdiscount-desc-value">{if $vouchers.type == 1 }{Tools::displayPrice($vouchers.value)|escape:'html':'UTF-8'}{else}{$vouchers.value|escape:'html':'UTF-8'}{l s='%' mod='gformbuilderpro'}{/if} {l s='OFF' mod='gformbuilderpro'} </span>
                                                </div>
                                            {/foreach}
                                        {/if}
                                    </div>
                                    <div class="gform_allcombin">
                                        <table class="table">
                                            <thead>
                                                <tr class="nodrag nodrop">
                                                    <th></th>
                                                    <th class="gform_allcombin_title-header">{l s='Title' mod='gformbuilderpro'}</th>
                                                    <th class="gform_allcombin_title-header">{l s='QTY' mod='gformbuilderpro'}</th>
                                                    <th class="gform_allcombin_title-header">{l s='Price' mod='gformbuilderpro'}</th>
                                                    <th class="gform_allcombin_title-header">{l s='Total' mod='gformbuilderpro'}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {if $product.combinations}
                                                    {foreach from=$product.combinations key=key_combin item=combinations}
                                                        <tr id="wholesale-trbox-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}">
                                                            <td style=" text-align: right; ">
                                                                <input type="checkbox" name="wholesaleboxProduct[{$product.id|escape:'html':'UTF-8'}][]" id="wholesale_box_{$product.id|escape:'html':'UTF-8'}_{$key_combin|escape:'html':'UTF-8'}" value="{$key_combin|escape:'html':'UTF-8'}" class="gform-combination-checkbox checkbox">
                                                            </td>
                                                            <td>
                                                                {$combinations.attributes|substr:0:-1|escape:'html':'UTF-8'}
                                                            </td>
                                                            <td>
                                                                <div class="wholesale_spinner_wp">
                                                                    <span data-id="variant-quantity-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}" class="wholesale_spinner_sub">-</span>
                                                                    <input type="text" data-range="1" data-min="1" data-max="999999" value="1" class="form-control variant-quantity " id="variant-quantity-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}" placeholder="" name="wholesaleboxProductQty[{$product.id|escape:'html':'UTF-8'}][{$key_combin|escape:'html':'UTF-8'}]">
                                                                    <span data-id="variant-quantity-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}" class="wholesale_spinner_plus">+</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="gform_original-price">{$combinations.price|escape:'html':'UTF-8'}</div>
                                                            </td>
                                                            <td>
                                                                <div class="gform_discount-price"></div>
                                                                <div class="gform_total-price">{$combinations.price|escape:'html':'UTF-8'}</div>
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                                {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    {/if}
                {/literal}
            </div>
            <div class="gformwholesale-total">
                <div class="gformwholesale-subtotal">
                    <span class="gformwholesale-subtotal-label">{l s='Total :' mod='gformbuilderpro'}</span>
                    <span class="gformwholesale-subtotalprice-label"> {Tools::displayPrice(0)|escape:'html':'UTF-8'} </span>
                </div>
                {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
            </div>
        </div>
     </div>
{else}
    <div class="form-group wholesale_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}           
            <div class="col-xs-12 col-md-8">
                <div class="gformwholesale_boxproducts">
                    <input type="hidden" name="{$name|escape:'html':'UTF-8'}" class="wholesale_box_idcart" value="0"/>
                    <div class="gformwholesale-list">
                        {literal}
                            {if isset({/literal}${$name|escape:'html':'UTF-8'}wholesale{literal})}
                                {foreach {/literal}${$name|escape:'html':'UTF-8'}wholesale{literal} as $product}
                                    <div class="gform_card">
                                        <div class="gform_card_header">
                                            <div class="gform_card_header_default">
                                                <div class="gform_card_header_left_default">
                                                    <input type="checkbox" id="wholesale_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}" value="{$product.id|escape:'html':'UTF-8'}" class="gform-checkbox checkbox">
                                                    <div class="gform-product">
                                                        <span class="gform-product-image">
                                                            <img src="{$product.image_link|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                                                        </span>
                                                        <span class="gform-product-title"><a target="_blank" href="{$product.link|escape:'html':'UTF-8'}"><label>{$product.name|escape:'html':'UTF-8'}</label></a></span>
                                                    </div>
                                                </div>
                                                <div class="gform_card_header_right_default">
                                                    <span class="pull-right icon_click_opend icon-plus">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gform_card_body gformnone">
                                            <div class="gform-discounts">
                                                {if $product.vouchers}
                                                    {foreach from=$product.vouchers key=key_voucher item=vouchers}
                                                        {math equation='x + y' x=$key_voucher y=1 assign='next_key_voucher'}
                                                        {math equation='x + y' x=$vouchers.qty y=1 assign='next_qty'}
                                                        <div class="gformdiscount-desc" data-min="{$vouchers.qty|escape:'html':'UTF-8'}" data-max="{if isset($product.vouchers.$next_key_voucher)}{if $next_qty < $product.vouchers.$next_key_voucher.qty}{$product.vouchers.$next_key_voucher.qty|escape:'html':'UTF-8'}{elseif $next_qty = $product.vouchers.$next_key_voucher.qty}{$vouchers.qty|escape:'html':'UTF-8'}{else}99999{/if}{else}99999{/if}" data-value="{$vouchers.value|escape:'html':'UTF-8'}" data-type="{$vouchers.type|escape:'html':'UTF-8'}" data-currency="{$vouchers.currency|escape:'html':'UTF-8'}"  data-tax="{$vouchers.tax|escape:'html':'UTF-8'}" data-type="{$vouchers.type|escape:'html':'UTF-8'}">
                                                            <span class="gformdiscount-desc-qty">{if isset($product.vouchers.$next_key_voucher)} {if $next_qty < $product.vouchers.$next_key_voucher.qty}{$vouchers.qty|escape:'html':'UTF-8'} - {$product.vouchers.$next_key_voucher.qty|escape:'html':'UTF-8'}{elseif $next_qty = $product.vouchers.$next_key_voucher.qty} {$vouchers.qty|escape:'html':'UTF-8'} - {$vouchers.qty|escape:'html':'UTF-8'}{else} >= {$vouchers.qty|escape:'html':'UTF-8'} {/if}{else} >= {$vouchers.qty|escape:'html':'UTF-8'}{/if}</span>
                                                            <span class="gformdiscount-desc-value">{if $vouchers.type == 1 }{Tools::displayPrice($vouchers.value)|escape:'html':'UTF-8'}{else}{$vouchers.value|escape:'html':'UTF-8'}{l s='%' mod='gformbuilderpro'}{/if} {l s='OFF' mod='gformbuilderpro'} </span>
                                                        </div>
                                                    {/foreach}
                                                {/if}
                                            </div>
                                            <div class="gform_allcombin">
                                                <table class="table">
                                                    <thead>
                                                        <tr class="nodrag nodrop">
                                                            <th></th>
                                                            <th class="gform_allcombin_title-header">{l s='Title' mod='gformbuilderpro'}</th>
                                                            <th class="gform_allcombin_title-header">{l s='QTY' mod='gformbuilderpro'}</th>
                                                            <th class="gform_allcombin_title-header">{l s='Price' mod='gformbuilderpro'}</th>
                                                            <th class="gform_allcombin_title-header">{l s='Total' mod='gformbuilderpro'}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {if $product.combinations}
                                                            {foreach from=$product.combinations key=key_combin item=combinations}
                                                                <tr id="wholesale-trbox-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}">
                                                                    <td style=" text-align: right; ">
                                                                        <input type="checkbox" name="wholesaleboxProduct[{$product.id|escape:'html':'UTF-8'}][]" id="wholesale_box_{$product.id|escape:'html':'UTF-8'}_{$key_combin|escape:'html':'UTF-8'}" value="{$key_combin|escape:'html':'UTF-8'}" class="gform-combination-checkbox checkbox">
                                                                    </td>
                                                                    <td>
                                                                        {$combinations.attributes|substr:0:-1|escape:'html':'UTF-8'}
                                                                    </td>
                                                                    <td>
                                                                        <div class="wholesale_spinner_wp">
                                                                            <span data-id="variant-quantity-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}" class="wholesale_spinner_sub">-</span>
                                                                            <input type="text" data-range="1" data-min="1" data-max="999999" value="1" class="form-control variant-quantity " id="variant-quantity-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}" placeholder="" name="wholesaleboxProductQty[{$product.id|escape:'html':'UTF-8'}][{$key_combin|escape:'html':'UTF-8'}]">
                                                                            <span data-id="variant-quantity-{$product.id|escape:'html':'UTF-8'}-{$key_combin|escape:'html':'UTF-8'}" class="wholesale_spinner_plus">+</span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="gform_original-price">{$combinations.price|escape:'html':'UTF-8'}</div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="gform_discount-price"></div>
                                                                        <div class="gform_total-price">{$combinations.price|escape:'html':'UTF-8'}</div>
                                                                    </td>
                                                                </tr>
                                                            {/foreach}
                                                        {/if}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        {/literal}
                    </div>
                    <div class="gformwholesale-total">
                        <div class="gformwholesale-subtotal">
                            <span class="gformwholesale-subtotal-label">{l s='Total :' mod='gformbuilderpro'}</span>
                            <span class="gformwholesale-subtotalprice-label"> {Tools::displayPrice(0)|escape:'html':'UTF-8'} </span>
                        </div>
                        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
                    </div>
                </div>
            </div>
            {if $labelpos == 2}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
        </div>
    </div>
{/if}