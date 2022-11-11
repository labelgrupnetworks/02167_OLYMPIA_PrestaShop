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
    	<label></label>
        {/if}
        <div class="checkbox_item_wp">
            <div class="privacy_wp">
                <input id="checkbox_{$name|escape:'html':'UTF-8'}" type="checkbox" name="{$name|escape:'html':'UTF-8'}" class="checkbox_privacy {$classatt|escape:'html':'UTF-8'}" value="1" />
                <div class="privacy_des">
                    {if isset($isps17) && $isps17}
                        {$description nofilter}{* $description is html content, no need to escape*}
                    {else}
                        {$description}{* $description is html content, no need to escape*}
                    {/if}
                </div>
            </div>
        </div>
    </div>
{else}
    <div class="form-group checkbox_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label></label>
            </div> 
            {/if}
            <div class="col-xs-12 col-md-8">
                <div class="privacy_wp">
                    <input id="checkbox_{$name|escape:'html':'UTF-8'}" type="checkbox" name="{$name|escape:'html':'UTF-8'}" class="checkbox_privacy {$classatt|escape:'html':'UTF-8'}" value="1" />
                    <div class="privacy_des">
                        {if isset($isps17) && $isps17}
                            {$description nofilter}{* $description is html content, no need to escape*}
                        {else}
                            {$description}{* $description is html content, no need to escape*}
                        {/if}
                    </div>
                </div>
            </div>
            {if $labelpos == 2}
            <div class="col-xs-12 col-md-4">
        	   <label></label>
            </div> 
            {/if}
        </div>
    </div>
{/if}