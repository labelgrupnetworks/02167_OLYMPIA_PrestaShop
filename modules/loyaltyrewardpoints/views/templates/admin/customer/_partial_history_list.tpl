{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Musaffar Patel <musaffar.patel@gmail.com>
*  @copyright 2016-2021 Musaffar Patel
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of Musaffar Patel
*}

<table class="table">
	<thead>
	<tr>
		<th><span class="title_box ">{l s='Order' mod='loyaltyrewardpoints'}</span></th>
		<th><span class="title_box ">{l s='Points' mod='loyaltyrewardpoints'}</span></th>
		<th><span class="title_box ">{l s='Point Value' mod='loyaltyrewardpoints'}</span></th>
		<th><span class="title_box ">{l s='Type' mod='loyaltyrewardpoints'}</span></th>
		<th><span class="title_box ">{l s='Source' mod='loyaltyrewardpoints'}</span></th>
	</tr>
	</thead>
	<tbody>
	{if $history}
		{foreach from=$history item=history_item}
			<tr>
				<td>{$history_item->reference|escape:'htmlall':'UTF-8'}</td>
				<td>{$history_item->points|escape:'htmlall':'UTF-8'}</td>
				<td>{$history_item->point_value|escape:'htmlall':'UTF-8'}</td>
				<td>{$history_item->type|escape:'htmlall':'UTF-8'}</td>
				<td>{$history_item->source|escape:'htmlall':'UTF-8'}</td>
			</tr>
		{/foreach}
	{/if}
	</tbody>
</table>

{if $pagination}
	{if $pagination.page_total gt 1}
		<div id="lrp-history-pagination" class="lrp-pagination">
			{for $i=1 to $pagination.page_total}
				<a href="" class="page {if $pagination.current_page eq $i}selected{/if}" data-page="{$i|escape:'htmlall':'UTF-8'}">{$i|escape:'htmlall':'UTF-8'}</a>
			{/for}
		</div>
	{/if}
{/if}
