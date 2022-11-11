{**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2019 Innova Deluxe SL
* @license   INNOVADELUXE
*}
{foreach $idxrcookiesTemplates as $template}

{if $template.tag_script}

<script type="text/javascript">
    {if $template.tag_literal}

    {$template.contenido nofilter}

     {else}
     {assign var="content" value=$template.contenido}
{include file="string:$content"}
      {/if}

</script>

{else}

{if $template.tag_literal}

{$template.contenido nofilter}

{else}
{assign var="content" value=$template.contenido}
{include file="string:$content"}

{/if}

{/if}
{/foreach}
