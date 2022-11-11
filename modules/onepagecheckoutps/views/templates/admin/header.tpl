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

{foreach from=$paramsBack.JS_FILES item="file"}
    <script type="text/javascript" src="{$file|escape:'htmlall':'UTF-8'}"></script>
{/foreach}
{foreach from=$paramsBack.CSS_FILES item="file"}
    <link type="text/css" rel="stylesheet" href="{$file|escape:'htmlall':'UTF-8'}"/>
{/foreach}

<script type="text/javascript">
    
    var url_contact_addons = 'https://addons.prestashop.com/en/write-to-developper?id_product=8503';
    var url_opinions_addons = 'http://addons.prestashop.com/ratings.php';
    var iso_lang_backoffice_shop = '{$paramsBack.ISO_LANG_BACKOFFICE_SHOP|escape:'htmlall':'UTF-8'}';

    var remote_addr = '{$paramsBack.remote_addr|escape:'htmlall':'UTF-8'}';

    var module_dir = "{$paramsBack.MODULE_DIR|escape:'htmlall':'UTF-8'}";
    var module_img = "{$paramsBack.MODULE_IMG|escape:'htmlall':'UTF-8'}";
    var pts_static_token = '{$paramsBack.OPC_STATIC_TOKEN|escape:'htmlall':'UTF-8'}';
    var class_name = 'App{$paramsBack.MODULE_PREFIX|escape:'htmlall':'UTF-8'}';

    //status codes
    var ERROR_CODE = {$paramsBack.ERROR_CODE|intval};
    var SUCCESS_CODE = {$paramsBack.SUCCESS_CODE|intval};

    var onepagecheckoutps_dir = '{$paramsBack.MODULE_DIR|escape:'htmlall':'UTF-8'}';
    var onepagecheckoutps_img = '{$paramsBack.MODULE_IMG|escape:'htmlall':'UTF-8'}';
    var GLOBALS_JS = {$paramsBack.GLOBALS_JS|escape:'quotes':'UTF-8'};
    var id_language_default = Number({$paramsBack.DEFAULT_LENGUAGE|intval});
    var iso_lang_backoffice_shop = '{$paramsBack.iso_lang_backoffice_shop|escape:'htmlall':'UTF-8'}';

    //languages
    var id_language = {$paramsBack.DEFAULT_LENGUAGE|intval};
    var languages = [];
    var languages_iso = [];
    var languages_name = [];

    {foreach from=$paramsBack.LANGUAGES item=language name=f_languages}
        languages.push({$language.id_lang|intval});
        languages_iso.push('{$language.iso_code|escape:'htmlall':'UTF-8'}');
        languages_name.push('{$language.name|escape:'htmlall':'UTF-8'}');
    {/foreach}
    var static_token = '{$paramsBack.STATIC_TOKEN|escape:'htmlall':'UTF-8'}';

    var actions_controller_url = '{$paramsBack.ACTIONS_CONTROLLER_URL|escape:'quotes':'UTF-8'}';
</script>

<script type="text/javascript">
    var Msg = {ldelim}
        update_ship_to_pay: {ldelim}
            off: "{l s='Updating association...' mod='onepagecheckoutps' js=1}",
            on: "{l s='Update' mod='onepagecheckoutps' js=1}"
        {rdelim},
        change: "{l s='Change' mod='onepagecheckoutps' js=1}",
        only_gif: "{l s='Only gif images are allowed.' mod='onepagecheckoutps' js=1}",
        select_file: "{l s='You must select one file.' mod='onepagecheckoutps' js=1}",
        edit_field: "{l s='Edit field.' mod='onepagecheckoutps' js=1}",
        new_field: "{l s='New field.' mod='onepagecheckoutps' js=1}",
        confirm_remove_field: "{l s='Are you sure to want remove this field?' mod='onepagecheckoutps' js=1}",
        cannot_remove_field: "{l s='Only custom fields can be removed' mod='onepagecheckoutps' js=1}",
        manage_field_options: "{l s='Manage field options' mod='onepagecheckoutps' js=1}",
        add_IP: "{l s='Add IP' mod='onepagecheckoutps' js=1}",
        required_default_country: "{l s='The default value of this field can not be empty, you must enter the ID of a country.' mod='onepagecheckoutps' js=1}",
        required_description: "{l s='You must enter a description for at least the default language.' mod='onepagecheckoutps' js=1}",
        chart_title: "{l s='Connections number per social network' mod='onepagecheckoutps' js=1}",
        credits_developed: "{l s='Developed by => PresTeamShop' mod='onepagecheckoutps' js=1}",
        credits: "{l s='User guide module' mod='onepagecheckoutps' js=1}",
        credits_spanish: "{l s='User guide module (Spanish)' mod='onepagecheckoutps' js=1}",
        credits_english: "{l s='User guide module (English)' mod='onepagecheckoutps' js=1}",
        credits_development: "{l s='Development of templates, modules and custom modifications.' mod='onepagecheckoutps' js=1}",
        credits_information: "{l s='Information' mod='onepagecheckoutps' js=1}",
        credits_support: "{l s='Support' mod='onepagecheckoutps' js=1}",
        credits_website: "{l s='Web site & Store' mod='onepagecheckoutps' js=1}",
        ctopc_enable: "{l s='The Customer Type OPC module eliminates the operation of this field' mod='onepagecheckoutps' js=1}",
        credits_suggestions: "{l s='Suggestions, bugs or problems to report here!' mod='onepagecheckoutps' js=1}",
        info_require_id_state_field: "{l s='This field is required if within the configuration of the selected country, the option - Contains states - is active' mod='onepagecheckoutps' js=1}",
        info_require_postcode_field: "{l s='This field is required if within the configuration of the selected country, the option - Does it need Zip / postal code? - is active' mod='onepagecheckoutps' js=1}",
        info_active_id_state_field: "{l s='This field is shown if within the configuration of the selected country, the option - Contains states - is active' mod='onepagecheckoutps' js=1}",
        info_active_postcode_field: "{l s='This field is shown if within the configuration of the selected country, the option - Do you need Zip / postal code? - is active' mod='onepagecheckoutps' js=1}",
        info_dni_field: "{l s='For it to work correctly you must desactivate the option - Do you need a tax identification number? - in country settings' mod='onepagecheckoutps' js=1}"
    {rdelim};
</script>

{if $paramsBack.ERRORS}
    {foreach from=$paramsBack.ERRORS item='warning'}
        <div class="alert alert-danger">
            {$warning|escape:'quotes':'UTF-8'}
        </div>
    {/foreach}
{/if}
{if $paramsBack.WARNINGS}
    {foreach from=$paramsBack.WARNINGS item='warning'}
        <div class="alert alert-warning">
            {$warning|escape:'quotes':'UTF-8'}
        </div>
    {/foreach}
{/if}