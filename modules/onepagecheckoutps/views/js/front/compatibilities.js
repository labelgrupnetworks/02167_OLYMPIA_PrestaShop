/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * @author    PresTeamShop SAS (Registered Trademark) <info@presteamshop.com>
 * @copyright 2011-2022 PresTeamShop SAS, All rights reserved.
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @category  PrestaShop
 * @category  Module
 */

 function initOpcCompatibilities() {
    if (typeof prestashop === typeof undefined) {
        return;
    }

    //App - Events
    prestashop.on('opc-app-init', (params) => {
        const { JqueryElementMain } = params;

        //ps_checkout - v2.15.4 - PrestaShop
        if (typeof window.ps_checkout !== typeof undefined) {
            window.ps_checkout.events = new EventTarget();
            window.ps_checkout.events.addEventListener('payment-option-active', ({ detail }) => {
                const { HTMLElementContainer } = detail;
                $(HTMLElementContainer).parent().show();
            });
        }
    });

    //Login - Events
    prestashop.on('opc-login-loginCustomer-error', (params) => {
        const { JqueryElement } = params;

        //idxrdefender - v1.3.2 - innovadeluxe
        if (typeof IdxrdefenderFront !== typeof undefined) {
            JqueryElement.find('#g-recaptcha-response-login').val('');
            let defender = new IdxrdefenderFront();
            defender.bindsCaptcha();
        }
    });

    //Personal Information - Events
    prestashop.on('opc-personal_information-saveCustomer-error', (params) => {
        const { JqueryElement } = params;

        //idxrdefender - v1.3.2 - innovadeluxe
        if (typeof IdxrdefenderFront !== typeof undefined) {
            JqueryElement.find('#g-recaptcha-response-login').val('');
            let defender = new IdxrdefenderFront();
            defender.bindsCaptcha();
        }
    });

    //Addresses - Events
    prestashop.on('opc-addresses-getAddresses-complete', (params) => {
        const { JqueryElement } = params;
    });
    prestashop.on('opc-addresses-getAddresses-complete-edit', (params) => {
        const { JqueryElement } = params;
    });
    prestashop.on('opc-addresses-getAddresses-complete-add', (params) => {
        const { JqueryElement } = params;
    });

    //Shipping - Events
    prestashop.on('opc-shipping-getCarrierList-complete', (params) => {
        const { JqueryElement } = params;

        //support module: mondialrelay - v3.1.1 - ScaleDEV
        //se hizo modificacion a medida en dicho modulo para poder funcionar.
        if (typeof mondialrelayWidget !== typeof undefined) {
            $(mondialrelayWidget).trigger('mondialrelay.ready');
        }

        //swastarkencl - v3.6.0 - Softwareagil
        if (typeof swastarkenclRunOnReady !== typeof undefined) {
            swastarkenclRunOnReady();
        }

        //gmparcellocker - v1.4.1 - GreenMouseStudio.com
        if (typeof gmParcelLockerAjaxUrl !== typeof undefined && JqueryElement.find('#easypack-widget').length > 0) {
            window.easyPack.dropdownWidget('easypack-widget', function (point) {
                const pointData = point.name + '| ' + point.address.line1 + '| ' + point.address.line2;
                $.ajax({
                    url: gmParcelLockerAjaxUrl,
                    type: 'POST',
                    crossDomain: true,
                    data: {cartId: gmCartId, pointData: pointData},
                    async: true,
                    dataType: "json",
                    headers: {"cache-control": "no-cache"},
                    success: function (data) {
                        if (data.msg == 'OK') {
                            $('.chosen-parcel').html(pointData.split('|').join(','));

                            if (typeof window.checkGmParcellocker !== typeof undefined) {
                                window.gmparcellockerCheckStoreSelected(JqueryElement);
                            }
                        }
                    },
                    error: function (jqXHR, textStatus) {
                        console.log(jqXHR.responseText);
                    }
                });
            });

            window.gmparcellockerCheckStoreSelected = function(JqueryElement) {
                if (JqueryElement.find('.gmparcellocker-button').is(':visible')) {
                    window.checkGmParcellocker = true;
                    if (JqueryElement.find('.chosen-parcel').text().length > 3) {
                        window.gmparcellockerStoreSelected = true;
                    } else {
                        window.gmparcellockerStoreSelected = false;
                    }
                } else {
                    if (window.checkGmParcellocker) {
                        window.gmparcellockerStoreSelected = true;
                        window.checkGmParcellocker = false;
                    }
                }
            }

            const gmparcellocker = window.setInterval(function () {
                if (typeof window.checkGmParcellocker !== typeof undefined) {
                    window.gmparcellockerCheckStoreSelected(JqueryElement);

                    clearInterval(gmparcellocker);
                }
            }, 500);
        }
    });
    prestashop.on('opc-shipping-updateCarrier-complete', (params) => {
        const { JqueryElement } = params;

        //support module: mondialrelay - v3.1.1 - ScaleDEV
        //se hizo modificacion a medida en dicho modulo para poder funcionar.
        if (typeof mondialrelayWidget !== typeof undefined) {
            $(mondialrelayWidget).trigger('mondialrelay.ready');
        }
    });

    //Payment - Events
    prestashop.on('opc-payment-getPaymentList-complete', (params) => {
        const { JqueryElement } = params;

        let paymentList = [];
        JqueryElement.find('.payment_radio').each(function (i, item) {
            paymentList.push($(item).data('module-name'));
        });

        //ps_checkout - v2.15.4 - PrestaShop
        if (typeof window.ps_checkout !== typeof undefined && typeof JqueryElement !== typeof undefined) {
            JqueryElement.find('.payment_radio').each(function (i, item) {
                const $item = $(item);
                const moduleName = $item.data('module-name');
                const $container = $item.parents('#' + $item.prop('id') + '-container');

                if (-1 !== moduleName.search('ps_checkout')) {
                    $container.parent().hide();
                }
            });

            window.ps_checkout.renderCheckout();
        }

        //paytpv - v7.7.1 - Paycomet
        if (typeof paytpv_initialize !== typeof undefined) {
            paytpv_initialize();
        }

        //pts_stripe - v4.0.6 - PresTeamShop
        if (typeof AppPTSS !== typeof undefined) {
            AppPTSS.setup();
        }

        //klarnapaymentsofficial - v2.1.7 - Prestaworks AB
        if (typeof kp_client_token !== typeof undefined) {
            const klarna_options = $('.klarna-container');
            for (const element of klarna_options) {
                const payment_category = element.id.substring(26, element.id.length);
                $('input[data-module-name=klarnapayments_' + payment_category + '_module]')
                    .click(function () {
                        initiateKlarnaWidget(kp_client_token, '#' + element.id, payment_category);
                    })
                    .parents('.module_payment_container').click(function () {
                        initiateKlarnaWidget(kp_client_token, '#' + element.id, payment_category);
                    });
            }
        }

        if (typeof prestashop !== typeof undefined && typeof prestashop.emit !== typeof undefined) {
            prestashop.emit('steco_event_updated');
        }

        //stripejs - v4.3.9 - NTS
        if (paymentList.includes("stripejs") && typeof stripe_allow_cards !== typeof undefined && typeof StripePubKey !== typeof undefined) {
            let _script = document.createElement('script');
            _script.type = 'text/javascript';
            _script.src = prestashop.urls.base_url + 'modules/stripejs/views/js/stripe-prestashop.js';
            JqueryElement.append(_script);
        }

        //kf_paypal - v2.1.7 - KForge
        if (paymentList.includes("kfpaypal")) {
            let _script = document.createElement('script');
            _script.type = 'text/javascript';
            _script.src = prestashop.urls.base_url + 'modules/kf_paypal/views/js/front.js';
            JqueryElement.append(_script);
        }

        //mollie - v5.0.1 - Mollie B.V.
        if (paymentList.includes("mollie")) {
            let $mollieContainers = JqueryElement.find('.mollie-iframe-container');
            if ($mollieContainers.length) {
                let _script = document.createElement('script');
                _script.type = 'text/javascript';
                _script.src = prestashop.urls.base_url + 'modules/mollie/views/js/front/mollie_iframe.js';
                JqueryElement.append(_script);
            }
        }

        //hipay_enterprise - v2.17.0 - HiPay
        if (typeof initEventsHostedFields !== typeof undefined && typeof initHostedFields !== typeof undefined) {
            initEventsHostedFields();
            initHostedFields();
        }

        //a4pauthorizenet - v2.0.1 - Addons4Presta
        if (paymentList.includes("a4pauthorizenet")) {
            let _script = document.createElement('script');
            _script.type = 'text/javascript';
            _script.src = prestashop.urls.base_url + '/modules/a4pauthorizenet/views/js/a4pauthorizenet.js';
            JqueryElement.append(_script);
        }
    });

    prestashop.on('opc-payment-toggleOrderButton-after', (params) => {
        const { JqueryElement, selectedOption, confirmationSelector, showConfirmationSelector } = params;

        //paypal - v5.4.7 - 202 ecommerce
        if (typeof JqueryElement !== typeof undefined && typeof confirmationSelector !== typeof undefined) {
            if (JqueryElement.find('input[name="payment-option"]:checked').attr("data-module-name") === 'paypal') {
                const paypalEcWrongButtonMessage = JqueryElement.find('[paypal-ec-wrong-button-message]');
                if (paypalEcWrongButtonMessage.length > 0) {
                    JqueryElement.find(confirmationSelector + ' button').on('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        paypalEcWrongButtonMessage.show();
                    });
                }
            }
        }

        //ets_payment_with_fee - v2.2.9 - ETS-Soft.
        if (typeof ets_cookie_module_name !== typeof undefined) {
            ets_cookie_payment_option = false;
            ajaxChangePaymentMethod = function (){};

            if (typeof ajaxChangePaymentMethodOPC !== typeof undefined) {
                ajaxChangePaymentMethodOPC(params);
            } else {
                ajaxChangePaymentMethodOPC = function(params) {
                    const { JqueryElement, selectedOption } = params;

                    if (typeof selectedOption !== typeof undefined) {
                        const $paymentSelected = JqueryElement.find('input[name="payment-option"]:checked');
                        const moduleName = $paymentSelected.data('module-name');
                        let paymentMethodId = 0;

                        if (JqueryElement.find('#pay-with-' + selectedOption + '-form input[name="id_payment_method"]').length > 0) {
                            paymentMethodId = JqueryElement.find('#pay-with-' + selectedOption + '-form input[name="id_payment_method"]').val();
                        }

                        $.ajax({
                            url: '',
                            data: 'ets_set_payment_option=1&module_name=' + moduleName + '&id_payment_method=' + paymentMethodId + '&payment_option=' + selectedOption,
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function () {
                                JqueryElement.append($('<div class="loading-opc"/>'));
                            },
                            complete: function () {
                                JqueryElement.find('.loading-opc').remove();
                            }
                        });
                    }
                };

                ajaxChangePaymentMethodOPC(params);
            }
        }

        //hipay_enterprise - v2.17.0 - HiPay
        if (typeof setSelectedPaymentMethod !== typeof undefined) {
            setSelectedPaymentMethod();
        }
    });

    prestashop.on('opc-payment-toggleOrderButton-validation', (params) => {
        const { JqueryElement, JqueryElementShipping, confirmationAlertSelector } = params;

        //glsshipping - v3.3.2 - GLS
        if (JqueryElementShipping.find('#parcelshopid').length > 0) {
            const parcelShopId = parseInt(JqueryElementShipping.find('#parcelshopid').val());
            const deliveryIdSelected = parseInt(JqueryElementShipping.find('.delivery_option_radio:checked').val());

            if (parcelShopId === deliveryIdSelected && JqueryElementShipping.find('#parcel_codigo').val() === '') {
                JqueryElement.data('opc-payment-toggleOrderButton-validation', false);

                $(confirmationAlertSelector).text(OPC.Message.pickupStoreRequired);

                let interval = setInterval(function () {
                    if (JqueryElementShipping.find('#parcel_codigo').val() !== '') {
                        JqueryElement.find('input[name="payment-option"]:checked').trigger('click');

                        clearInterval(interval);
                    }
                }, 1000);
            } else {
                JqueryElement.data('opc-payment-toggleOrderButton-validation', true);
            }
        }

        //gmparcellocker - v1.4.1 - GreenMouseStudio.com
        if (typeof window.gmparcellockerStoreSelected !== typeof undefined) {
            if (!window.gmparcellockerStoreSelected) {
                JqueryElement.data('opc-payment-toggleOrderButton-validation', false);

                $(confirmationAlertSelector).text(OPC.Message.pickupStoreRequired);

                let interval = setInterval(function () {
                    if (window.gmparcellockerStoreSelected) {
                        JqueryElement.find('input[name="payment-option"]:checked').trigger('click');

                        clearInterval(interval);
                    }
                }, 1000);
            } else {
                JqueryElement.data('opc-payment-toggleOrderButton-validation', true);
            }
        }
    });

    prestashop.on('opc-cart-getCartSummary-complete', (params) => {
        const { JqueryElement } = params;
    });
}
