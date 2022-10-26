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
import debounce from 'throttle-debounce/debounce';

export default class ThemeCompontents {
  init() {
    this.backToTop();
    this.productCarousels();
    this.otherCarousels();

    if ('ontouchstart' in document.documentElement) {
      $('body').addClass('touch-device');
    }

    if (iqitTheme.h_layout !== 6 && iqitTheme.h_layout !== 7) {
      if (iqitTheme.h_sticky === 'menu' || iqitTheme.h_sticky === 'header') {
        this.stickyHeader(iqitTheme.h_sticky);
      }
    }
  }

  backToTop() {
    const $backToTop = $('#back-to-top');

    $(window).scroll(debounce(300, function () {
      if ($(this).scrollTop() > 300) {
        $backToTop.addClass('-back-to-top-visible');
      } else {
        $backToTop.stop().removeClass('-back-to-top-visible');
      }
    }));

    $backToTop.on('click', (event) => {
      event.preventDefault();
      $('body, html').animate({
        scrollTop: 0,
      }, 300,
      );
    });
  }

  stickyHeader(scrollElement) {
    let $header;
    let $stickyCartWrapper;
    let $defaultCartWrapper;
    let $cart;
    let handler = function (direction) {
    };

    if (scrollElement == 'menu') {
      $header = $('#iqitmegamenu-wrapper');
      $stickyCartWrapper = $('#sticky-cart-wrapper');
      $defaultCartWrapper = $('#ps-shoppingcart-wrapper');
      $cart = $('#ps-shoppingcart');
      handler = function (direction) {
        if (direction === 'down') {
          if (iqitTheme.pp_image_layout === 'column') {
            const top = 30 + $header.height();
            $('#col-product-info').first().css('top', `${top}px`);
          }
          $stickyCartWrapper.append($cart);
        } else {
          $defaultCartWrapper.append($cart);
          if (iqitTheme.pp_image_layout === 'column') {
            $('#col-product-info').first().removeAttr('style');
          }
        }
      };
    } else {
      $header = $('#desktop-header');
      if (iqitTheme.pp_image_layout === 'column') {
        handler = function (direction) {
          if (direction === 'down') {
            const top = 30 + $header.height();
            $('#col-product-info').first().css('top', `${top}px`);
          } else {
            $('#col-product-info').first().removeAttr('style');
          }
        };
      }
    }

    if ($header.length) {
      const sticky = new Waypoint.Sticky({
        element: $header[0],
        wrapper: '<div class="sticky-desktop-wrapper" />',
        stuckClass: `stuck stuck-header-element stuck-${scrollElement}`,
        handler,
        offset: 0,
      });
    }
  }

  productCarousels() {
    const $carousels = $('.swiper-default-carousel');
    let autoplay;

    if (iqitTheme.pl_crsl_autoplay) {
      autoplay = {
        delay: 4500,
        disableOnInteraction: true,
      };
    }

    const defaultOptions = {
      pagination: {
        el: '.swiper-pagination-product',
        clickable: true,
      },
      autoplay,
      loop: false,
      speed: 600,
      lazy: {
        loadedClass: 'loaded',
        checkInView: true,
      },
      watchOverflow: true,
      watchSlidesProgress: true,
      watchSlidesVisibility: true,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      slidesPerView: iqitTheme.pl_slider_p,
      slidesPerGroup: iqitTheme.pl_slider_p,
      breakpoints: {
        768: {
          slidesPerView: iqitTheme.pl_slider_t,
          slidesPerGroup: iqitTheme.pl_slider_t,
        },
        992: {
          slidesPerView: iqitTheme.pl_slider_d,
          slidesPerGroup: iqitTheme.pl_slider_d,
        },
        1200: {
          slidesPerView: iqitTheme.pl_slider_ld,
          slidesPerGroup: iqitTheme.pl_slider_ld,
        },
      },
    };

    $carousels.each(function () {
      const $carousel = $(this);
      const swiperOptions = $.extend({}, defaultOptions, $carousel.data('swiper_options'));
      const mySwiper = new Swiper($carousel[0], swiperOptions);
    });
  }

  otherCarousels() {
    const mySwiper = new Swiper('.js-iqithtmlandbanners-block-banner-slider', {
      loop: true,
      watchOverflow: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: true,
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
    });
  }
}
