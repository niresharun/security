define(['jquery'], function ($) {
    'use strict';
    return function (config) {
        $('#submitbtn').on('click', function () {
            var type,postal_code,region;

            type = $('input[name="rep_type"]:checked').val();
            postal_code = $('#postal_code').val();
            region = $("#rep-regions option:selected").val();

            if ($('#postal_code').val() === '' && (region == 'us')) {
                $('.require-field-msg').css('display', 'block');
                $('#postal_code').trigger('focus');
                $('.require-field-msg').delay(3000).fadeOut('slow');
                return;
            } else {
                $('.require-field-msg').css('display', 'none');
            }

            $.ajax({
                url: config.repURL,
                type: 'POST',
                data: {type:type,postalcode:postal_code,region:region},
                cache: false
            }).done(function (data) {
                $('#rep-info-div').html(data['html']);
                return true;
            });
        });

        // keypress enter submit form
        $('#find_your_rep_form').on('keypress', function (e) {
            $('#find_your_rep_form').on('submit', () => {
                return false;
            });
            if (e.which === 13) {
                $('#submitbtn').trigger('click');
            }
        });

        $('#residential').on('click', function () {
            $('#residential').attr('checked', 'checked');
            $('#commercial').removeAttr('checked');
        });

        $('#commercial').on('click', function () {
            $('#commercial').attr('checked', 'checked');
            $('#residential').removeAttr('checked');
        });

        $("#rep-regions").change(function() {
                var optionValue = $(this).val();
                if (optionValue == 'us') {
                    $(".rep-postal-code-div").show();
                } else {
                    $(".rep-postal-code-div").hide();
                }
        });
    };
});
