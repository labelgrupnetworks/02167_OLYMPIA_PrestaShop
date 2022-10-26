{**
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
 *}

{extends file='page.tpl'}

{block name='checkout_header'}
  {if isset($iqitTheme.checkout_header) && $iqitTheme.checkout_header == 'simple'}
    <div id="checkout-header" class="header-top">
      <div class="container">

        {if $iqitTheme.h_logo_position == 'left'}
          <a class="text-muted mt-2 mb-2 d-inline-block" href="{$urls.pages.index}">
            <i class="fa fa-angle-left" aria-hidden="true"></i> {l s='Continue shopping' d='Shop.Theme.Actions'}
          </a>
            <div>
                <a href="{$urls.pages.index}">
                    <img class="logo img-fluid"
                         src="{$shop.logo}"
                            {if isset($iqitTheme.rm_logo) && $iqitTheme.rm_logo != ''} srcset="{$iqitTheme.rm_logo} 2x"{/if}
                         alt="{$shop.name}"
                         width="{$shop.logo_details.width}"
                         height="{$shop.logo_details.height}"
                    >
                </a>
            </div>
        {else}
        <div class="row">
            <div class="col">
            <a class="text-muted mt-2 mb-2 d-inline-block text-nowrap" href="{$urls.pages.index}">
                <i class="fa fa-angle-left" aria-hidden="true"></i> {l s='Continue shopping' d='Shop.Theme.Actions'}
            </a>
            </div>
            <div class="col col-auto">
                <a href="{$urls.base_url}">
                    <img class="logo img-fluid"
                         src="{$shop.logo}" {if isset($iqitTheme.rm_logo) && $iqitTheme.rm_logo != ''} srcset="{$iqitTheme.rm_logo} 2x"{/if}
                         alt="{$shop.name}">
                </a></div>
            <div class="col"></div>
        </div>
        {/if}


      </div>
    </div>
  {/if}
{/block}


{block name='content'}
  <section id="main">
    <h1 class="h1 page-title"><span>{l s='Checkout' d='Shop.Warehousetheme'}</span></h1>
    <div class="row">
      <div class="col-md-8">
        {block name='checkout_process'}
          {render file='checkout/checkout-process.tpl' ui=$checkout_process}
        {/block}
      </div>
      <div class="col-md-4 cart-grid-right">

        {block name='cart_summary'}
          {include file='checkout/_partials/cart-summary.tpl' cart = $cart}
        {/block}

        {hook h='displayReassurance'}
      </div>
    </div>
  </section>
{/block}

{block name='checkout_footer'}
    {if isset($iqitTheme.checkout_footer) && $iqitTheme.checkout_footer == 'simple'}
        <div id="checkout-footer" class="footer-container footer-style-{$iqitTheme.f_layout}">
            <div class="container">
                <div class="row">
                    {hook h='displayCheckoutFooter'}
                </div>
            </div>
        </div>
    {/if}

    <div class="modal fade js-checkout-modal" id="modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title"></span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body modal-terms js-modal-content">

                </div>
            </div>
        </div>
    </div>

{/block}