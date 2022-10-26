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
import 'easyzoom/dist/easyzoom';
import prestashop from 'prestashop';

$(document).ready(() => {
  let coverSwiper;
  let thumbsSwiper;

  createProductSpin();
  createInputFile();
  imageScrollBox();
  coverImage();
  createToolTip();
  accessoriesSidebarCarousel();
  const $main = $('#main');

  if (iqitTheme.pp_tabs == 'tabha') {
    getAccordion('#product-infos-tabs', 576);
  }

  $('[data-button-action="add-to-cart"]').on('click', (event) => {
    event.preventDefault();
    $(event.target).addClass('processing-add');
    const $form = $(event.currentTarget.form);
    $form.find("input[name='token']").val(prestashop.static_token);
  },
  );

  prestashop.on('updateCart', (event) => {
    $('.add-to-cart.processing-add').removeClass('processing-add');
  });

  prestashop.on('updateProduct', (event) => {
    if (typeof prestashop.page.page_name !== 'undefined') {
      if (prestashop.page.page_name === 'product') {
        $main.addClass('-combinations-loading');
      }
    }
  });

  prestashop.on('updatedProduct', (event) => {
    createInputFile();
    createToolTip();
    imageScrollBox();
    coverImage();

    if (event && event.product_minimal_quantity) {
      const minimalProductQuantity = parseInt(event.product_minimal_quantity, 10);
      const quantityInputSelector = prestashop.selectors.quantityWanted;
      const quantityInput = $(quantityInputSelector);

      // @see http://www.virtuosoft.eu/code/bootstrap-touchspin/ about Bootstrap TouchSpin
      quantityInput.trigger('touchspin.updatesettings', {
        min: minimalProductQuantity,
      });
    }

    if (iqitTheme.pp_image_layout === 'column') {
      prestashop.iqitLazyLoad.update();
    }

    $($('.tabs .nav-link.active').attr('href')).addClass('active').removeClass('fade');
    $(prestashop.themeSelectors.product.imagesModal).replaceWith(event.product_images_modal);
    $main.removeClass('-combinations-loading');
  });

  function coverImage() {
    if ($('.modal.quickview').length) {
      new Swiper('#product-images-large', {
        loop: false,
        effect: 'fade',
        fadeEffect: {
          crossFade: true,
        },
        preloadImages: false,
        initialSlide: $('#product-images-large').find('.js-thumb-selected').first().index(),
        lazy: {
          loadPrevNext: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    } else {
      if (!$('#product-images-large').length) {
        return;
      }
      if (coverSwiper !== undefined) {
        coverSwiper.destroy();
      }
      if (iqitTheme.pp_image_layout === 'column') {
        const breakpoint = window.matchMedia('(min-width:768px)');
        const breakpointChecker = function () {
          if (breakpoint.matches === true) {
            if (coverSwiper !== undefined) {
              coverSwiper.destroy();
            }
          } else if (breakpoint.matches === false) {
            return enableSwiperOnColumn();
          }
        };

        const enableSwiperOnColumn = function () {
          coverSwiper = new Swiper('#product-images-large', {
            loop: false,
            navigation: {
              nextEl: '.swiper-button-next',
              prevEl: '.swiper-button-prev',
            },
            preloadImages: false,
            initialSlide: $('#product-images-large').find('.js-thumb-selected').first().index(),
            lazy: {
              loadPrevNext: true,
            },
            pagination: {
              el: '.swiper-pagination-product',
              clickable: true,
            },
          });
        };
        try {
          // Chrome & Firefox
          breakpoint.addEventListener('change', () => {
            breakpointChecker();
          });
        } catch (e1) {
          try {
            // Safari
            breakpoint.addListener(breakpointChecker);
          } catch (e2) {
            console.error(e2);
          }
        }
        breakpointChecker();
      }
      if (iqitTheme.pp_image_layout === 'carousel') {
        let thumbs;

        if ($('#product-images-thumbs').length) {
          thumbs = {
            swiper: thumbsSwiper,
            autoScrollOffset: 1,
          };
        }

        coverSwiper = new Swiper('#product-images-large', {
          loop: false,
          effect: 'fade',
          initialSlide: $('#product-images-large').find('.js-thumb-selected').first().index(),
          fadeEffect: {
            crossFade: true,
          },
          preloadImages: false,
          lazy: {
            loadPrevNext: true,
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          thumbs,
        });
      }
    }

    if (iqitTheme.pp_zoom === 'inner') {
      const $easyzoom = $('.easyzoom-product').easyZoom();
    } else if (iqitTheme.pp_zoom === 'modalzoom') {
      $('.js-easyzoom-trigger').on('click', (event) => {
        event.preventDefault();
      });
    }
  }

  function imageScrollBox() {
    if (iqitTheme.pp_image_layout === 'column' || $('.modal.quickview').length) {
      return;
    }
    if (thumbsSwiper !== undefined) {
      thumbsSwiper.destroy();
    }
    let direction = 'horizontal';
    const slides = 5;
    let breakpoints;
    const $thumbs = $('#product-images-thumbs');

    if (iqitTheme.pp_thumbs === 'left') {
      direction = 'vertical';
    }
    if (iqitTheme.pp_thumbs === 'leftd') {
      breakpoints = {
        768: {
          direction: 'vertical',
        },
      };
    }
    if ($thumbs.length) {
      const defaultOptions = {
        loop: false,
        direction,
        slidesPerView: slides,
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
        preloadImages: false,
        lazy: {
          loadPrevNext: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        breakpoints,
      };

      const swiperOptions = $.extend({}, defaultOptions, $thumbs.data('swiper_options'));
      thumbsSwiper = new Swiper($thumbs[0], swiperOptions);
    }
  }

  function createInputFile() {
    const $input = $(prestashop.themeSelectors.fileInput);
    $input.filestyle({ buttonText: $input.data('buttontext') });
    $input.on('change', (event) => {
      let target; let
        file;
      // eslint-disable-next-line
      if ((target = $(event.currentTarget)[0]) && (file = target.files[0])) {
        $(target).prev().text(file.name);
      }
    });
  }

  function createToolTip() {
    if (!('ontouchstart' in document.documentElement)) {
      $(() => {
        $('[data-toggle="tooltip"]').tooltip();
      });
    }
  }

  function createProductSpin() {
    const $quantityInput = $(prestashop.selectors.quantityWanted);

    $quantityInput.TouchSpin({
      verticalbuttons: true,
      verticalupclass: 'fa fa-angle-up touchspin-up',
      verticaldownclass: 'fa fa-angle-down touchspin-down',
      buttondown_class: 'btn btn-touchspin js-touchspin',
      buttonup_class: 'btn btn-touchspin js-touchspin',
      min: parseInt($quantityInput.attr('min'), 10),
      max: 1000000,
    });

    $(prestashop.themeSelectors.touchspin).off('touchstart.touchspin');

    $quantityInput.focusout(() => {
      if ($quantityInput.val() === '' || parseInt($quantityInput.val()) < parseInt($quantityInput.attr('min'))) {
        $quantityInput.val($quantityInput.attr('min'));
        $quantityInput.trigger('change');
      }
    });

    $('body').on('change keyup', prestashop.selectors.quantityWanted, (e) => {
      if ($quantityInput.val() !== '') {
        $(e.currentTarget).trigger('touchspin.stopspin');
        prestashop.emit('updateProduct', {
          eventType: 'updatedProductQuantity',
          event: e,
        });
      }
    });
  }

  function accessoriesSidebarCarousel() {
    let autoplay;

    if (iqitTheme.pl_crsl_autoplay) {
      autoplay = {
        delay: 4500,
        disableOnInteraction: true,
      };
    }
    const accessoriesSidebarCarousel = new Swiper('#product-accessories-sidebar', {
      pagination: {
        el: '.swiper-pagination-product',
        clickable: true,
      },
      autoplay,
      loop: false,
      speed: 600,
      watchOverflow: true,
      slidesPerView: 1,
      slidesPerColumn: 5,
      slidesPerColumnFill: 'row',
    });
  }
  /* eslint-disable */
  function getAccordion($element_id, screen) {
    if ($(window).width() < screen) {
      let obj_tabs; let obj_cont; let
        $tabs_content;

      let concat = '';
      obj_tabs = $(`${$element_id} > li`).toArray();
      $tabs_content = $('#product-infos-tabs-content');
      obj_cont = $tabs_content.children('.tab-pane').toArray();

      concat = '<div class="card">';
      jQuery.each(obj_tabs, (n, val) => {
        concat += '<div class="nav-tabs" role="tab" >';
        if (n > 0) {
          concat += `<a class="nav-link collapsed" id="ma-nav-link-${n}" data-toggle="collapse" data-parent="#product-infos-accordion-mobile" href="#product-infos-accordion-mobile-${n}">`;
        } else {
          concat += `<a class="nav-link" id="ma-nav-link-${n}" data-toggle="collapse" data-parent="#product-infos-accordion-mobile" href="#product-infos-accordion-mobile-${n}">`;
        }

        concat += `${val.innerText}`
          + '<i class="fa fa-angle-down float-right angle-down" aria-hidden="true"></i><i class="fa fa-angle-up float-right angle-up" aria-hidden="true"></i>'
          + '</a>';

        concat += '</div>';

        if (n > 0) {
          concat += `<div id="product-infos-accordion-mobile-${n}" class="collapse tab-content" role="tabpanel">`;
        } else {
          concat += `<div id="product-infos-accordion-mobile-${n}" class="collapse tab-content show" role="tabpanel">`;
        }
        concat += `<div id="ma-${obj_cont[n].id}" class=""></div>`;
        concat += '</div>';
      });
      concat += '</div></div>';
      const $accordion = $('#product-infos-accordion-mobile');
      $accordion.html(concat);

      jQuery.each(obj_tabs, (n, val) => {
        $(obj_cont[n]).appendTo(`#ma-${obj_cont[n].id}`);
      });

      prestashop.iqitLazyLoad.update();
      $($element_id).remove();
      $tabs_content.remove();
    }
  }
  /* eslint-enable */
});
