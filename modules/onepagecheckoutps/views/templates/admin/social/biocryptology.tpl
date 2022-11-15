{**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 *}

<div>
    {l s='Go to the' mod='onepagecheckoutps'}
    <a target="_blank" href="https://presteamshop.atlassian.net/wiki/spaces/{$paramsBack.PREFIX_MODULE|escape:'htmlall':'UTF-8'}/pages/{if $paramsBack.ISO_LANG_BACKOFFICE_SHOP eq 'es'}1059127442{else}1087832089{/if}/Biocryptology">{l s='user guide' mod='onepagecheckoutps'}</a> >
    {l s='option "How to create your domain in Biocryptology?"' mod='onepagecheckoutps'}
    <br/><br/>

    <b>* {l s='Post login URLs' mod='onepagecheckoutps'}</b>:
    {foreach $paramsBack.LANGUAGES item='language'}
        <input class="disabled" style="width: 100%;" type="text" value="{$paramsBack.LINK->getModuleLink('onepagecheckoutps', 'login', ['sv' => 'Biocryptology'], null, $language.id_lang)|escape:'htmlall':'UTF-8'}"/>
    {/foreach}
    <br/>
    <b>* {l s='Post logout URLs' mod='onepagecheckoutps'}</b>:
    {foreach $paramsBack.LANGUAGES item='language'}
        <input class="disabled" style="width: 100%;" type="text" value="{$paramsBack.LINK->getPageLink('index', true, $language.id_lang)|escape:'htmlall':'UTF-8'}"/>
    {/foreach}
</div>