import $ from 'jquery';
import prestashop from 'prestashop';
import ProductMinitature from './product-miniature';


export default function ($productDiv) {

    let initialize = false;
    let infiniteWaypoint;
    let pendingQuery = false;

    let initWaypoint = () => {


        let loadPrevProducts = () => {
            let urlPrev = $infiniteUrlButtonPrev.attr('href');
            let slightlyDifferentPrevURL = [urlPrev, urlPrev.indexOf('?') >= 0 ? '&' : '?', 'from-xhr'].join('');

            $.get(slightlyDifferentPrevURL, null, null, 'json')
                .then(function (data) {
                        pendingQuery = false;
                        let $productList = $('#js-product-list');


                        $productList.find('.products').first().prepend($(data.rendered_products).find('.products').first().html());

                        prestashop.emit('afterUpdateProductList');

                        var $newInfiniteUrlButton = $(data.rendered_products).find('#infinity-url-prev');

                        if ($newInfiniteUrlButton.length) {
                            $infiniteUrlButtonPrev = $newInfiniteUrlButton;
                            loadPrevProducts();
                        }
                    }
                );

        };

        var element = $('#js-product-list').find('.products')[0];
        let $infiniteUrlButtonPrev = $('#infinity-url-prev');
        let $infiniteUrlButtonNext = $('#infinity-url-next');
        var options = {
            element: element,
            handler: function (direction) {
                if (direction == 'down') {



                    let urlNext = $infiniteUrlButtonNext.attr('href');
                    let slightlyDifferentNextURL = [urlNext, urlNext.indexOf('?') >= 0 ? '&' : '?', 'from-xhr'].join('');



                    $productDiv.addClass('-infinity-loading');
                    infiniteWaypoint.destroy();
                    initialize = false;

                    //next
                    $.get(slightlyDifferentNextURL, null, null, 'json')
                        .then(function (data) {
                                pendingQuery = false;
                                history.pushState({}, '', urlNext);
                                let $productList = $('#js-product-list');

                                $productList.find('.products').first().append($(data.rendered_products).find('.products').first().html());
                                $productList.find('.pagination').first().replaceWith($(data.rendered_products).find('.pagination').first());
                                $('#js-product-list-bottom').replaceWith(data.rendered_products_bottom);

                                let productMinitature = new ProductMinitature();
                                productMinitature.init();

                                $productDiv.removeClass('-infinity-loading');
                                prestashop.emit('afterUpdateProductList');

                                var $newInfiniteUrlButton = $(data.rendered_products).find('#infinity-url-next');

                                if ($newInfiniteUrlButton.length) {
                                    $infiniteUrlButtonNext = $newInfiniteUrlButton;
                                    infiniteWaypoint = new Waypoint(options);
                                    initialize = true;
                                } else{
                                    if (initialize){
                                        infiniteWaypoint.destroy();
                                    }
                                }
                            }
                        );
                }
            },
            offset: 'bottom-in-view'
        };


        if ($infiniteUrlButtonNext.length) {
            infiniteWaypoint = new Waypoint(options);
            initialize = true;
        }

        if ($infiniteUrlButtonPrev.length) {
            loadPrevProducts();
        }

    };

    initWaypoint();

    prestashop.on('afterUpdateProductListFacets', () => {
        if (initialize){
            infiniteWaypoint.destroy();
        }
        initWaypoint();

    });

}





