var config = {
    map: {
        '*': {
            fabric  :'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/2.3.3/fabric.min.js',
            artworkIndex: 'DCKAP_Productimize/js/artwork-index',
            artworkCore: 'DCKAP_Productimize/js/artwork-core',
            customisedOptions: 'DCKAP_Productimize/js/customise-options',
            selectric:'DCKAP_Productimize/js/selectric',
            owlCarousel:'DCKAP_Productimize/js/owl.carousel',
            //'Magento_Checkout/template/minicart/item/default.html': 'DCKAP_Productimize/template/minicart/item/default.html',
            //'Magento_Checkout/template/summary/item/details.html': 'DCKAP_Productimize/template/summary/item/details.html',
            customizerCommon:'DCKAP_Productimize/js/customizer-common',
        }
    },
    /*paths: {
        'pdfjs-dist/build/pdf': 'https://devcloud.productimize.com/Promize3d/libraries/pdf',
        'pdfjs-dist/build/pdf.worker': 'https://devcloud.productimize.com/Promize3d/libraries/pdf.worker',
    },*/
    config: {
        mixins: {
            'Magento_Wishlist/js/add-to-wishlist': {  // Target module
                'DCKAP_Productimize/js/add-to-wishlist-mixin': true  // Extender module
            },
            'Magento_Catalog/js/catalog-add-to-cart': {
                'DCKAP_Productimize/js/catalog-add-to-cart-mixin': true  // Extender module
            }
        }
    }
}
