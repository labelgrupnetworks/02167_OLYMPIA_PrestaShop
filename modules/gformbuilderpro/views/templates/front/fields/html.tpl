{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

<div id="{$idatt|escape:'html':'UTF-8'}" class="html_box {$classatt|escape:'html':'UTF-8'}">
    {if isset($isps17) && $isps17}
        {$description nofilter}{* $description is html content, no need to escape*}
    {else}
        {$description}{* $description is html content, no need to escape*}
    {/if}
</div>