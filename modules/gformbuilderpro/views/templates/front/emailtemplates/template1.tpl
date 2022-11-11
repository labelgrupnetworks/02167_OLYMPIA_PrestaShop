{*
* Do not edit the file if you want to upgrade the module in future.
*
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

<table style="width: 100%;">
    <tbody>
    <tr>
        <td style="background: #9C9CE8;width:50px"></td>
        <td style="padding: 40px 20px;">
            <table style="width: 100%;">
                <tbody>
                {literal}
                <tr>
                    <td colspan="2" class="logo" style="border-bottom:2px dashed #D7D7FC;padding:7px 0;">
                        <a title="{shop_name}" href="{shop_url}" style="color:#337ff1"><img src="{shop_logo}" alt="{shop_name}" /></a>
                        {/literal}
                        <p>{literal}{shop_name}{/literal}</p>
                        <p></p>
                        {if isset($datasreply) && $datasreply}
                            <p>{l s='Hi ' mod='gformbuilderpro'} {literal}{sender_email}{/literal}!</p>
                        {else}
                            {if isset($fieldsData) && $fieldsData}
                                <p>{l s='Hi !' mod='gformbuilderpro'}</p>
                                <p>{l s='You have just received a new submission of the "' mod='gformbuilderpro'}{literal}{form_title}{/literal}{l s='" form' mod='gformbuilderpro'}</p>
                            {elseif isset($datassender) && $datassender}
                                <p>{l s='Hi ' mod='gformbuilderpro'} {literal}{sender_email}{/literal}!</p>
                            {/if}
                        {/if}
                        {literal}
                    </td>
                </tr>
                    <tr>
                        <td colspan="2" class="space_footer" style="padding:5px 0!important"></td>
                    </tr>
                {/literal}
                {if isset($datasreply) && $datasreply}
                    <tr>
                        <td colspan="2">
                        {literal}{reply_message}{/literal}
                        </td>
                    </tr>
                {else}
                    {if isset($fieldsData) && $fieldsData}
                        {foreach $fieldsData as $field}
                            {if $field.type !='html' && $field.type !='captcha' && $field.type !='submit' && $field.type !='googlemap' && $field.type !='privacy'}
                                <tr style="background:{cycle values="#fcfcfc,#f0f0f0"};">
                                    <td style="width:40%;padding:0!important"><strong>{$field.label|escape:'html':'UTF-8'}</strong></td>
                                    <td style="width:60%;padding:0!important">{literal}{{/literal}{$field.name|escape:'html':'UTF-8'}{literal}}{/literal}</td>
                                </tr>
                            {/if}
                        {/foreach}
                    {elseif isset($datassender) && $datassender}
                        <tr>
                            <td colspan="2">
                                <h3>{l s='Thank you for your request!' mod='gformbuilderpro'}</h3>
                                <p>{l s='We appreciate that you\'ve taken the time to write us. We\'ll get back to you very soon. Please come back and see us often.' mod='gformbuilderpro'}</p>
                            </td>
                        </tr>
                    {/if}
                {/if}
                {literal}
                    <tr>
                        <td colspan="2" class="space_footer" style="padding:5px 0!important"></td>
                    </tr>
                <tr>
                    <td colspan="2" style="border-top:2px dashed #D7D7FC;padding:10px 0!important">
                        <a href="{shop_url}" title="{shop_name}"><strong>{shop_name}</strong></a>
                        <p>{/literal}{l s='Email' mod='gformbuilderpro'}: {literal}{shop_email}</p>
                        <p>{/literal}{l s='Address' mod='gformbuilderpro'}: {literal}{shop_address}</p>
                        <p>{/literal}{l s='Phone' mod='gformbuilderpro'}: {literal}{shop_phone}</p>
                        <p>{/literal}{l s='Fax' mod='gformbuilderpro'}: {literal}{shop_fax}</p>
                    </td>
                </tr>
                {/literal}
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>