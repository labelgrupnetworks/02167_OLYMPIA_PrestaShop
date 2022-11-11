<!doctype html>
<html lang="{$language.iso_code}">

    <head>
        {block name='head'}
            {include file='_partials/head.tpl'}
        {/block}
    </head>

    <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

        {block name='hook_after_body_opening_tag'}
            {hook h='displayAfterBodyOpeningTag'}
        {/block}

        <main>
            {block name='product_activation'}
                {include file='catalog/_partials/product-activation.tpl'}
            {/block}
            {*  Changes done by Kanishka Kannoujia on 13-04-2022 to solve RenderLogo not defined*}
            <header id="header">
                {block name='header'}
                    {if isset($ps_ver) && $ps_ver}
                        {include file='_partials/helpers.tpl'}
                    {/if}
                    {include file='_partials/header.tpl'}
                {/block}
            </header>
            {*  Changes end here       *}
            {block name='notifications'}
                {include file='_partials/notifications.tpl'}
            {/block}

            <section id="wrapper">
                {hook h="displayWrapperTop"}
                <div class="container">
                    {block name='breadcrumb'}
                        {include file='_partials/breadcrumb.tpl'}
                    {/block}

                    {block name="content_wrapper"}
                        <div id="content-wrapper" class="left-column col-xs-12 col-sm-12 col-md-12">
                            {hook h="displayContentWrapperTop"}
                            {block name="content"}
                                <section id="main">

                                    {block name='product_list_header'}
                                        <h2 class="h2">{$listing.label}</h2>
                                    {/block}

                                    <section id="products">
                                        {if $listing.products|count}

                                            <div id="">
                                                {block name='product_list_top'}
                                                    {include file='catalog/_partials/products-top.tpl' listing=$listing}
                                                {/block}
                                            </div>
                                            {*
                                            {block name='product_list_active_filters'}
                                            <div id="" class="hidden-sm-down">
                                            {$listing.rendered_active_filters nofilter}
                                            </div>
                                            {/block}*}

                                            <div id="">
                                                {block name='product_list'}
                                                    {include file='catalog/_partials/products.tpl' listing=$listing}
                                                {/block}
                                            </div>

                                            <div id="js-product-list-bottom">
                                                {block name='product_list_bottom'}
                                                    {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
                                                {/block}
                                            </div>

                                        {else}

                                            <div class="alert alert-danger">
                                                {l s='No Products found!' mod='kbbookingcalendar' }
                                            </div>

                                        {/if}
                                    </section>

                                </section>
                            {/block}
                            {hook h="displayContentWrapperBottom"}
                        </div>
                    {/block}

                </div>
                {hook h="displayWrapperBottom"}
            </section>

            <footer id="footer">
                {block name="footer"}
                    {include file="_partials/footer.tpl"}
                {/block}
            </footer>

        </main>

        {block name='javascript_bottom'}
            {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
        {/block}

        {block name='hook_before_body_closing_tag'}
            {hook h='displayBeforeBodyClosingTag'}
        {/block}
    </body>

</html>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2019 Knowband
* @license   see file: LICENSE.txt
*
*}