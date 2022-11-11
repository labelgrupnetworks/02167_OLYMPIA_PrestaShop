{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'gformproduct'}
    	<div class="col-lg-9">
    		<div class="row">
    			<div class="col-lg-6">
                    <input type="hidden" class="gvalidate gvalidate_isRequired" name="extra" id="inputPackItems" value="{$fields_value['extra']['products']|escape:'html':'UTF-8'}"/>
                    <input type="hidden" id="ajaxaction" value="{$fields_value['ajaxaction']|escape:'html':'UTF-8'}" />
    				<input type="text" id="curPackItemName" name="curPackItemName" class="form-control" />
                    <input type="hidden" id="curPackItemId" name="curPackItemId" class="form-control" />
    			</div>
    			<div class="col-lg-2">
    				<button type="button" id="add_pack_item" class="btn btn-default">
    					<i class="icon-plus-sign-alt"></i> {l s='Add' mod='gformbuilderpro'}
    				</button>
    			</div>
                <br />
                <div id="divPackItems" class="col-lg-12 {if isset($fields_value['loadjqueryselect2']) && $fields_value['loadjqueryselect2'] !='1'} get_product_version_old {/if}">
                    {if isset($fields_value['extra']['html']) && $fields_value['extra']['html']}
                        {foreach $fields_value['extra']['html'] as $html}
                            <div class="product-pack-item media-product-pack" data-product-name="{$html.name|escape:'html':'UTF-8'}" data-product-id="{$html.id|escape:'html':'UTF-8'}">
                                <span class="media-product-pack-title">#{$html.id|escape:'html':'UTF-8'}: {$html.name|escape:'html':'UTF-8'}</span>
                                <button type="button" class="btn btn-default delGformproductItem media-product-pack-action" data-delete="{$html.id|escape:'html':'UTF-8'}"><i class="icon-trash"></i></button>
                            </div>
                        {/foreach}
                    {/if}
                </div>
    		</div>
    	</div>
     {else if $input.type =='multival'}
        <div class="col-lg-9">
            <div class="panel">
                <div class="multival_box" id="multival_{$input.name|escape:'html':'UTF-8'}" rel="{$input.name|escape:'html':'UTF-8'}">
                    <div id="{$input.name|escape:'html':'UTF-8'}_multival_newval" class="multival_newval">
                        <div style="display:none;" id="value_invalid" class="alert alert-danger" role="alert"><p>{l s='Value invalid' mod='gformbuilderpro'}</p></div>
                        <div class="form-group">
                        {foreach $languages as $language}
                                <div class="translatable-field col-lg-12  lang-{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
                                    <div class="col-lg-6">
                                        <input type="text" value="" class="multival_newval_{$language.id_lang|escape:'html':'UTF-8'}" />
                                    </div>
                                    {if $languages|count > 1}
                    					<div class="col-lg-2">
                    						<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                    							{$language.iso_code|escape:'html':'UTF-8'}
                    							<span class="caret"></span>
                    						</button>
                    						<ul class="dropdown-menu">
                    							{foreach from=$languages item=lang}
                    							<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$lang.name|escape:'html':'UTF-8'}</a></li>
                    							{/foreach}
                    						</ul>
                    					</div>
                    				{/if}
                                    <div class="col-lg-4">
                                        <a  class="add_multival_newval btn btn-default"><i class="icon-save"></i><span class="addlabel">{l s='Add' mod='gformbuilderpro'}</span><span class="updatelabel">{l s='Update' mod='gformbuilderpro'}</span></a>
                                        <a  class="cancel_multival_newval btn btn-danger" style="display:none;"><i class="icon-remove"></i></a>
                                    </div>
                                </div>
                        {/foreach}
                        </div>
                    </div>
                    <hr />
                    <div id="{$input.name|escape:'html':'UTF-8'}_multival_wp" rel="{$input.name|escape:'html':'UTF-8'}"  class="multival_wp">
                        {if isset($fields_value[$input.name][$defaultFormLanguage]) && $fields_value[$input.name][$defaultFormLanguage]}
                            {foreach $fields_value[$input.name][$defaultFormLanguage] as $key=>$val}
                            <div class="multival">
                                {foreach $languages as $language}
                                    <div class="translatable-field lang-{$language.id_lang|intval}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if};">{if isset($fields_value[$input.name][$language.id_lang][$key])}{$fields_value[$input.name][$language.id_lang][$key]|escape:'html':'UTF-8'}{/if}</div>
                                {/foreach}
                                <div class="multival_action">
                                    <a class="multival_move btn btn-default"><i class="icon-move"></i></a>
                                    <a class="multival_edit btn btn-default"><i class="icon-edit"></i></a>
                                    <a class="multival_delete  btn btn-danger"><i class="icon-trash"></i></a>
                                </div>
                            </div>
                            {/foreach}
                        {/if}
                    </div>
                    <div style="display:none;" class="multival_action_wp">
                        <div class="multival_action">
                            <a class="multival_move btn btn-default"><i class="icon-move"></i></a>
                            <a class="multival_edit btn btn-default"><i class="icon-edit"></i></a>
                            <a class="multival_delete  btn btn-danger"><i class="icon-trash"></i></a>
                        </div>
                    </div>
                    {foreach $languages as $language}
                        <input type="hidden" id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" value="" />
                    {/foreach}
                </div>
            </div>
            <script type="text/javascript">
                $(".multival_wp").sortable({
                    handle: '.multival_move',
                    opacity:0.5,
                    cursor:'move',
                });
            </script>
        </div>
    {elseif $input.type == 'colorchoose'}
        <div class="col-lg-9">
    		<div class="row">
                <textarea class="hidden gvalidate  gvalidate_isRequired" name="extra" id="colorchoose">{if isset($fields_value['extra']['value']) && $fields_value['extra']['value']}{$fields_value['extra']['value']|escape:'html':'UTF-8'}{/if}</textarea>
                <div class="col-lg-6">
                    <input id="color_item" type="color" data-hex="true" class="color mColorPickerInput mColorPicker" name="color" value="" />
                </div>
                <div class="col-lg-2">
    				<button type="button" id="add_color_item" class="btn btn-default">
    					<i class="icon-plus-sign-alt"></i> {l s='Add this color' mod='gformbuilderpro'}
    				</button>
    			</div>
                <div id="divColorItems" class="col-lg-12">
                    {if isset($fields_value['extra']['colors']) && $fields_value['extra']['colors']}
                        {foreach $fields_value['extra']['colors'] as $color}
                            {if $color !=''}
                                <div style="background-color: {$color|escape:'html':'UTF-8'};" class="color_item">
                                <button type="button" class="btn btn-default delColorItem" data-delete="{$color|escape:'html':'UTF-8'}"><span><i class="icon-trash"></i> {$color|escape:'html':'UTF-8'}</button>
                                </div>
                            {/if}
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
    {elseif $input.type == 'imagethumb'}
        <div class="col-lg-9">
    		<div class="row">
                <div id="divThumbItems" class="col-lg-12">
                    {if isset($fields_value['extra']['thumbs']) && $fields_value['extra']['thumbs']}
                        {foreach $fields_value['extra']['thumbs'] as $thumb}
                            {if $thumb !=''}
                                <div class="gthumb_item">
                                    <img src="{$fields_value['base_uri']|escape:'html':'UTF-8'}modules/gformbuilderpro/views/img/thumbs/{$thumb|escape:'html':'UTF-8'}" alt="" />
                                    <button type="button" class="btn btn-default delThumbItem" data-delete="{$thumb|escape:'html':'UTF-8'}"><span><i class="icon-trash"></i></button>
                                </div>
                            {/if}
                        {/foreach}
                    {/if}
                </div>
                <br />
                <input type="hidden" id="thumb_url" value="{$fields_value['base_uri']|escape:'html':'UTF-8'}modules/gformbuilderpro/views/img/thumbs/" />
                <textarea name="extra" id="thumbchoose" class="hidden gvalidate  gvalidate_isRequired">{if isset($fields_value['extra']['value']) && $fields_value['extra']['value']}{$fields_value['extra']['value']|escape:'html':'UTF-8'}{/if}</textarea>
                <div class="col-lg-4">
                    <input type="file" name="thumb[]" id="imagethumbupload" class="imagethumbupload" multiple />
                </div>
                <div class="col-lg-6">
                    <button type="button" id="add_thumb_item_fromlist" class="btn btn-default">
    					<i class="icon-list"></i> {l s='Or choose from exist image' mod='gformbuilderpro'}
    				</button>

    			</div>
                <div class="col-lg-12">
                    <div id="thumbs_fromlist"></div>
                    <button type="button" id="add_thumb_item" class="btn btn-default">
    					<i class="icon-plus-sign-alt"></i> {l s='Add thumbs' mod='gformbuilderpro'}
    				</button>
                    <p class="help-block">{l s='Click to button "Add thumbs" after select thumb' mod='gformbuilderpro'}</p>
                </div>
                
            </div>
        </div>
    {elseif $input.type == 'extraproducts'}
        <div class="col-lg-9">
            <div class="row">
                <div class="col-lg-6">
                    <button type="button" id="add_products_item_fromlist" class="btn btn-default">
                        <i class="icon-list"></i> {l s=' Config Product' mod='gformbuilderpro'}
                    </button>
                </div>
            </div>
        </div>
    {elseif $input.type == 'slidervalue'}
        <div class="col-lg-9">
    		<div class="row">
                <input type="hidden" name="extra" class="gvalidate  gvalidate_isRequired3" id="slidervalue" value="{$fields_value['extra']['value']|escape:'html':'UTF-8'}" />
                <div class="col-lg-3">
                    <div class="row">
                        <label class="col-lg-12" for="minval">{l s='Min' mod='gformbuilderpro'}</label>
                        <div class="col-lg-12">
                            <input type="text" name="minval" class="slidervalue" id="minval" placeholder="{l s='Min value' mod='gformbuilderpro'}" value="{if isset($fields_value['extra']['extraval']['0'])}{$fields_value['extra']['extraval']['0']|escape:'html':'UTF-8'}{else}0{/if}" />
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <label class="col-lg-12" for="maxval">{l s='Max' mod='gformbuilderpro'}</label>
                        <div class="col-lg-12">
                            <input type="text" name="maxval" class="slidervalue" id="maxval" placeholder="{l s='Max value' mod='gformbuilderpro'}" value="{if isset($fields_value['extra']['extraval']['1'])}{$fields_value['extra']['extraval']['1']|escape:'html':'UTF-8'}{else}100{/if}" />
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <label class="col-lg-12" for="rangeval">{l s='Range' mod='gformbuilderpro'}</label>
                        <div class="col-lg-12">
                            <input type="text" name="rangeval" class="slidervalue" id="rangeval" placeholder="{l s='Range value' mod='gformbuilderpro'}"  value="{if isset($fields_value['extra']['extraval']['2'])}{$fields_value['extra']['extraval']['2']|escape:'html':'UTF-8'}{else}1{/if}"/>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="row">
                        <label class="col-lg-12" for="defaultval">{l s='Default' mod='gformbuilderpro'}</label>
                        <div class="col-lg-12">
                            <input type="text" name="defaultval" class="slidervalue" id="defaultval" placeholder="{l s='Default value' mod='gformbuilderpro'}"  value="{if isset($fields_value['extra']['extraval']['1'])}{$fields_value['extra']['extraval']['1']|escape:'html':'UTF-8'}{else}{if $fields_value['multi']}10;30{else}100{/if}{/if}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {else if $input.type == 'tags' && $fields_value['psoldversion15'] == -1}
        <div class="margin-form">
			{block name="input"}
            {if isset($input.lang) AND $input.lang}
				<div class="translatable">
					{foreach $languages as $language}
						<div class="lang_{$language.id_lang|escape:'html':'UTF-8'}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
							{if $input.type == 'tags'}
								{literal}
								<script type="text/javascript">
									$().ready(function () {
										var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}{literal}';
										$('#'+input_id).tagify({addTagPrompt: '{/literal}{l s='Add tag' js=1 mod='gformbuilderpro'}{literal}'});
										$({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
											$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
										});
									});
								</script>
								{/literal}
							{/if}
							{assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
							<input type="text"
									name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
									id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
									value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
									class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
									{if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}
									{if isset($input.maxlength)}maxlength="{$input.maxlength|escape:'html':'UTF-8'}"{/if}
									{if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
									{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
									{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
							{if !empty($input.hint)}<span class="hint" name="help_box">
                                {if is_array($input.hint)}
                                    {foreach $input.hint as $hint}
                                        {$hint|escape:'htmlall':'UTF-8'}
                                    {/foreach}
                                {else}
                                    {$input.hint|escape:'htmlall':'UTF-8'}
                                {/if}
                            </span>{/if}
						</div>
					{/foreach}
				</div>
			{else}
				{if $input.type == 'tags'}
					{literal}
					<script type="text/javascript">
						$().ready(function () {
							var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}{literal}';
							$('#'+input_id).tagify({addTagPrompt: '{/literal}{l s='Add tag' mod='gformbuilderpro'}{literal}'});
							$({/literal}'#{$table|escape:'html':'UTF-8'}{literal}_form').submit( function() {
								$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
							});
						});
					</script>
					{/literal}
				{/if}
				{assign var='value_text' value=$fields_value[$input.name]}
				<input type="text"
						name="{$input.name|escape:'html':'UTF-8'}"
						id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
						value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
						class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
						{if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}
						{if isset($input.maxlength)}maxlength="{$input.maxlength|escape:'html':'UTF-8'}"{/if}
						{if isset($input.class)}class="{$input.class|escape:'html':'UTF-8'}"{/if}
						{if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
						{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
						{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
				{if isset($input.suffix)}{$input.suffix|escape:'html':'UTF-8'}{/if}
				{if !empty($input.hint)}<span class="hint" name="help_box">
                    {if is_array($input.hint)}
                        {foreach $input.hint as $hint}
                            {$hint|escape:'htmlall':'UTF-8'}
                        {/foreach}
                    {else}
                        {$input.hint|escape:'htmlall':'UTF-8'}
                    {/if}
                    </span>
                
                {/if}
			{/if}
            {/block}
        </div>
    {/if}
    {if $input.type == 'tags' && $fields_value['psoldversion15'] == -1}
    {else}
    {$smarty.block.parent}
    {/if}
{/block}