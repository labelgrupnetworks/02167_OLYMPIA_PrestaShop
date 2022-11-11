{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

<div class="hidden_box">
    <input type="hidden" value="{if $extra !='' && $extra !='productatt'}{literal}{${/literal}{$extra|escape:'html':'UTF-8'}{literal}}{/literal}{else}{$value|escape:'html':'UTF-8'}{/if}" name="{$name|escape:'html':'UTF-8'}" id="{$idatt|escape:'html':'UTF-8'}" class="{if $extra =='productatt'} hidden_productatt {/if} {$classatt|escape:'html':'UTF-8'}" />
</div>