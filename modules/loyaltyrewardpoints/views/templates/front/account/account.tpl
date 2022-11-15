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

{extends file='customer/page.tpl'}

{block name='page_title'}
	{l s='Loyalty Reward Points' mod='loyaltyrewardpoints'}
{/block}

{block name='page_content'}

	<div id="lrp-customer-account">
		<div class="col-xs-12">
			<div class="row">
				<div class="points-card card col-xs-12 {if $referral_enabled eq 1}col-sm-6 col-md-4{/if}">
					<div>
						<span style="font-size:50px;"><strong>{(int)$points_available|escape:'htmlall':'UTF-8'}</strong></span><br>
						{l s='Points available' mod='loyaltyrewardpoints'}
					</div>
				</div>

				{if $referral_enabled eq 1}
					<div class="points-card card details col-xs-12 col-sm-6 col-md-8">
						<div>
							<span style="font-size:20px;"><strong>{l s='share with a friend...' mod='loyaltyrewardpoints'}</strong></span><br>
							{l s='Earn [1]%d reward points[/1]' sprintf=[$referral_points] tags=['<strong>'] mod='loyaltyrewardpoints'}<br>
							{l s='and your friend will get [1]%d reward points[/1] off their first order!' sprintf=[$referral_friend_points] tags=['<strong>'] mod='loyaltyrewardpoints'}<br>
							{l s='..that\'s %s for you and %s for them!' sprintf=[$referral_points_value, $referral_friend_points_value] mod='loyaltyrewardpoints'}<br>
							{l s='share the link:' mod='loyaltyrewardpoints'} <a href="{$referral_link|escape:'htmlall':'UTF-8'}">{$referral_link|escape:'htmlall':'UTF-8'}</a>
						</div>
					</div>
				{/if}
			</div>
		</div>

		<h6>{l s='Below is a list of loyalty points history related to your account' mod='loyaltyrewardpoints'}</h6>

		{if !empty($lrp_history)}
			<table class="table table-striped table-bordered table-labeled hidden-sm-down">
				<thead class="thead-default">
				<tr>
					<th>{l s='Order reference' mod='loyaltyrewardpoints'}</th>
					<th>{l s='Points' mod='loyaltyrewardpoints'}</th>
					<th>{l s='Points Value' mod='loyaltyrewardpoints'}</th>
					<th>{l s='Action' mod='loyaltyrewardpoints'}</th>
					<th>{l s='Date' mod='loyaltyrewardpoints'}</th>
					{if $expiry_enabled}
						<th>{l s='Expiry' mod='loyaltyrewardpoints'}</th>
					{/if}
				</tr>
				</thead>
				<tbody>
					{foreach from=$lrp_history item=history}
					<tr>
						<th scope="row">{$history->reference|escape:'htmlall':'UTF-8'}</th>
						<td>{$history->points|escape:'htmlall':'UTF-8'}</td>
						<td>{$history->points_value|escape:'htmlall':'UTF-8'}</td>
						<td>
							{if $history->type eq 1}
								{l s='Rewarded' mod='loyaltyrewardpoints'}
							{else}
								{l s='Redeemed' mod='loyaltyrewardpoints'}
							{/if}
						</td>
						<td>{$history->date_add_formatted|escape:'htmlall':'UTF-8'}</td>
						{if $expiry_enabled}
							<td>
								{if $history->expired eq 1}
									{l s='Expired on' mod='loyaltyrewardpoints'} {$history->expires_date_formatted|escape:'htmlall':'UTF-8'}
								{else}
									{$history->expires_date_formatted|escape:'htmlall':'UTF-8'}
								{/if}
							</td>
						{/if}
					</tr>
					{/foreach}
				</tbody>
			</table>

			<table class="table table-striped table-bordered table-labeled hidden-sm-up">
				<tbody>
                {foreach from=$lrp_history item=history}
                    <tr>
                        <td style="font-weight: bold">
                            {l s='Order reference' mod='loyaltyrewardpoints'} : {$history->reference|escape:'htmlall':'UTF-8'}
                            ({$history->date_add_formatted|escape:'htmlall':'UTF-8'})
                        </td>
                    </tr>
                    <tr>
						<td>
							<strong>
								{$history->points|escape:'htmlall':'UTF-8'} {l s='points' mod='loyaltyrewardpoints'}
								{if $history->type eq 1}
									{l s='Rewarded' mod='loyaltyrewardpoints'}
								{else}
									{l s='Redeemed' mod='loyaltyrewardpoints'}
								{/if}
							</strong><br>
							{l s='Points Value' mod='loyaltyrewardpoints'} : {$history->points_value|escape:'htmlall':'UTF-8'}<br>
							{if $expiry_enabled}
								{if $history->expired eq 1}
									{l s='Expired on' mod='loyaltyrewardpoints'} {$history->expires_date_formatted|escape:'htmlall':'UTF-8'}
								{else}
                                    {l s='Expires' mod='loyaltyrewardpoints'} :
									{$history->expires_date_formatted|escape:'htmlall':'UTF-8'}
								{/if}
							{/if}
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		{/if}
	</div>
{/block}