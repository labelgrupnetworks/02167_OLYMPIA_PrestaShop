<div class="simpleblog__addComment blog-mb">
{if $customer.is_logged || Configuration::get('PH_BLOG_COMMENT_ALLOW_GUEST')}
    <h2 class="h2 mb-2">{l s='New comment' mod='ph_simpleblog'}</h2> 
	<form class="simpleblog__addComment__form cardblog" action="{$post->url|escape:'html':'UTF-8'}" method="post">
    <p class="simpleblog_answer_info card-header text-xs-center">{l s='You are replying to a comment' mod='ph_simpleblog'}</p>

        <div class="card-block">
            <div class="form-group">
                <label class="col-12 form-control-label">
                    {l s='Your name' mod='ph_simpleblog'}
                </label>
                <div class="col-12">
                    <input {if $customer.is_logged}readonly{/if} type="text" class="form-control" name="customer_name" id="customer_name" value="{if $customer.is_logged}{$customer.firstname|escape:'html':'UTF-8'}{else}{if isset($smarty.post.comment_content)}{$smarty.post.customer_name|escape:'htmlall':'UTF-8'}{/if}{/if}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-12 form-control-label">
                    {l s='Your comment' mod='ph_simpleblog'}
                </label>
                <div class="col-12">
                    <textarea class="form-control"id="comment_content" name="comment_content" rows="6">{if isset($smarty.post.comment_content)}{$smarty.post.comment_content|escape:'htmlall':'UTF-8'}{/if}</textarea>
                </div>
            </div>
            {if Configuration::get('PH_BLOG_COMMENTS_RECAPTCHA') && Configuration::get('PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY')}
            <div class="form-group">
                <div class="g-recaptcha" data-sitekey="{Configuration::get('PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY')}" data-theme="{Configuration::get('PH_BLOG_COMMENTS_RECAPTCHA_THEME')}"></div>
                <script src='https://www.google.com/recaptcha/api.js'></script>
            </div>
            {/if}
            <footer class="form-footer clearfix">
                <input type="hidden" name="id_simpleblog_post" value="{$post->id_simpleblog_post|intval}" />
                <input type="hidden" name="id_parent" id="id_parent" value="0" />
                {if isset($id_module)}
                {hook h='displayGDPRConsent' id_module=$id_module}
                {/if}
                <button class="continue btn btn-secondary float-xs-left simpleblog__cancelReplay" name="cancelreplay">
                    {l s='Cancel' mod='ph_simpleblog'}
                </button>
                <button class="continue btn btn-primary float-xs-right" name="submitNewComment" type="submit" value="1">
                    {l s='Add new comment' mod='ph_simpleblog'}
                </button>
            </footer>
        </div>
    </form>
{else}
	<div class="warning alert alert-warning">
		<a href="{url entity='authentication' params=['back' => $post->url]}">{l s='Only registered and logged customers can add comments' mod='ph_simpleblog'}</a>
	</div><!-- .warning -->
{/if}
</div>