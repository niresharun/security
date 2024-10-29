require([
    'jquery',
    'mage/url',
    'Magento_Ui/js/modal/modal'
], function ($,urlBuilder, modal) {
    (function ($){

        $(document).ready(function () {

            function addToWishlistAjax(dataJson) {
                var parseData = JSON.parse(dataJson);
                var wishlist_id = parseData.data.wishlist_id;
                var product = parseData.data.product;
                var pz_cart_properties = parseData.data.pz_cart_properties;

                $.ajax({
                    showLoader: true,
                    url: urlBuilder.build("wishlist/index/add/"),
                    data: {
                        ajax : 1,
                        wishlist_id: wishlist_id,
                        product : product,
                        pz_cart_properties : pz_cart_properties,
                        form_key: $.mage.cookies.get('form_key')
                    },
                    type: "POST"
                }).done(function (data) {
                    var successMessage =  '<div id = "wishlist-success-content">' + data.message
                        + ' has been added successfully.';
                    if (data.relatedItems){
                        successMessage +=  '<br> <span><a href="#" id="' +
                            product + '"class="action product-set-wishlist">ADD COLLECTION</a></span>';
                    }
                    successMessage += '</div>';
                    $('body').append(successMessage);

                    var options = {
                        type: 'popup',
                        responsive: true,
                        innerScroll: false,
                        buttons: []
                    };
                    var popup = modal(options, $('#wishlist-success-content'));
                    $("#wishlist-success-content").parents('.modal-inner-wrap').addClass('success-popup-section');
                    $("#wishlist-success-content").parent().parent().parent().addClass('style-popup-section');
                    if(data.success){
                        $('#wishlist-success-content').modal('openModal');
                    }
                    return false;
                });
            }

            if ($('body').is('.catalog-category-view, .catalogsearch-result-index ')) {
                $('body').on('click','.wishlist button.split',function (e) {
                    var dataJson = $(this).attr('data-post');
                    $('#wishlist-success-content').remove();
                    e.preventDefault();
                    addToWishlistAjax(dataJson);

                    return false;
                });

                $('body').on('click','.wishlist ul.items li',function (e) {
                    var dataPost = $(this).find('.existing-list').attr('data-post');
                    $('#wishlist-success-content').remove();
                    e.preventDefault();
                    addToWishlistAjax(dataPost);

                    return false;
                });
            }
        });
    })(jQuery);
});
