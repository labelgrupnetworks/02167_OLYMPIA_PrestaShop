{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

<div class="publish_box">
    <div class="box-heading">
        <i class="icon-envelope"></i> {l s='Received data' mod='gformbuilderpro'} - <i class="icon-calendar-o"></i> {$date_add|escape:'html':'UTF-8'}{if isset($user_ip) && $user_ip !=''} - <i class="icon-map-marker"></i> {l s='Ip Address: ' mod='gformbuilderpro'}{$user_ip|escape:'html':'UTF-8'}{/if}
    </div>
    <div class="gbox_content">
        <div class="publish_box">
            <div class="box-heading"><i class="icon-envelope"></i>{$subject|escape:'html':'UTF-8'}</div>
            <div class="gbox_content">
                {if isset($isps17) && $isps17}
                    {$request nofilter}{* $request is html content, no need to escape*}
                {else}
                    {$request}{* $request is html content, no need to escape*}
                {/if}
                <hr />
            <div class="form-group">
        		<label class="control-label col-lg-2" for="gformbuilderpro_change_status">{l s='Change status'  mod='gformbuilderpro'}</label>
        		<div class="col-lg-3">
        			<select name="change_status" id="gformbuilderpro_change_status" rel="{$idrequest|intval}">
                        {if $statuses_array}
                            {foreach $statuses_array as $key=> $_status}
                                <option {if isset($status) && $status == $key} selected="selected"{/if} value="{$key|intval}">{$_status|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        {/if}
        			</select>
        		</div>
        	</div>
            </div>
        </div>
        <div style="clear:both;"></div>
    {if isset($attachfiles) && $attachfiles}
        <div class="publish_box">
            <div class="box-heading"><i class="icon-cloud-download"></i> {l s='Attachments' mod='gformbuilderpro'}</div>
            <div class="gbox_content">
            {foreach $attachfiles as $file}
                <a href="{$requestdownload|escape:'html':'UTF-8'}{$file.name|escape:'html':'UTF-8'}" class="btn btn-default" title="{l s='Click to download' mod='gformbuilderpro'}">
                    {if $file.isImage}
                        <img class="request_img" src="../upload/{$file.name|escape:'html':'UTF-8'}" alt="" />
                    {/if}
						<i class="icon-cloud-download"></i>{$file.name|escape:'html':'UTF-8'}</a>
            {/foreach}
            </div>
        </div>
        <div style="clear:both;"></div>
    {/if}
        <div class="publish_box"  style="margin-top: 10px;">
            <div class="box-heading"><i class="icon-reply"></i>{l s='Reply'  mod='gformbuilderpro'}</div>
            <div class="gbox_content">
        {if $replys}
            <table class="table">
            {foreach $replys as $reply}
                <tr>
                    <td>{$reply.date_add|date_format:'%Y-%m-%d %H:%M:%S'}</td>
                    <td>
                        <p>{l s='To'  mod='gformbuilderpro'}: {$reply.replyemail|escape:'html':'UTF-8'}</p>
                        <p>{l s='Subject'  mod='gformbuilderpro'}:{$reply.subject|escape:'html':'UTF-8'}</p><hr />
                        <div class="reply_request">
                            {if isset($isps17) && $isps17}
                                {$reply.request nofilter}{* $request is html content, no need to escape*}
                            {else}
                                {$reply.request}{* $request is html content, no need to escape*}
                            {/if}
                        </div>
                    </td>
                </tr>
            {/foreach}
            </table>
        {/if}
            
        <div class="publish_box" id="replyform">
        <div class="gbox_content">
        <form action="" method="POST" name="gformbuilderpro_reply_form">
            <input type="hidden" name="idrequest" value="{$idrequest|intval}" />
            <input type="hidden" name="gid_lang" value="{$gid_lang|intval}" />
            <div class="form-group gformbuilderpro_reply_wp" style="margin-top: 10px;">
                <label class="control-label col-lg-2" for="gformbuilderpro_reply_to">{l s='To' mod='gformbuilderpro'}</label>
                <div class="col-lg-8"><input type="text" id="gformbuilderpro_reply_to" name="gformbuilderpro_reply_to" value="{if (isset($sender) && $sender !='')}{$sender|escape:'html':'UTF-8'}{else}{$user_email|escape:'html':'UTF-8'}{/if}" class="" /></div>
            </div><div style="clear:both;"></div>
            <div class="form-group gformbuilderpro_reply_wp" style="margin-top: 10px;">
                <label class="control-label col-lg-2" for="gformbuilderpro_reply_subject">{l s='Subject' mod='gformbuilderpro'}</label>
                <div class="col-lg-8">
                    <textarea id="gformbuilderpro_reply_subject" name="gformbuilderpro_reply_subject" class="">{if isset($reply_subject) && $reply_subject !=''}{$reply_subject|escape:'html':'UTF-8'}{/if}</textarea>
                </div>
            </div><div style="clear:both;"></div>
            <div class="form-group gformbuilderpro_reply_wp" style="margin-top: 10px;">
                <label class="control-label col-lg-2" for="gformbuilderpro_change_status">{l s='Message' mod='gformbuilderpro'}</label>
                <div class="col-lg-8">
                    <textarea id="gformbuilderpro_reply" name="gformbuilderpro_reply" class="rte autoload_rte gautoload_rte"></textarea>
                </div>
            </div>
            <div style="clear:both;"></div>
            <div class="form-group gformbuilderpro_reply_wp" style="margin-top: 10px;">
                <label class="control-label col-lg-2" for="gformbuilderpro_change_status"></label>
                <div class="col-lg-8">
                    <button type="submit" class="btn btn-primary submit_reply"><i class="icon-reply"></i>{l s='Reply'  mod='gformbuilderpro'}</button>
                </div>
            </div>
        </form><div style="clear:both;"></div>
        <div style="clear:both;"></div>
        </div><div style="clear:both;"></div>
        <div style="clear:both;"></div>
        </div>
    </div></div>
    <div style="clear:both;"></div>
    <div class="panel-footer">
		<a href="{$backurl|escape:'html':'UTF-8'}" class="btn btn-default" title="{l s='Back to list' mod='gformbuilderpro'}"><i class="process-icon-back"></i>{l s='Back to list' mod='gformbuilderpro'}</a>
	</div>
</div>
</div>