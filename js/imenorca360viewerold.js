window.onload = function () {
    var
        panorama = document.getElementsByClassName('panorama')
    ;

    for (var i = 0; i < panorama.length; i++) {
        var pan = panorama[i];
        loadPanorama(pan);
    }

    // Load the predefined panorama
    function loadPanorama(container) {
        PSV = new PhotoSphereViewer({
            // Path to the panorama
            panorama: container.innerText,

            // Container
            container: container,

            // Deactivate the animation
            time_anim: 0,

            // Display the navigation bar
            navbar: true,

            // Resize the panorama
            size: {
                width: '100%',
            },

            loading_html: '<div class="center" style="width:97%;margin:22% 0;position:relative">' +
            ' <div class="preloader-wrapper big active" style="position: absolute; top:50%; bottom:50%">\n' +
            '    <div class="spinner-layer spinner-blue-only">\n' +
            '      <div class="circle-clipper left">\n' +
            '        <div class="circle"></div>\n' +
            '      </div><div class="gap-patch">\n' +
            '        <div class="circle"></div>\n' +
            '      </div><div class="circle-clipper right">\n' +
            '        <div class="circle"></div>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>' +
            '</div>',

            // Disable smooth moves to test faster
            smooth_user_moves: true

        });
    }
};