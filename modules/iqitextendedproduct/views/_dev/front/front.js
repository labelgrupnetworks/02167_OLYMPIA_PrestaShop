import Tridi from  './lib/threesixty/threesixty.min';

$(document).ready(() => {
    var threesixty;
    let videoIframes,
        $videoIframesWrapper,
        $videoModal = $('#iqit-iqitvideos-modal'),
        $threesixtyModal = $('#iqit-threesixty-modal');

    $threesixtyModal.on('shown.bs.modal', function() {

        var speed = 70;
        if (typeof iqitextendedproduct.speed !== 'undefined') {
            speed = iqitextendedproduct.speed;
        }


        if (typeof threesixty !== 'undefined') {
            threesixty.autoplayStart();
        } else{
           threesixty = new Tridi({
                element: '#iqit-threesixty',
                images: $('#iqit-threesixty').data('threesixty'),
                autoplay: true,
                autoplaySpeed: speed,
                stopAutoplayOnClick: true,
                stopAutoplayOnMouseenter: true,
                resumeAutoplayOnMouseleave: true,
                resumeAutoplayDelay: 500
            });
            threesixty.load();
        }

    });

    $threesixtyModal.on('hidden.bs.modal', function() {
        threesixty.autoplayStop();
    });

    $videoModal.on('shown.bs.modal', function() {
        $videoIframesWrapper = $('#iqitvideos-block');
        videoIframes = $videoIframesWrapper.html();
    });

    $videoModal.on('hidden.bs.modal', function() {
        $videoIframesWrapper.html(videoIframes);
    });

});
