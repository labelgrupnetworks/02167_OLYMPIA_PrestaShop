{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if isset($is_left_tab) && $is_left_tab && isset($is_close_tab) && $is_close_tab}
	</div>
{else}
    {if isset($is_left_tab) && $is_left_tab}
		<div class="col-lg-2 is_left_tab">
	{/if}
    <div class="productTabs gformbuilderpro_admintab">
		<ul class="tab nav nav-tabs">
			<li class="tab-row active">
				<a class="tab-page" href="#tabmain"><i class="icon-link"></i>{l s='Setting' mod='gformbuilderpro'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" href="#apitab"><i class="icon-cogs"></i>{l s='Integrations' mod='gformbuilderpro'}</a>
			</li>
			<li class="tab-row">
				<a class="tab-page" href="#blacklisttab"><i class="icon-hide"></i>{l s='Backlist Ip' mod='gformbuilderpro'}</a>
			</li>
		</ul>
	</div>
	{if isset($is_left_tab) && $is_left_tab}
		</div><div class="col-lg-10">
	{/if}
{/if}