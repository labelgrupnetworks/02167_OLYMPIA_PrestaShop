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
    <div class="form-group yesno_box">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
        {/if}
        <div class="onoffswitch">
            <input type="checkbox" name="{$name|escape:'html':'UTF-8'}" class="{$classatt|escape:'html':'UTF-8'} onoffswitch-checkbox" id="{$idatt|escape:'html':'UTF-8'}" value="{l s='YES' mod='gformbuilderpro'}" data-value="{l s='NO' mod='gformbuilderpro'}"/>
            <label class="onoffswitch-label" for="{$idatt|escape:'html':'UTF-8'}">
                <span class="onoffswitch-inner">
                    <span class="onoffswitch-inneryes">{l s='YES' mod='gformbuilderpro'}</span>
                    <span class="onoffswitch-innerno">{l s='NO' mod='gformbuilderpro'}</span>
                </span>
                <span class="onoffswitch-switch"></span>
            </label>
            
        </div>
        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
     </div>
{else}
    <div class="form-group yesno_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}           
            <div class="col-xs-12 col-md-8">
                <div class="onoffswitch">
                    <input type="checkbox" name="{$name|escape:'html':'UTF-8'}" class="{$classatt|escape:'html':'UTF-8'} onoffswitch-checkbox" id="{$idatt|escape:'html':'UTF-8'}" value="{l s='YES' mod='gformbuilderpro'}" data-value="{l s='NO' mod='gformbuilderpro'}"/>
                    <label class="onoffswitch-label" for="{$idatt|escape:'html':'UTF-8'}">
                        <span class="onoffswitch-inner">
                            <span class="onoffswitch-inneryes">{l s='YES' mod='gformbuilderpro'}</span>
                            <span class="onoffswitch-innerno">{l s='NO' mod='gformbuilderpro'}</span>
                        </span>
                        <span class="onoffswitch-switch"></span>
                    </label>
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