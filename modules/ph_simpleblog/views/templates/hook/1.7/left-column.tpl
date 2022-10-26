{*
* 2007-2018 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="block-categories hidden-sm-down">
    <ul class="category-top-menu">
    	<li><a class="text-uppercase h6" href="{ph_simpleblog::getLink()}" title="{l s='Blog' mod='ph_simpleblog'}">{l s='Blog' mod='ph_simpleblog'}</a></li>
        <li>
            <ul class="category-sub-menu">
        	{foreach $categories AS $category}
				<li><a href="{$category['url']}" title="{l s='Link to' mod='ph_simpleblog'} {$category['name']}">{$category['name']}</a>
					{if isset($category['childrens'])}
					<div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#blog_subcategory_{$category['id']}"><i class="material-icons add"></i><i class="material-icons remove"></i></div>
					<div class="collapse" id="blog_subcategory_{$category['id']}">
						<ul class="category-sub-menu">
							{foreach $category['childrens'] as $child_category}
							<li>
								<a class="category-sub-link" href="{$child_category['url']}" title="{l s='Link to' mod='ph_simpleblog'} {$child_category['name']}">
									{$child_category['name']}
								</a>
							</li>
							{/foreach}
						</ul>
					</div>
					{/if}
				</li>
			{/foreach}
            </ul>
        </li>
    </ul>
</div>
{* <div id="ph_simpleblog_categories" class="block informations_block_left">
	<p class="title_block"><a href="{ph_simpleblog::getLink()}" title="{l s='Blog' mod='ph_simpleblog'}">{l s='Blog' mod='ph_simpleblog'}</a></p>
	<div class="block_content list-block">
		<ul>
			{foreach $categories AS $category}
				<li><a href="{$category['url']}" title="{l s='Link to' mod='ph_simpleblog'} {$category['name']}">{$category['name']}</a>
					{if isset($category['childrens'])}
					<ul class="child_categories">
						{foreach $category['childrens'] as $child_category}
						<li><a href="{$link->getModuleLink('ph_simpleblog', 'category', ['sb_category' => $child_category.link_rewrite])}" title="{l s='Link to' mod='ph_simpleblog'} {$child_category['name']}">{$child_category['name']}</a>
						{/foreach}
					</ul>
					{/if}
				</li>
			{/foreach}
		</ul>
	</div>
</div> *}