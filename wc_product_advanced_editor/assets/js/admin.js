jQuery(document).ready(function ($) {

    // Media Uploader
    var frame;
    var currentUploadTarget;

    $('.wc-pae-upload-btn').on('click', function (e) {
        e.preventDefault();
        currentUploadTarget = $(this).data('target');

        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Media',
            button: {
                text: 'Use this media'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected in the media frame...
        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();

            if (currentUploadTarget === 'featured') {
                $('#wc_pae_featured_image_id').val(attachment.id);
                $('#wc_pae_featured_image_preview').attr('src', attachment.url).removeClass('hidden');
                $('.wc-pae-upload-btn[data-target="featured"]').text('Change Image');
            } else if (currentUploadTarget === 'gallery') {
                $('#wc_pae_gallery_image_id').val(attachment.id);
                $('#wc_pae_gallery_image_preview').attr('src', attachment.url).removeClass('hidden');
                $('.wc-pae-upload-btn[data-target="gallery"]').text('Change Image');
            }
        });

        frame.open();
    });

    // Remove Image
    $('.wc-pae-remove-image').on('click', function (e) {
        e.preventDefault();
        var target = $(this).data('target');

        if (target === 'featured') {
            $('#wc_pae_featured_image_id').val('');
            $('#wc_pae_featured_image_preview').attr('src', '').addClass('hidden');
            $('.wc-pae-upload-btn[data-target="featured"]').text('Upload Image');
        } else if (target === 'gallery') {
            $('#wc_pae_gallery_image_id').val('');
            $('#wc_pae_gallery_image_preview').attr('src', '').addClass('hidden');
            $('.wc-pae-upload-btn[data-target="gallery"]').text('Upload Image');
        }
    });

    // Save Product
    $('.wc-pae-save-btn').on('click', function (e) {
        e.preventDefault();

        var productId = $('#wc_pae_product_id').val();
        if (!productId) return;

        $('.wc-pae-spinner').addClass('visible');
        $('.wc-pae-save-btn').prop('disabled', true);

        // Force TinyMCE to update textareas
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        var descriptionContent = $('#wc_pae_description').val();
        var shortDescriptionContent = $('#wc_pae_short_description').val();

        var categories = [];
        $('input[name="tax_input[product_cat][]"]:checked').each(function () {
            categories.push($(this).val());
        });

        var data = {
            action: 'wc_pae_save_product',
            security: wc_pae_vars.nonce,
            product_id: productId,
            product_name: $('#wc_pae_product_name').val(),
            product_description: descriptionContent,
            product_short_description: shortDescriptionContent,
            featured_image_id: $('#wc_pae_featured_image_id').val(),
            gallery_image_id: $('#wc_pae_gallery_image_id').val(),
            'tax_input[product_cat]': categories
        };

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: data,
            success: function (response) {
                $('.wc-pae-spinner').removeClass('visible');
                $('.wc-pae-save-btn').prop('disabled', false);

                if (response.success) {
                    alert('Product updated successfully!');
                } else {
                    alert('Error saving: ' + response.data);
                }
            },
            error: function () {
                $('.wc-pae-spinner').removeClass('visible');
                $('.wc-pae-save-btn').prop('disabled', false);
                alert('Connection error');
            }
        });
    });

});
