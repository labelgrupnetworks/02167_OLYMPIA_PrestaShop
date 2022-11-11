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
    <div class="form-group checkbox_box">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" class="{if $required} required_label{/if}">{$label|escape:'html':'UTF-8'}</label>
        {/if}
        <div class="checkbox_item_wp">
            {if $value}
                <div class="row">
                {foreach $value as $key=>$_value}
                    <p  class="col-xs-12 {if isset($extra) && $extra > 0}col-md-{$extra|intval}{/if}"><input id="checkbox_{$name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" type="checkbox" name="{$name|escape:'html':'UTF-8'}[]" class="{$classatt|escape:'html':'UTF-8'}" value="{$_value|escape:'html':'UTF-8'}" /><label for="checkbox_{$name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}">{$_value|escape:'html':'UTF-8'}</label></p>
                {/foreach}
                </div>
            {/if}
        </div>
        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
    </div>
{else}
    <div class="form-group checkbox_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div> 
            {/if}
            <div class="col-xs-12 col-md-8">
                <div class="checkbox_item_wp">
                    {if $value}
                        {foreach $value as $key=>$_value}
                            <p class="col-xs-12 {if isset($extra) && $extra > 0}col-md-{$extra|intval}{/if}"><input id="checkbox_{$name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" type="checkbox" name="{$name|escape:'html':'UTF-8'}[]" class="{$classatt|escape:'html':'UTF-8'}" value="{$_value|escape:'html':'UTF-8'}" /><label for="checkbox_{$name|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}">{$_value|escape:'html':'UTF-8'}</label></p>
                        {/foreach}
                    {/if}
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