var lottiePlayerLoaded = false;

jQuery.cachedScript = function( url, options ) {

    options = $.extend( options || {}, {
        dataType: "script",
        cache: true,
        url: url
    });

    return jQuery.ajax( options );
};

function loadElementorLottiePlayer() {

    if(!lottiePlayerLoaded){
        $.cachedScript( "https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js" ).done(function( script, textStatus ) {
            console.log('Lottie player loaded');
            $.cachedScript( "https://unpkg.com/@lottiefiles/lottie-interactivity@0.1.3/dist/lottie-interactivity.min.js" ).done(function( script, textStatus ) {
                console.log('Lottie player loaded2');
                var event = new CustomEvent("lottieLoaded");
                document.dispatchEvent(event);
            });
        });

        lottiePlayerLoaded = true;
    }
}


