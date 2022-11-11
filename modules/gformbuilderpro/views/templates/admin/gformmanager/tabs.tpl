{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if isset($psversion15) && $psversion15 == '-1'}
<script type="text/javascript">
// <![CDATA[
    var psversion15 = {$psversion15|intval};
    var gdefault_language = {$gdefault_language|intval};
    var gtitleform = '{l s='Form Title' mod='gformbuilderpro'}';
    var copyToClipboard_success = '{l s='Copy to clipboard successfully' mod='gformbuilderpro'}';
    var allfieldstype = {$allfieldstype}; {* $allfieldstype is json data. No need to escape *}
//]]>
</script>
{/if}
{if isset($is_left_tab) && $is_left_tab && isset($is_close_tab) && $is_close_tab}
	</div>
{else}
	{if isset($is_left_tab) && $is_left_tab}
		<div class="col-lg-2 is_left_tab">
	{/if}
	<div class="productTabs gformbuilderpro_admintab">
		<ul class="tab nav nav-tabs">
			<li class="tab-row active">
				<a class="tab-page" href="#tabmain"><i class="icon-info"></i>{l s='General Information' mod='gformbuilderpro'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" href="#tabtemplate"><i class="icon-wrench"></i>{l s='Form Builder' mod='gformbuilderpro'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" href="#tabemail"><i class="icon-envelope"></i>{l s='Mail' mod='gformbuilderpro'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" href="#tabmessage"><i class="icon-check"></i>{l s='Thank you page' mod='gformbuilderpro'}</a>
			</li>
			{if isset($formrequest_link) && $formrequest_link !=''}
			<li class="tab-row">
				<a class="tab-page" href="#tabintegration"><i class="icon-puzzle-piece"></i>{l s='Third party integration' mod='gformbuilderpro'}</a>
			</li>
			<li class="tab-row">
				<a class="formrequest_link" href="{$formrequest_link|escape:'html':'UTF-8'}"><i class="icon-copy"></i>{l s='Received Data' mod='gformbuilderpro'}{if isset($nbr_received) && $nbr_received > 0}<span class="nbr_received">{$nbr_received|intval}</span>{/if}</a>
			</li>
			{/if}
			{if isset($gshortcode)}
				<li class="tab-row">
					<a class="tab-page" href="#publish"><i class="icon-link"></i>{l s='Publish' mod='gformbuilderpro'}</a>
				</li>
			{/if}
		</ul>
	</div>
	{if isset($is_left_tab) && $is_left_tab}
		</div><div class="col-lg-10">
	{/if}
	{if isset($formrequest_id) && $formrequest_id > 0}
	<div style="display: none">
		<form id="submit_to_formrequest" method="post" target="_blank" action="{$formrequest_link|escape:'html':'UTF-8'}">
			<input type="hidden" id="submitFiltergformrequest" name="submitFiltergformrequest" value="0"/>
			<input type="hidden" name="page" value="1"/>
			<input type="hidden" name="selected_pagination" value="1">
			<input type="hidden" name="gformrequestFilter_a!id_gformbuilderpro" value="{$formrequest_id|intval}"/>
		</form>
	</div>
	{/if}
{/if}