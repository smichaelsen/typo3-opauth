$(function() {
    jQuery('[data-authstrategy]').click(function() {
        var el = $(this);
        var url = jQuery.param({
            eID: "opauth",
            extensionName: "Opauth",
            pluginName: "pi1",
            controllerName: "Authentification",
            actionName: el.data('action'),
            arguments: {
                strategy: el.data('authstrategy')
            }
        });

        //window.reload();
        //var url = '/index.php?eID=opauth&type=21071992&extensionName=Opauth&pluginName=pi1&controllerName=Authentification&actionName=' +  + '&arguments[strategy]=' + el.data('authstrategy');
        window.location.href = decodeURIComponent('/index.php?' + url);
        return false;
    });
});
