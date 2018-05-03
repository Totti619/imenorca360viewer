var PSV;

function initPanorama(o) {
    $(".slider").slider({
        interval: 6000
        , height: parseInt(o.height)
    });
    for (var i = 0; i < document.getElementsByClassName("panorama").length; i++) {
        var pan = document.getElementsByClassName("panorama")[i];
        o.panorama = pan.innerText;
        o.container = pan;
        PSV = new PhotoSphereViewer(o);
    }
}

$(document).ready(function () {

    // Move the main div to the #columns div, in order to get fullwidth and in first position.
    $('#imenorca360viewer_block_home').prependTo('#columns');
});