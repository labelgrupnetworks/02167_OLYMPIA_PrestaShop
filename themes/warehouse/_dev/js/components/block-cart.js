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
import prestashop from 'prestashop';
import $ from 'jquery';

prestashop.blockcart = prestashop.blockcart || {};

prestashop.blockcart.showModal = (html) => {
  function getBlockCartModalWrap() {
    return $('#blockcart-modal-wrap');
  }
  function getBlockCartModal() {
    return $('#blockcart-modal');
  }
  function getBlockCartNotification() {
    return $('#blockcart-notification');
  }

  if (iqitTheme.cart_confirmation === 'modal') {
    let $blockCartModal = getBlockCartModalWrap();

    if ($blockCartModal.length) {
      $blockCartModal.remove();
      $('.modal-backdrop.show').first().remove();
    }

    $('body').append(html);

    $blockCartModal = getBlockCartModal();
    $blockCartModal.modal('show');
    $blockCartModal.on('hide.bs.modal', (event) => {
      prestashop.emit('updateProduct', {
        reason: event.currentTarget.dataset,
        event,
      });
    });

    $blockCartModal.on('shown.bs.modal', () => {
      prestashop.iqitLazyLoad.update();
      new Swiper('.swiper-crossselling-products', {
        pagination: {
          el: '.swiper-pagination-product',
          clickable: true,
        },
        autoplay: {
          delay: 4500,
          disableOnInteraction: true,
        },
        loop: false,
        speed: 600,
        lazy: {
          loadedClass: 'loaded',
          checkInView: true,
        },
        watchOverflow: true,
        watchSlidesProgress: true,
        watchSlidesVisibility: true,
        slidesPerView: 2,
        slidesPerGroup: 2,
        breakpoints: {
          768: {
            slidesPerView: 3,
            slidesPerGroup: 3,
          },
          992: {
            slidesPerView: 5,
            slidesPerGroup: 5,
          },
          1200: {
            slidesPerView: 5,
            slidesPerGroup: 5,
          },
        },
      });
    });
  } else if (iqitTheme.cart_confirmation === 'notification') {
    let $blockCartNotification = getBlockCartModalWrap();

    if ($blockCartNotification.length) {
      $blockCartNotification.remove();
    }

    $('body').append(html);

    $blockCartNotification = getBlockCartNotification();
    $blockCartNotification.addClass('ns-show');
    prestashop.emit('updateProduct', {});
    setTimeout(() => {
      $blockCartNotification.addClass('ns-hide');
    }, 3500);
  } else {
    if (prestashop.responsive.mobile) {
      $('#mobile-cart-toogle').dropdown('toggle');

      if (iqitTheme.cart_style === 'floating') {
        $('body').animate({
          scrollTop: $('#mobile-cart-toogle').offset().top,
        }, 300,
        );
      }
    } else {
      $('#cart-toogle').dropdown('toggle');

      if (iqitTheme.cart_style === 'floating') {
        $('body').animate({
          scrollTop: $('#blockcart').offset().top,
        }, 300,
        );
      }
    }

    prestashop.emit('updateProduct', {});
  }
};
