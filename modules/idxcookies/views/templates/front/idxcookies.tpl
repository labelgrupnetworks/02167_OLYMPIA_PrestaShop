{*
* 2015 InnovaDeluxe
*
*  @author      InnovaDeluxe
*  @copyright   InnovaDeluxe
*  @version     2.8.0
*}
<!-- MODULE idxcookies -->

<div id="contentidxrcookies" style="display:none;">
    <div id="idxrcookies">
        <div id="divPosition"{if $idxrcookiesConf.rejectButton} class="withRejectButton"{/if}>
            <div class="contenido">
                <div class="container">
                    <div id="textDiv"></div>
                    <div id="buttons" >
                        <a class="btn-cookies" id="idxrcookiesOK" rel="nofollow"></a>
                        <a class="btn-cookies" id="idxrcookiesPartial" rel="nofollow"></a>
                        <a class="btn-cookies" id="idxrcookiesKO" rel="nofollow"></a>
                        <a class="btn-cookies" id="cookiesConf" rel="nofollow">
                        <span class="cookies-conf">{l s='Cookies configuration' mod='idxcookies'}</span>
                        <span class="cookies-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                            <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                            <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                            </svg>
                        </span>
                        </a>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>

<!-- cookie modal -->
<div id="cookieConfigurator" style="display:none;">
    <div id='cookieModal'>
        <div id='cookieModalHeader'>
            <img src="{$module_dir|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}views/img/conf.png"/>{l s='Cookie preferences' mod='idxcookies'}
        </div>  
        <div id='cookieModalBody'>
            {$idxrcookiesConf.cookiesTabs|escape:'htmlall':'UTF-8'|htmlspecialchars_decode nofilter}
        </div>
        <div id='cookieModalFooter'>
            <a class="cookie-info-page" rel="nofollow" href="{$idxrcookiesConf.cookiesUrl|urldecode|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}">{$idxrcookiesConf.cookiesUrlTitle|escape:'htmlall':'UTF-8'}</a>
            <a class="btn-config" rel="nofollow" id="js-save-cookieconf">{l s='Save' mod='idxcookies'}</a>
        </div>
    </div>
</div>
<!-- end cookie modal -->
{if $idxrcookiesConf.audit}
<div id="contentDeluxecookiesAudit" style="display:none;">
    <div class="background-progress">
        <div class="progress-container" >
            <img class="loading" src="{$module_dir|escape:'htmlall':'UTF-8'|htmlspecialchars_decode}views/img/search-icon.png"/>
            <p class="progress-title">{l s='Auditing page' mod='idxcookies'}</p>
            <div class="progress">
                <div class="progress-bar progress-bar-success bg-success" role="progressbar" aria-valuenow="0"
                     aria-valuemin="0" aria-valuemax="100" style="width:0%">
                    <p id="audit-progress-text"><strong>0%</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}

{if $idxrcookiesConf.fixedButton}
<div class="cookie-button cookie-button-{$idxrcookiesConf.buttonPosition|escape:'htmlall':'UTF-8'}" title="{l s='Cookie configuration' mod='idxcookies'}">
    <img class="cookie-trigger cookiesConfButton" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/cookie.png"/>
</div>
{/if}
