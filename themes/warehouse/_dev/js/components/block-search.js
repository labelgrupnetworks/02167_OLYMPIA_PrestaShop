/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import $ from 'jquery';
import autoComplete from '../lib/jquery.auto-complete';


prestashop.blocksearch = prestashop.blocksearch || {};

prestashop.blocksearch.initAutocomplete = ($searchWidget, $searchBox, searchURL) => {

    let xhr;
    let resultsPerPage = 10;
    let allText = $searchBox.data('all-text');
    let brandsText = $searchBox.data('brands-text');
    let productText = $searchBox.data('product-text');
    let blogText = $searchBox.data('blog-text');

    let iqitSearchAutocomplete = $searchBox.autoComplete({
        minChars: 2,
        cache: false,
        source: function (query, response) {
            try { xhr.abort(); } catch(e){}
            xhr = $.post(searchURL, {
                s: query,
                resultsPerPage: resultsPerPage,
                ajax: true
            }, null, 'json')
                .then(function (resp) {
                    if (Object.keys(resp.brands).length){
                        let brands = {type: 'brands', results: resp.brands};
                        resp.products.push(brands);
                    }
                    if (Object.keys(resp.blogposts).length){
                        let posts = {type: 'posts', results: resp.blogposts};
                        resp.products.push(posts);
                    }
                    let showAll = {type: 'all'};
                    if (resp.products.length >= resultsPerPage){
                        resp.products.push(showAll);
                    }
                    response(resp.products);
                })
                .fail(response);
        },
        renderItem: function (product, search) {
            console.log(product);
            if(product.type == 'all') {
                return '<div class="autocomplete-suggestion autocomplete-suggestion-show-all dropdown-item" data-type="all" data-val="'+ search + '">' +
                    '<div class="row no-gutters align-items-center">' +
                    '<div class="col"><span class="name">' + allText + ' <i class="fa fa-angle-right" aria-hidden="true"></i></span></div>' +
                    '</div>' +
                    '</div>';
            } else if (product.type == 'brands') {
                let brandsHtml = '';

                Object.keys(product.results).forEach(function(key) {
                    brandsHtml += '<div class="autocomplete-suggestion dropdown-item" data-url="' + product.results[key].link + '">' +
                        '<div class="row no-gutters align-items-center">' +
                        '<div class="col col-auto col-img"><img class="img-fluid" src="' + product.results[key].image + '" /></div>' +
                        '<div class="col pt-3 pb-3"><span class="name">' + product.results[key].name + '</span></div>' +
                        '<div class="col col-auto col-shop pt-3 pb-3 ">' + brandsText + '</div>' +
                        '</div></div>';
                });
                return brandsHtml;
            }  else if (product.type == 'posts') {
                let postsHtml = '';


                Object.keys(product.results).forEach(function(key) {

                    let imageHtml = '';
                    if (typeof product.results[key].banner_thumb !== 'undefined') {
                        imageHtml = '<img class="img-fluid" src="' + product.results[key].banner_thumb + '" />';
                    }

                    postsHtml += '<div class="autocomplete-suggestion dropdown-item" data-url="' + product.results[key].url + '">' +
                        '<div class="row no-gutters align-items-center">' +
                        '<div class="col col-auto col-img"><div class="col col-auto col-img">'+ imageHtml +'</div></div>' +
                        '<div class="col pt-3 pb-3"><span class="name">' + product.results[key].title + '</span></div>' +
                        '<div class="col col-auto col-shop pt-3 pb-3 ">' + blogText + '</div>' +
                        '</div></div>';
                });
                return postsHtml;
            }
            else {
                let imageHtml = '';
                try{ imageHtml = '<div class="col col-auto col-img"><img class="img-fluid" src="' + product.cover.small.url + '" /></div>';} catch(e){ imageHtml = ''; }
                return '<div class="autocomplete-suggestion dropdown-item" data-url="' + product.url + '">' +
                    '<div class="row no-gutters align-items-center">' + imageHtml +
                    '<div class="col pt-3 pb-3"><span class="name">' + product.name + '</span><span class="product-price">' + product.price + '</span></div>' +
                    '<div class="col col-auto col-shop pt-3 pb-3 ">' + productText +  '</div>' +
                    '</div>' +
                    '</div>';
            }
        },
        onSelect: function (e, term, item) {
            if (item.data('type') == 'all'){
                $searchWidget.find('form').submit();
            } else{
                window.location.href = item.data('url');
            }

        }
    });

};

export default class BlockSearch {
    init() {

        let $searchWidget = $('#search_widget');
        let $searchWidgetMobile = $('#search-widget-mobile');
        let $searchBox = $searchWidget.find('input[type=text]');
        let $searchBoxMobile = $searchWidgetMobile.find('input[type=text]');
        let searchURL = $searchWidget.attr('data-search-controller-url');
        let $searchToggle = $('#header-search-btn');
        let $searchToggleMobile = $('#mobile-btn-search');

        var initAutocomplete = prestashop.blocksearch.initAutocomplete || function ($searchWidget, $searchBox, searchURL) {};

        initAutocomplete($searchWidget, $searchBox, searchURL);
        initAutocomplete($searchWidgetMobile, $searchBoxMobile, searchURL);

        $searchToggle.on('shown.bs.dropdown', function () {
            setTimeout(function(){
                $searchBox.focus();
            }, 300);
        });

        $searchToggleMobile.on('shown.bs.dropdown', function () {
            setTimeout(function(){
                $('#mobile-btn-search').find('input[type=text]').focus();
            }, 300);
        });

        $('#fullscreen-search-backdrop').on('touchstart', function(e){
            e.stopPropagation();
            $('#header-search-btn-drop').dropdown('toggle');
        });
    }


}