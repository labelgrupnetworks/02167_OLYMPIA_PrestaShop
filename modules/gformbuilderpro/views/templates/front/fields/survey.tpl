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
    <div class="form-group survey_box">
    	{if $labelpos == 0}
    	<label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
        {/if}
    	<table cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="ng-binding"></th>
                    {foreach $description as $colurm}
                        <th class="survey_colurm">{$colurm|escape:'html':'UTF-8'}</th>
                    {/foreach}
                </tr>
            </thead>
            <tbody>
                {foreach $value as $key=>$_value}
                <tr  class="{cycle values="odd,even"}">
                    <td class="ng-binding">{$_value|escape:'html':'UTF-8'}</td>
                    {foreach $description as $colurm}
                        <td class="surveyclass {$classatt|escape:'html':'UTF-8'}"><label><input type="radio" name="{$name|escape:'html':'UTF-8'}[{$key|escape:'html':'UTF-8'}]" value="{$colurm|escape:'html':'UTF-8'}"></label></td>
                    {/foreach}
                </tr>
                {/foreach}
            </tbody>
        </table>
     </div>
{else}
    <div class="form-group survey_box">
        <div class="row">
            {if $labelpos == 1}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
            <div class="col-xs-12 col-md-8">
        	   <table cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th class="ng-binding"></th>
                            {foreach $description as $colurm}
                                <th class="survey_colurm">{$colurm|escape:'html':'UTF-8'}</th>
                            {/foreach}
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $value as $key=>$_value}
                        <tr  class="{cycle values="odd,even"}">
                            <td class="ng-binding">{$_value|escape:'html':'UTF-8'}</td>
                            {foreach $description as $colurm}
                                <td class="surveyclass {$classatt|escape:'html':'UTF-8'}"><label><input type="radio" name="{$name|escape:'html':'UTF-8'}[{$key|escape:'html':'UTF-8'}]" value="{$colurm|escape:'html':'UTF-8'}"></label></td>
                            {/foreach}
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {if $labelpos == 2}
            <div class="col-xs-12 col-md-4">
        	   <label for="{$idatt|escape:'html':'UTF-8'}" {if $required} class="required_label"{/if}>{$label|escape:'html':'UTF-8'}</label>
            </div>  
            {/if}
        </div>
    </div>
{/if}