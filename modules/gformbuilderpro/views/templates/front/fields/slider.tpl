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
    <div class="form-group slider_box">
        {if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'} : <span id="{$idatt|escape:'html':'UTF-8'}-value">{if isset($multi) && $multi}{$extra.3|escape:'html':'UTF-8'}->{$extra.4|escape:'html':'UTF-8'}{else}{$extra.3|escape:'html':'UTF-8'}{/if}</span></label>
        {/if}
    	<input type="hidden" data-range="{$extra.2|escape:'html':'UTF-8'}"   data-min="{$extra.0|escape:'html':'UTF-8'}" data-max="{$extra.1|escape:'html':'UTF-8'}" {if isset($multi) && $multi} data-valmin="{$extra.3|escape:'html':'UTF-8'}" data-valmax="{$extra.4|escape:'html':'UTF-8'}" {/if} value="{if isset($multi) && $multi}{$extra.3|escape:'html':'UTF-8'}->{$extra.4|escape:'html':'UTF-8'}{else}{$extra.3|escape:'html':'UTF-8'}{/if}" class="form-control {$classatt|escape:'html':'UTF-8'} " id="{$idatt|escape:'html':'UTF-8'}" placeholder="{$placeholder|escape:'html':'UTF-8'}"  name="{$name|escape:'html':'UTF-8'}"  {if $required} required="required" {/if} />
        <div id="{$idatt|escape:'html':'UTF-8'}-range-min" data-id="{$idatt|escape:'html':'UTF-8'}" class="{if isset($multi) && $multi}slider-range-multi{else}slider-range{/if}"></div>
        <div class="slider_max_min_box">
            <span class="slider_min_box">{$extra.0|escape:'html':'UTF-8'}</span>
            <span class="slider_max_box">{$extra.1|escape:'html':'UTF-8'}</span>
        </div>
        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
    </div>
{else}
    <div class="form-group slider_box">
        <div class="row">
            {if $labelpos == 2}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'} : <span id="{$idatt|escape:'html':'UTF-8'}-value">{if isset($multi) && $multi}{$extra.3|escape:'html':'UTF-8'}->{$extra.4|escape:'html':'UTF-8'}{else}{$extra.3|escape:'html':'UTF-8'}{/if}</span></label>
            </div>  
            {/if}
            <div class="col-xs-12 col-md-8">
        	   <input type="hidden" data-range="{$extra.2|escape:'html':'UTF-8'}"   data-min="{$extra.0|escape:'html':'UTF-8'}" data-max="{$extra.1|escape:'html':'UTF-8'}" {if isset($multi) && $multi} data-valmin="{$extra.3|escape:'html':'UTF-8'}" data-valmax="{$extra.4|escape:'html':'UTF-8'}" {/if} value="{if isset($multi) && $multi}{$extra.3|escape:'html':'UTF-8'}->{$extra.4|escape:'html':'UTF-8'}{else}{$extra.3|escape:'html':'UTF-8'}{/if}" class="form-control {$classatt|escape:'html':'UTF-8'} " id="{$idatt|escape:'html':'UTF-8'}" placeholder="{$placeholder|escape:'html':'UTF-8'}"  name="{$name|escape:'html':'UTF-8'}"  {if $required} required="required" {/if} />
                <div id="{$idatt|escape:'html':'UTF-8'}-range-min" data-id="{$idatt|escape:'html':'UTF-8'}" class="{if isset($multi) && $multi}slider-range-multi{else}slider-range{/if}"></div>
                <div class="slider_max_min_box">
                    <span class="slider_min_box">{$extra.0|escape:'html':'UTF-8'}</span>
                    <span class="slider_max_box">{$extra.1|escape:'html':'UTF-8'}</span>
                </div>
               {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
            </div>
            {if $labelpos == 2}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'} : <span id="{$idatt|escape:'html':'UTF-8'}-value">{if isset($multi) && $multi}{$extra.3|escape:'html':'UTF-8'}->{$extra.4|escape:'html':'UTF-8'}{else}{$extra.3|escape:'html':'UTF-8'}{/if}</span></label>
            </div> 
            {/if}
        </div>
    </div>
{/if}