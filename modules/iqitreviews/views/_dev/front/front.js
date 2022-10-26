import './lib/bootstrap-rating-input/bootstrap-rating-input.min';


iqitreviews.script = (function () {
    var $productReviewForm = $('#iqitreviews-productreview-form');

    return {
        'init': function () {

            if (iqitTheme.pp_tabs == 'tabh' || iqitTheme.pp_tabs == 'tabha') {

                $('#iqitreviews-rating-product').on('click', function () {
                    let element = document.getElementById("product-infos-tabs");
            
                    $('.nav-tabs a[data-iqitextra="iqit-reviews-tab"]').tab('show');

                    if(typeof(element) != 'undefined' && element != null){
                        element.scrollIntoView();
                    }

                });
            } else{

                $('#iqitreviews-rating-product').on('click', function () {
                    document.getElementById("iqit-reviews-tab").scrollIntoView();
                });
            }

            $productReviewForm.submit(function(e) {

                e.preventDefault();

                var $productReviewFormAlert = $('#iqitreviews-productreview-form-alert'),
                    $productReviewFormSuccessAlert = $('#iqitreviews-productreview-form-success-alert');


                $.post($(this).attr('action'), $(this).serialize(), null, 'json').then(function (resp) {

                    if (!resp.success){
                        let htmlResp = '<strong>' + resp.data.message + '</strong>';

                        htmlResp = htmlResp + '<ul>';
                        $.each(resp.data.errors, function( index, value ) {
                            htmlResp = htmlResp + '<li>' + value + '</li>';
                        });
                        htmlResp = htmlResp + '</ul>';

                        $productReviewFormAlert.html(htmlResp);
                        $productReviewFormAlert.removeClass('hidden-xs-up');
                    } else {
                        let htmlResp = '<strong>' + resp.data.message + '</strong>';
                        $productReviewFormSuccessAlert.html(htmlResp);
                        $productReviewFormSuccessAlert.removeClass('hidden-xs-up');
                        $('#iqit-reviews-modal').modal('hide');
                    }

                }).fail((resp) => {
                    $productReviewFormAlert.html(resp);
                    $productReviewFormAlert.removeClass('invisible');
                });

                e.preventDefault();

            });


        },
    };
})();

$(document).ready(() => {
        iqitreviews.script.init();
});


