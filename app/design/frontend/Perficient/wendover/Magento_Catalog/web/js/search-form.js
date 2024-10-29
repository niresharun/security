define(['jquery'], function($){
    "use strict";
    return function (config, element) {
        if ($('#search_sku').val()) {
            $('#resetSearch').css('display', 'block');
        }
        $( "#resetSearch" ).on( "click", function() {
            $('#search_sku').val('');
            var url = window.location.href;
            url = removeQueryParam('searchSku', url);
            window.location.replace(url);
        });
        var input = document.getElementById("search_sku");
        if (input !== null) {
            input.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("submitbtn").click();
                }
            });
        }
        $( "#submitbtn" ).on( "click", function() {
            var searchSku = $('#search_sku').val();
            if (searchSku.length > 0 && searchSku.length < 3) {
                $('#searchsku-error-message').css('display', 'block');
            } else {
                $('#searchsku-error-message').css('display', 'none');
                var url = new URL(config.current_url);
                url.searchParams.set("searchSku", searchSku);
                var newURL = url.href;
                newURL = removeQueryParam('p', newURL);
                window.location.replace(newURL);
            }
        });
    }

    function removeQueryParam(removeParam, url){
        var urlparts= url.split('?');
        if (urlparts.length>=2) {
            var prefix= encodeURIComponent(removeParam)+'=';
            var pars= urlparts[1].split(/[&;]/g);
            for (var i= pars.length; i-- > 0;) {
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }
            url= urlparts[0]+'?'+pars.join('&');
            return url;
        } else {
            return url;
        }
    }
});
