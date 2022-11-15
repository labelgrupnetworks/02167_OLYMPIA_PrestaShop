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
    <div class="form-group rating_box">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
        {/if}
    	<div class="rating_box_content">
            <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star1" name="{$name|escape:'html':'UTF-8'}" value="1" /><label class="{$name|escape:'html':'UTF-8'}star1 starlabel" for="{$name|escape:'html':'UTF-8'}star1" title="{l s='1 Star' mod='gformbuilderpro'}"></label>
            <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star2" name="{$name|escape:'html':'UTF-8'}" value="2" /><label class="{$name|escape:'html':'UTF-8'}star2 starlabel"  for="{$name|escape:'html':'UTF-8'}star2" title="{l s='2 Star' mod='gformbuilderpro'}"></label>
            <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star3" name="{$name|escape:'html':'UTF-8'}" value="3" /><label class="{$name|escape:'html':'UTF-8'}star3 starlabel"  for="{$name|escape:'html':'UTF-8'}star3" title="{l s='3 Star' mod='gformbuilderpro'}"></label>
            <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star4" name="{$name|escape:'html':'UTF-8'}" value="4" /><label class="{$name|escape:'html':'UTF-8'}star4 starlabel"  for="{$name|escape:'html':'UTF-8'}star4" title="{l s='4 Star' mod='gformbuilderpro'}"></label>
            <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star5" name="{$name|escape:'html':'UTF-8'}" value="5" /><label class="{$name|escape:'html':'UTF-8'}star5 starlabel"  for="{$name|escape:'html':'UTF-8'}star5" title="{l s='5 Star' mod='gformbuilderpro'}"></label>
        </div>
        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
     </div>
{else}
    <div class="form-group rating_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if} 
            <div class="col-xs-12 col-md-8">
                <div class="rating_box_content">
                    <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star1" name="{$name|escape:'html':'UTF-8'}" value="1" /><label class="{$name|escape:'html':'UTF-8'}star1 starlabel" for="{$name|escape:'html':'UTF-8'}star1" title="{l s='1 Star' mod='gformbuilderpro'}"></label>
                    <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star2" name="{$name|escape:'html':'UTF-8'}" value="2" /><label class="{$name|escape:'html':'UTF-8'}star2 starlabel"  for="{$name|escape:'html':'UTF-8'}star2" title="{l s='2 Star' mod='gformbuilderpro'}"></label>
                    <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star3" name="{$name|escape:'html':'UTF-8'}" value="3" /><label class="{$name|escape:'html':'UTF-8'}star3 starlabel"  for="{$name|escape:'html':'UTF-8'}star3" title="{l s='3 Star' mod='gformbuilderpro'}"></label>
                    <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star4" name="{$name|escape:'html':'UTF-8'}" value="4" /><label class="{$name|escape:'html':'UTF-8'}star4 starlabel"  for="{$name|escape:'html':'UTF-8'}star4" title="{l s='4 Star' mod='gformbuilderpro'}"></label>
                    <input type="radio" class="grating" id="{$name|escape:'html':'UTF-8'}star5" name="{$name|escape:'html':'UTF-8'}" value="5" /><label class="{$name|escape:'html':'UTF-8'}star5 starlabel"  for="{$name|escape:'html':'UTF-8'}star5" title="{l s='5 Star' mod='gformbuilderpro'}"></label>
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