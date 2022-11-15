{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2019 Globo JSC
* @license   please read license in file license.txt
* @link	     http://www.globosoftware.net
*/
*}

{if $start!=$stop}
<ul class="pagination">
	{if $p != 1}
		{assign var='p_previous' value=$p-1}
		<li id="pagination_previous" class="pagination_previous">
			<a href="{$link->goPage($requestPage, $p_previous)|escape:'html':'UTF-8'}" rel="prev">
				<i class="icon-chevron-left"></i> <b>{l s='Previous' mod='gformbuilderpro'}</b>
			</a>
		</li>
	{else}
		<li id="pagination_previous" class="disabled pagination_previous">
			<span>
				<i class="icon-chevron-left"></i> <b>{l s='Previous' mod='gformbuilderpro'}</b>
			</span>
		</li>
	{/if}
	{if $start==3}
		<li>
			<a href="{$link->goPage($requestPage, 1)|escape:'html':'UTF-8'}">
				<span>1</span>
			</a>
		</li>
		<li>
			<a href="{$link->goPage($requestPage, 2)|escape:'html':'UTF-8'}">
				<span>2</span>
			</a>
		</li>
	{/if}
	{if $start==2}
		<li>
			<a href="{$link->goPage($requestPage, 1)|escape:'html':'UTF-8'}">
				<span>1</span>
			</a>
		</li>
	{/if}
	{if $start>3}
		<li>
			<a href="{$link->goPage($requestPage, 1)|escape:'html':'UTF-8'}">
				<span>1</span>
			</a>
		</li>
		<li class="truncate">
			<span>
				<span>...</span>
			</span>
		</li>
	{/if}
	{section name=pagination start=$start loop=$stop+1 step=1}
		{if $p == $smarty.section.pagination.index}
			<li class="active current">
				<span>
					<span>{$p|escape:'html':'UTF-8'}</span>
				</span>
			</li>
		{else}
			<li>
				<a href="{$link->goPage($requestPage, $smarty.section.pagination.index)|escape:'html':'UTF-8'}">
					<span>{$smarty.section.pagination.index|escape:'html':'UTF-8'}</span>
				</a>
			</li>
		{/if}
	{/section}
	{if $pages_nb>$stop+2}
		<li class="truncate">
			<span>
				<span>...</span>
			</span>
		</li>
		<li>
			<a href="{$link->goPage($requestPage, $pages_nb)|escape:'html':'UTF-8'}">
				<span>{$pages_nb|intval}</span>
			</a>
		</li>
	{/if}
	{if $pages_nb==$stop+1}
		<li>
			<a href="{$link->goPage($requestPage, $pages_nb)|escape:'html':'UTF-8'}">
				<span>{$pages_nb|intval}</span>
			</a>
		</li>
	{/if}
	{if $pages_nb==$stop+2}
		<li>
			<a href="{$link->goPage($requestPage, $pages_nb-1)|escape:'html':'UTF-8'}">
				<span>{$pages_nb-1|intval}</span>
			</a>
		</li>
		<li>
			<a href="{$link->goPage($requestPage, $pages_nb)|escape:'html':'UTF-8'}">
				<span>{$pages_nb|intval}</span>
			</a>
		</li>
	{/if}
	{if $pages_nb > 1 AND $p != $pages_nb}
		{assign var='p_next' value=$p+1}
		<li id="pagination_next" class="pagination_next">
			<a href="{$link->goPage($requestPage, $p_next)|escape:'html':'UTF-8'}" rel="next">
				<b>{l s='Next' mod='gformbuilderpro'}</b> <i class="icon-chevron-right"></i>
			</a>
		</li>
	{else}
		<li id="pagination_next" class="disabled pagination_next">
			<span>
				<b>{l s='Next' mod='gformbuilderpro'}</b> <i class="icon-chevron-right"></i>
			</span>
		</li>
	{/if}
</ul>
{/if}