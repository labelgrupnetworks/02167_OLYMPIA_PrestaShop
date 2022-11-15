{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if isset($multi) && $multi}
    {if $labelpos == 0 || $labelpos == 3}
        <div class="form-group multifileupload_box">
        	{if $labelpos == 0}
        	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            {/if}
        	<input  data-no-uniform="true"  multiple type="file" name="{$name|escape:'html':'UTF-8'}[]" id="{$idatt|escape:'html':'UTF-8'}" class="form-control {$classatt|escape:'html':'UTF-8'} filestyle multifileupload" data-buttonName="btn-primary"  data-buttonText="{literal}{if isset($button_upload_text)}{$button_upload_text}{else}{l s='Choose file' mod='gformbuilderpro'}{/if}{/literal}" />
            {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
         </div>
    {else}
        <div class="form-group multifileupload_box">
            <div class="row">
                {if $labelpos == 1}
                <div class="col-xs-12 col-md-4">
            	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
                </div>  
                {/if} 
                <div class="col-xs-12 col-md-8">
                    <input data-no-uniform="true" multiple type="file" name="{$name|escape:'html':'UTF-8'}[]" id="{$idatt|escape:'html':'UTF-8'}" class="form-control {$classatt|escape:'html':'UTF-8'} filestyle multifileupload" data-buttonName="btn-primary"  data-buttonText="{literal}{if isset($button_upload_text)}{$button_upload_text}{else}{l s='Choose file' mod='gformbuilderpro'}{/if}{/literal}" />
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
{else}
    {if $labelpos == 0 || $labelpos == 3}
        <div class="form-group fileupload_box">
        	{if $labelpos == 0}
        	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            {/if}
        	<input type="file" name="{$name|escape:'html':'UTF-8'}" id="{$idatt|escape:'html':'UTF-8'}" class="form-control {$classatt|escape:'html':'UTF-8'} filestyle" data-buttonName="btn-primary"  data-buttonText="{literal}{if isset($button_upload_text)}{$button_upload_text}{else}{l s='Choose file' mod='gformbuilderpro'}{/if}{/literal}" />
            {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
         </div>
    {else}
        <div class="form-group fileupload_box">
            <div class="row">
                {if $labelpos == 1}
                <div class="col-xs-12 col-md-4">
            	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
                </div>  
                {/if}
                <div class="col-xs-12 col-md-8">
                    <input type="file" name="{$name|escape:'html':'UTF-8'}" id="{$idatt|escape:'html':'UTF-8'}" class="form-control {$classatt|escape:'html':'UTF-8'} filestyle" data-buttonName="btn-primary"  data-buttonText="{literal}{if isset($button_upload_text)}{$button_upload_text}{else}{l s='Choose file' mod='gformbuilderpro'}{/if}{/literal}" />
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
{/if}