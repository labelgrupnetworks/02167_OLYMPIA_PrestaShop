{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if isset($_errors) && $_errors}
<div class="alert alert-danger" id="create_account_error">
    <ol>
    {foreach $_errors as $_error}
        <li>{$_error|escape:'html':'UTF-8'}</li>
    {/foreach}
    </ol>
</div>
{else}
<div id="thankyou-page">
    {if isset($errors) && $errors!='1'}
        <div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{l s='Your message has been successfully sent to our team.' mod='gformbuilderpro'}
		</div>
    {/if}
    {if isset($success_message) && $success_message}
        {$success_message}{* $success_message is html content, no need to escape*}
    {/if}
    {if isset($autoredirect) && $autoredirect}
    {literal}
        <script type="text/javascript">
            $(document).ready(function(){
                setTimeout(function () {
                   window.location.href = "{/literal}{$redirect_link|escape:'html':'UTF-8'}{literal}";
                }, {/literal}{$timedelay|intval}{literal});
            });
        </script>
    {/literal}
    {/if}
</div>
{/if}