jQuery(function() {
    jQuery('[data-authstrategy]').click(function() {
        var el = jQuery(this);
        var url = jQuery.param({
            eID: "opauth",
            extensionName: "Opauth",
            logintype: "login",
            pluginName: "Auth",
            controllerName: "Authentification",
            actionName: el.data('action'),
            scopetype: el.data('scope'),
            arguments: {
                strategy: el.data('authstrategy'),
            }
        });

        //window.reload();
        //var url = '/index.php?eID=opauth&type=21071992&extensionName=Opauth&pluginName=pi1&controllerName=Authentification&actionName=' +  + '&arguments[strategy]=' + el.data('authstrategy');
        window.location.href = decodeURIComponent('../index.php?'+url);
    });
});
