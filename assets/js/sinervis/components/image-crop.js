/**
 * This file replaces the one from the bundle rares/image-crop:
 * public/bundles/raresimagecrop/js/image-crop.js
 */

import 'cropper';
import 'cropper/dist/cropper.min.css';

$(document).ready(function() {
    const vichFieldsClass = '.vich-image';
    const $cropFileInput = $(vichFieldsClass).find("#rares_image_crop_crop_file input");

    $cropFileInput.off('change').on('change', function() {
        const input = this;
        if (input.files && input.files[0]) {
            if (input.files[0].type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageFile = new Image();
                    imageFile.src = e.target.result.toString();

                    imageFile.onload = function() {
                        let $cropImage = $(input).closest(vichFieldsClass).find('#rares_image_crop_crop_image');
                        let $cropRotate = $(input).closest(vichFieldsClass).find("#rares_image_crop_crop_rotate");

                        $cropImage.cropper('destroy');
                        $cropImage.attr('src', this.src);
                            $cropImage.cropper();
                            $cropImage.show();
                            $cropRotate.show();
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    });

    $(vichFieldsClass).on('click', '.btn-rares-crop-left', function(e) {
        e.preventDefault();
        $(e.currentTarget).closest(vichFieldsClass).find("#rares_image_crop_crop_image").cropper('rotate', -15);
    });

    $(vichFieldsClass).on('click', '.btn-rares-crop-right', function(e) {
        e.preventDefault();
        $(e.currentTarget).closest(vichFieldsClass).find("#rares_image_crop_crop_image").cropper('rotate', 15);
    });

    $('#admin-form').on('submit', function(e) {
        $(vichFieldsClass).each(function(index, item){
            let $cropFileInput = $(item).find('#rares_image_crop_crop_file input');
            if($cropFileInput.val()) {
                let $cropImage = $(item).find('#rares_image_crop_crop_image');
                let $cropDataInput = $(item).find('#rares_image_crop_crop_data input');
                const data = $cropImage.cropper('getData');
                $cropDataInput.val(JSON.stringify(data));
            }
        })
    });
});
