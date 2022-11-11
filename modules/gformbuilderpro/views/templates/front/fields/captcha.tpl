{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
{literal}
{if isset($using_v3) && $using_v3}
    <input type="hidden" name="recaptcha_response" id="recaptchaResponse" data-sitekey="{$sitekey|escape:'html':'UTF-8'}" />
{else}
{/literal}
    {if $labelpos == 0 || $labelpos == 3}
        <div class="form-group capcha_box">
            {if $labelpos == 0}
        	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            {/if}
        	<div id="{$idatt|escape:'html':'UTF-8'}" class="g-recaptcha" data-sitekey="{literal}{$sitekey|escape:'html':'UTF-8'}{/literal}" style="transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
            {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
         </div>
    {else}
        <div class="form-group capcha_box">
            <div class="row">
                {if $labelpos == 1}
                <div class="col-xs-12 col-md-4">
            	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
                </div> 
                {/if}
                <div class="col-xs-12 col-md-8">
                    <div id="{$idatt|escape:'html':'UTF-8'}" class="g-recaptcha" data-sitekey="{literal}{$sitekey|escape:'html':'UTF-8'}{/literal}" style="transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
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
{literal}
{/if}
{/literal}