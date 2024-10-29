require([
    'jquery',
    'mage/url',
    'mage/translate',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/modal'
], function($,urlBuilder, translate,customerData,modal) {
    (function ($){
        var loadCart = true;

        $(document).ready(
            function(){
                $(document).on('click','.product-set-wishlist',function() {
                    var productId = $(this).attr('id');
                    $('#wishlist-success-content').modal('closeModal');
                    $('#collection-success-content').remove();
                    $.ajax({
                        url: urlBuilder.build("wishlistset/product/addtowishlist"),
                        type: "POST",
                        data: {
                            product : productId
                        },
                        showLoader: true,
                        cache: false,
                        beforeSend: function() {
                            let parentDiv = $('div').find('[data-ui-id="message-success"]').parent();
                            if (parentDiv[0]) {
                                parentDiv.remove();
                            }
                        }
                    }).done(function (data) {
                        $('body').append('<div id = "collection-success-content"> Collection has been added successfully.</div>');
                        var options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: false,
                            buttons: []
                        };
                        var popup = modal(options, $('#collection-success-content'));
                        $("#collection-success-content").parents('.modal-inner-wrap').addClass('success-popup-section');
                        $("#collection-success-content").parent().parent().parent().addClass('style-popup-section');
                        if(data.errors === false) {
                            $('#collection-success-content').modal('openModal');
                        }
                    });
                    return false;
                });
                $(document).on('click','#product-set-cart',function() {
                    $.ajax({
                        url: urlBuilder.build("wishlistset/product/addtocart"),
                        type: "POST",
                        data: {},
                        showLoader: true,
                        cache: false,
                        success: function (data) {
                            loadCart = true;
                            /* MiniCart reloading */
                            var sections = ['cart'];
                            customerData.reload(sections,true);
                            if (data.redirectUrl) {
                                window.location.href = data.redirectUrl;
                            }
                        },
                        error: function (request, error)
                        {
                            console.log(error);
                        }
                    });
                    return false;
                });
                $('#product-addtocart-button').on('click', function(){
                    loadCart = true;
                })
            });
        $(document).ajaxComplete(function (event, xhr, settings) {
            if(loadCart) {
                if (settings.url.indexOf("customer/section/load/?sections=cart") > 0) {
                    loadCart = false;
                    /* MiniCart reloading */
                    var sections = ['cart'];
                    customerData.reload(sections, true);
                }
            }
        });

    })(jQuery);

});
