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
    <div class="form-group time_box">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}"  class="{if $required} required_label{/if} toplabel">{$label|escape:'html':'UTF-8'}</label>
        {/if}
        <input type="hidden" name="{$name|escape:'html':'UTF-8'}" id="{$idatt|escape:'html':'UTF-8'}" class="time_input {$classatt|escape:'html':'UTF-8'}" />
    	<select rel="{$name|escape:'html':'UTF-8'}" class="form-control time_select {$name|escape:'html':'UTF-8'}_hour" name="{$name|escape:'html':'UTF-8'}-hour">
            {if $extra}
                {for $i=0 to 12}
                    <option value="{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'}</option>
                {/for}
            {else}
                {for $i=0 to 23}
                    <option value="{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'}</option>
                {/for}
            {/if}
        </select>
        <select rel="{$name|escape:'html':'UTF-8'}" class="form-control time_select {$name|escape:'html':'UTF-8'}_minute" name="{$name|escape:'html':'UTF-8'}-minute">
            {for $i=0 to 11}
                <option value="{if $i < 2}0{/if}{$i*5|escape:'html':'UTF-8'}">{if $i < 2}0{/if}{$i*5|escape:'html':'UTF-8'}</option>
            {/for}
        </select>
        {if $extra}
            <select rel="{$name|escape:'html':'UTF-8'}" class="form-control time_select {$name|escape:'html':'UTF-8'}_apm" name="{$name|escape:'html':'UTF-8'}-apm">
                <option value="{l s='AM' mod='gformbuilderpro'}">{l s='AM' mod='gformbuilderpro'}</option>
                <option value="{l s='PM' mod='gformbuilderpro'}">{l s='PM' mod='gformbuilderpro'}</option>
            </select>
        {/if}
        {if $description!=''}<p class="help-block">{$description|escape:'html':'UTF-8'}</p>{/if}
     </div>
{else}
    <div class="form-group time_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
            <div class="col-xs-12 col-md-8">
                <input type="hidden" name="{$name|escape:'html':'UTF-8'}" id="{$idatt|escape:'html':'UTF-8'}" class="time_input {$classatt|escape:'html':'UTF-8'}" />
            	<select rel="{$name|escape:'html':'UTF-8'}" class="form-control time_select {$name|escape:'html':'UTF-8'}_hour" name="{$name|escape:'html':'UTF-8'}-hour">
                    {if $extra}
                        {for $i=0 to 12}
                            <option value="{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'}</option>
                        {/for}
                    {else}
                        {for $i=0 to 23}
                            <option value="{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'}</option>
                        {/for}
                    {/if}
                </select>
                <select rel="{$name|escape:'html':'UTF-8'}" class="form-control time_select {$name|escape:'html':'UTF-8'}_minute" name="{$name|escape:'html':'UTF-8'}-minute">
                    {for $i=0 to 11}
                        <option value="{if $i < 2}0{/if}{$i*5|escape:'html':'UTF-8'}">{if $i < 2}0{/if}{$i*5|escape:'html':'UTF-8'}</option>
                    {/for}
                </select>
                {if $extra}
                    <select rel="{$name|escape:'html':'UTF-8'}" class="form-control time_select {$name|escape:'html':'UTF-8'}_apm" name="{$name|escape:'html':'UTF-8'}-apm">
                        <option value="{l s='AM' mod='gformbuilderpro'}">{l s='AM' mod='gformbuilderpro'}</option>
                        <option value="{l s='PM' mod='gformbuilderpro'}">{l s='PM' mod='gformbuilderpro'}</option>
                    </select>
                {/if}
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