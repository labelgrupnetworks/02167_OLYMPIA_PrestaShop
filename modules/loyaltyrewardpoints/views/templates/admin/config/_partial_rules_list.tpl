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

<div class="panel col-lg-12">
	<div class="panel-heading">
		{l s='Loyalty Reward Point Rules' mod='loyaltyrewardpoints'}
	</div>
	<table class="table">
		<thead>
			<tr class="nodrag nodrop">
				<th class="fixed-width-xs">
					<span class="title_box active">{l s='Name' mod='loyaltyrewardpoints'}</span>
				</th>
				<th class="fixed-width-xs">
					<span class="title_box active">{l s='Impact Type' mod='loyaltyrewardpoints'}</span>
				</th>
				<th class="fixed-width-xs">
					<span class="title_box active">{l s='Points Impact' mod='loyaltyrewardpoints'}</span>
				</th>
				<th class="fixed-width-xs">
					<span class="title_box active">{l s='Status' mod='loyaltyrewardpoints'}</span>
				</th>
				<th class="fixed-width-xs" style="text-align:right">
					<span class="title_box active">{l s='Action' mod='loyaltyrewardpoints'}</span>
				</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$lrp_rules item=rule}
			<tr>
				<td class="pointer">{$rule->name|escape:'htmlall':'UTF-8'}</td>
				<td>
					{if $rule->operator eq '+'}
						{l s='Add Points' mod='loyaltyrewardpoints'}
					{/if}
					{if $rule->operator eq '*'}
						{l s='Multiply Points' mod='loyaltyrewardpoints'}
					{/if}
					{if $rule->operator eq '='}
						{l s='Set Points' mod='loyaltyrewardpoints'}
					{/if}
				</td>
				<td>
					{$rule->points|escape:'htmlall':'UTF-8'}
				</td>
				<td>
					{if $rule->enabled eq 1}
						{l s='Enabled' mod='loyaltyrewardpoints'}
					{else}
						{l s='Disabled' mod='loyaltyrewardpoints'}
					{/if}
				</td>
				<td class="text-right">
					<div class="btn-group-action">
						<div class="btn-group pull-right">
							<a href="" title="Edit" class="edit btn btn-default lrp-rule-edit" data-id_lrp_rule="{$rule->id_lrp_rule|escape:'htmlall':'UTF-8'}">
								<i class="icon-pencil"></i> {l s='Edit' mod='loyaltyrewardpoints'}
							</a>
							<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<i class="icon-caret-down"></i>&nbsp;
							</button>

							<ul class="dropdown-menu">
								<li class="divider"></li>
								<li>
									<a href="" title="Delete" class="delete lrp-rule-delete" data-id_lrp_rule="{$rule->id_lrp_rule|escape:'htmlall':'UTF-8'}">
										<i class="icon-trash"></i> {l s='Delete' mod='loyaltyrewardpoints'}
									</a>
								</li>
							</ul>
						</div>
					</div>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>