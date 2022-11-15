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
    <div class="form-group product_box">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
        {/if}
        <div class="product_item_wp owl-carousel">
        {literal}
            {if isset({/literal}${$name|escape:'html':'UTF-8'}product{literal})}
                {foreach {/literal}${$name|escape:'html':'UTF-8'}product{literal} as $product}
                    <div class="gform_product_item">
                        <a target="_blank" href="{$product.link|escape:'html':'UTF-8'}">
                            <img class="gform_product_image" src="{$product.image_link|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                        </a>
                        <p>{/literal}
                        {if isset($multi) && $multi}
                        {literal}
                        <input type="checkbox" id="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}" name="{/literal}{$name|escape:'html':'UTF-8'}{literal}[]" class="{/literal}{$classatt|escape:'html':'UTF-8'}{literal}" value="{$product.id|escape:'html':'UTF-8'}" /><label for="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</label>
                        {/literal}
                        {else}
                        {literal}
                        <input type="radio" id="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}" name="{/literal}{$name|escape:'html':'UTF-8'}{literal}" class="{/literal}{$classatt|escape:'html':'UTF-8'}{literal}" value="{$product.id|escape:'html':'UTF-8'}" /><label for="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</label>
                        {/literal}
                        {/if}
                        {literal}
                        </p>
                    </div>
                {/foreach}
            {/if}
        {/literal}
        </div>
        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
    </div>
{else}
    <div class="form-group product_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if} 
            <div class="col-xs-12 col-md-8">
                <div class="product_item_wp owl-carousel">
                    {literal}
                        {if isset({/literal}${$name|escape:'html':'UTF-8'}product{literal})}
                            {foreach {/literal}${$name|escape:'html':'UTF-8'}product{literal} as $product}
                                <div class="gform_product_item">
                                    <a target="_blank" href="{$product.link|escape:'html':'UTF-8'}">
                                        <img class="gform_product_image" src="{$product.image_link|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                                    </a>
                                    <p>{/literal}
                                    {if isset($multi) && $multi}
                                    {literal}
                                    <input type="checkbox" id="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}" name="{/literal}{$name|escape:'html':'UTF-8'}{literal}[]" class="{/literal}{$classatt|escape:'html':'UTF-8'}{literal}" value="{$product.id|escape:'html':'UTF-8'}" /><label for="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</label>
                                    {/literal}
                                    {else}
                                    {literal}
                                    <input type="radio" id="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}" name="{/literal}{$name|escape:'html':'UTF-8'}{literal}" class="{/literal}{$classatt|escape:'html':'UTF-8'}{literal}" value="{$product.id|escape:'html':'UTF-8'}" /><label for="product_box_{/literal}{$name|escape:'html':'UTF-8'}{literal}_{$product.id|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</label>
                                    {/literal}
                                    {/if}
                                    {literal}
                                    </p>
                                </div>
                            {/foreach}
                        {/if}
                    {/literal}
                </div>
                {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
            </div>
            {if $labelpos == 2}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
        </div>
    </div>
{/if}