{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2020 Globo JSC 
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if $labelpos == 0 || $labelpos == 3}
    <div class="form-group select_box">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" class="{if $required} required_label{/if} toplabel">{$label|escape:'html':'UTF-8'}</label>
        {/if}
    	<select name="{$name|escape:'html':'UTF-8'}{if $multi}[]{/if}" id="{$idatt|escape:'html':'UTF-8'}" class="{$classatt|escape:'html':'UTF-8'} form-control select_chosen" {if $required} required="required" {/if} {if $multi} multiple {/if}>
            {if !$required}<option value="">{l s='Select reference' mod='gformbuilderpro'}</option>{/if}
            {if $value}
                {foreach $value as $_value}
                    <option value="{$_value|escape:'html':'UTF-8'}">{$_value|escape:'html':'UTF-8'}</option>
                {/foreach}
            {/if}
        </select>
        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
     </div>
{else}
    <div class="form-group select_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
            <div class="col-xs-12 col-md-8">
                <select name="{$name|escape:'html':'UTF-8'}{if $multi}[]{/if}" id="{$idatt|escape:'html':'UTF-8'}" class="{$classatt|escape:'html':'UTF-8'} form-control select_chosen" {if $required} required="required" {/if} {if $multi} multiple {/if}>
                    {if !$required}<option value="">{l s='Select reference' mod='gformbuilderpro'}</option>{/if}
                    {if $value}
                        {foreach $value as $_value}
                            <option value="{$_value|escape:'html':'UTF-8'}">{$_value|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
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