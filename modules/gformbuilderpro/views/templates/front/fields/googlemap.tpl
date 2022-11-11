{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
{if isset($value) && $value!=''}
{if $labelpos == 0 || $labelpos == 3}
<div class="form-group googlemap_box">
    {if $labelpos == 0}
	<label>{$label|escape:'html':'UTF-8'}</label>
    {/if}
	<div id='google-maps-{$name|escape:'html':'UTF-8'}' class="google-maps" data-gmap_key="{literal}{if isset($gmap_key) && $gmap_key !=''}{$gmap_key|escape:'html':'UTF-8'}{/if}{/literal}" data-name="{$name|escape:'html':'UTF-8'}" data-label="{$label|escape:'html':'UTF-8'}" data-description="{$description|escape:'html':'UTF-8'}" data-value="{$value|escape:'html':'UTF-8'}"></div>
</div>
{else}
    <div class="form-group fileupload_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
            <div class="col-xs-12 col-md-8">
                <div id='google-maps-{$name|escape:'html':'UTF-8'}' class="google-maps" data-gmap_key="{literal}{if isset($gmap_key) && $gmap_key !=''}{$gmap_key|escape:'html':'UTF-8'}{/if}{/literal}" data-name="{$name|escape:'html':'UTF-8'}" data-label="{$label|escape:'html':'UTF-8'}" data-description="{$description|escape:'html':'UTF-8'}" data-value="{$value|escape:'html':'UTF-8'}"></div>
    	    </div>
            {if $labelpos == 2}
            <div class="col-xs-12 col-md-4">
        	   <label>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
        </div>
    </div>
{/if}
{/if}
