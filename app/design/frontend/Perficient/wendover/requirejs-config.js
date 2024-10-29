var config = {
    map: {
        '*': {
            'slick': 'js/slick.min',
            'lazyload': 'js/jquery.lazyload.min'
        }
    },
    shim: {
        slick: {
            deps: ['jquery']
        },
        lazyload: {
            deps: ['jquery']
        }
    }
};