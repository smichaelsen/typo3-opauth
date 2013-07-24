$(function() {
    $(document).on('click', '[data-authstrategy]', function() {
        var el = $(this);
        var url = '/index.php?eID=opauth&type=21071992&extensionName=Opauth&pluginName=pi1&controllerName=Authentification&actionName=' + el.data('action') + '&arguments[strategy]=' + el.data('authstrategy');
        window.location.href = url;
        return false;
    });
});
