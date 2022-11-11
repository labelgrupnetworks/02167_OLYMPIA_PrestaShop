{*
* Do not edit the file if you want to upgrade the module in future.
*
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<div class="{if $labelpos == 0}submit_btn_left{elseif $labelpos == 1}submit_btn_right{elseif $labelpos == 2}submit_btn_center{/if}">
<button type="submit" name="submitForm" id="submitForm" class="button btn btn-default button-medium"><span>{if isset($label) && $label !=''}{$label|escape:'html':'UTF-8'}{else}{l s='Sent' mod='gformbuilderpro'}{/if}</span></button>
</div>