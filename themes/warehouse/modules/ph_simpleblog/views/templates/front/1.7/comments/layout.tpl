<div class="blog-mb simpleblog__comments{if $post->comments == 0}-empty{/if}" id="phsimpleblog_comments">
    <h2 class="section-title"><span>{l s='Comments' d='Modules.Simpleblog.Shop'} ({$post->comments})</span></h2>
    {if $post->comments}
    <ul class="cardblog mb-0">
    	{foreach $comments as $comment}
        <li class="card-block {if $comment.is_highlighted}simpleblog_comments_highlighted{/if} pt-3 pb-3">
            <ul class="simpleblog__comments__authorInfo text-muted">
                <li class="simpleblog__comments__authorInfo__author">{$comment.name}</li>
                <li>{$comment.date_add}</li>
            </ul>
            <div class="simpleblog__comments__text">
                {$comment.comment}
            </div>
            {if $comment.depth == 0} 
                <button class="reply-simpleblog-button btn btn-secondary btn-sm mt-3" data-id-comment="{$comment.id}" type="button">{l s='Reply' d='Modules.Simpleblog.Shop'}</button>
            {/if}
            {if $comment.replies|@count > 0}
            {foreach $comment.replies as $commentReply}
                <li class="simpleblog_reply_position {if $commentReply.is_highlighted}simpleblog_comments_highlighted{/if}">
                    <ul class="simpleblog__comments__authorInfo">
                        <li class="simpleblog__comments__authorInfo__author">
                            {$commentReply.name} 
                        </li>
                        <li>{$commentReply.date_add}</li>
                    </ul>
                    <div class="simpleblog__comments__text">
                        {$commentReply.comment}
                    </div>
                </li>
            {/foreach}
            {/if}
        </li>
        {/foreach}
    </ul>
    {else}
    	<div class="warning alert alert-warning mb-2">
    		{l s='No comments at this moment' d='Modules.Simpleblog.Shop'}
    	</div><!-- .warning -->
    {/if}
</div>

{include file="module:ph_simpleblog/views/templates/front/1.7/comments/form.tpl"}