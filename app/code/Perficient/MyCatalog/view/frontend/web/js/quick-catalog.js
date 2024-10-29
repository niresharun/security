require(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function(
        $,
        modal
    ) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            buttons: []
        };

        var popup = modal(options, $('#popup-modal'));
        $("#click-me").on('click',function(){
            $("#popup-modal").modal("openModal");
            $('.modal-popup._inner-scroll .modal-inner-wrap').css('background-color','#f6f6f6');
            $(".popup-catalog-form").parents('.modal-inner-wrap').addClass('catalog-popup-section');

        });
    }
);