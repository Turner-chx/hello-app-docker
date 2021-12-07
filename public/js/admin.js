$(document).ready(function () {
    let $imagePopup = $('.image-popup');
    if ($imagePopup.length > 0) {
        $imagePopup.magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            image: {
                verticalFit: false
            }
        });
    }
});