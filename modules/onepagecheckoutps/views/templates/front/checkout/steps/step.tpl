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

<section
    id="opc_step_{$name|escape:'htmlall':'UTF-8'}"
    class="opc-step"
    data-step="{$name|escape:'htmlall':'UTF-8'}"
>
    <div class="card">
        {if isset($title)}
            <div id="opc_step_{$name|escape:'htmlall':'UTF-8'}_header"
                {if $accordion}
                    class="opc-step-header collapsed"
                    data-bs-toggle="collapse"
                    data-bs-target="#opc_step_{$name|escape:'htmlall':'UTF-8'}_body"
                    aria-controls="opc_step_{$name|escape:'htmlall':'UTF-8'}_body"
                    aria-expanded="false"
                {else}
                    class="opc-step-header"
                {/if}
            >
                <h5 class="mb-0 {if $accordion}accordion{/if}">
                    {$title|escape:'htmlall':'UTF-8'}
                </h5>
                <span class="line-title"></span>
            </div>
        {/if}

        <div id="opc_step_{$name|escape:'htmlall':'UTF-8'}_body"
            {if $accordion}
                class="collapse"
                aria-labelledby="opc_step_{$name|escape:'htmlall':'UTF-8'}_header"
                data-bs-parent="#opc_steps_content"
            {/if}
        >
            <div id="opc_step_{$name|escape:'htmlall':'UTF-8'}_content" class="opc-step-content">
                {if isset($render)}
                    {$render nofilter}
                {/if}
            </div>

            {if isset($nameBackStep) or isset($nameNextStep)}
                <div id="opc_step_{$name|escape:'htmlall':'UTF-8'}_footer" class="opc-step-footer">
                    {if isset($nameNextStep)}
                        <span data-step="{$nameNextStep|escape:'htmlall':'UTF-8'}" class="continue btn btn-primary">
                            {$labelNextStep|escape:'htmlall':'UTF-8'}
                        </span>
                    {/if}
                    {if isset($nameBackStep)}
                        <span data-step="{$nameBackStep|escape:'htmlall':'UTF-8'}" class="return_step">
                            <i class="material-icons">chevron_left</i>
                            {$labelBackStep|escape:'htmlall':'UTF-8'}
                        </span>
                    {/if}
                </div>
            {/if}
        </div>
    </div>
</section>