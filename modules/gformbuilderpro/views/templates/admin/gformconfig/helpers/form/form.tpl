{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'formbuildertabopen'}
        {if !isset($fields_value['psoldversion15']) || $fields_value['psoldversion15'] != -1}
        </div>
        {/if}
        <div id="{$input.name|escape:'html':'UTF-8'}" class="formbuilder_tab {if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}">
        {if !isset($fields_value['psoldversion15']) || $fields_value['psoldversion15'] != -1}
        <div>
        {/if}
    {else if $input.type == 'formbuildertabclose'}
        </div>
    {/if}
    {$smarty.block.parent}
{/block}