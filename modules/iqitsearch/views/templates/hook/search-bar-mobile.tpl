{*
* 2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement
*
* @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
* @copyright 2017 IQIT-COMMERCE.COM
* @license   Commercial license (You can not resell or redistribute this software.)
*
*}


<!-- Block search module TOP -->
<form method="get" action="{$search_controller_url}">
    <div class="input-group">
        <input type="text" name="s" value=""
               placeholder="{l s='Search' d='Shop.Theme.Catalog'}"
               data-all-text="{l s='Show all results' mod='iqitsearch'}"
               data-blog-text="{l s='Blog post' mod='iqitsearch'}"
               data-product-text="{l s='Product' mod='iqitsearch'}"
               data-brands-text="{l s='Brand' mod='iqitsearch'}"
               autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
               class="form-control form-search-control">
        <button type="submit" class="search-btn">
            <i class="fa fa-search"></i>
        </button>
    </div>
</form>
<!-- /Block search module TOP -->

