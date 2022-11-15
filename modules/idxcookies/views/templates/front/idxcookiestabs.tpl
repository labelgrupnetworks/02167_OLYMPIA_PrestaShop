{*
* 2018 InnovaDeluxe
*
*  @author      InnovaDeluxe
*  @copyright   InnovaDeluxe
*  @version     4.0.0
*}

<div id="cookieModalList">
    <ul>
        <li class="dlxctab-row active" data-id="info">{l s='Info' mod='idxcookies'}</li>
        {foreach from=$cookies_types item=type}
            <li class="dlxctab-row" data-id="{$type.id_cookie_type|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}">
                {$type.name|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}</li>
        {/foreach}
        <li class="dlxctab-row" data-id="delete">{l s='How to delete cookies' mod='idxcookies'}</li>
    </ul>
</div>
<div id="cookieModalContent">
    <div data-tab="info" class="dlxctab-content">
        <p>
            {$idxrcookies_CookiesInfoText|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}
        </p>
    </div>
    {foreach from=$cookies_types item=type}
        <div data-tab="{$type.id_cookie_type|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}" class="dlxctab-content"
            style="display:none">
            <p class="cookie-content-title">{$type.name|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}</p>
            {if $type.imperative}<p class="always-active"><i class="always-check"></i>
                {l s='Always active' mod='idxcookies'}</p>{/if}
            <p>
                {$type.description|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}
            </p>
            <p class="cookie-content-subtitle">{l s='Cookies used' mod='idxcookies'}</p>
            <ul class="info-cookie-list">
                {foreach from=$type.cookies item=cookie}
                    <li><label
                            for="switch{$cookie.id_cookie|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}"><span>{$cookie.domain|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}</span>
                            - {$cookie.name|escape:'UTF-8'|htmlspecialchars_decode}</label>
                        <input name="switch{$cookie.id_cookie|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}"
                            data-modulo="{$cookie.module|escape:'htmlall':'UTF-8'}"
                            data-template="{$cookie.id_template|escape:'htmlall':'UTF-8'}"
                            data-idcookie="{$cookie.id_cookie|escape:'htmlall':'UTF-8'}" class="switch"
                            {if $cookiesSelected || $type.imperative}checked{/if} {if $type.imperative}disabled{/if}>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/foreach}
    <div data-tab="delete" class="dlxctab-content" style="display:none">
        <p>
            {$idxrcookies_CookiesDeleteCookiesText|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}
        </p>
    </div>
</div>