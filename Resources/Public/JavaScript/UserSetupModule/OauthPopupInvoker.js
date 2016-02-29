/**
 * Module: TYPO3/CMS/Opauth/UserSetupModule/OauthPopupInvoker
 * URL link interaction
 */
define(['jquery'], function($) {
    'use strict';

    /**
     *
     * @type {{}}
     * @exports TYPO3/CMS/Opauth/UserSetupModule/OauthPopupInvoker
     */
    var OauthPopupInvoker = {};

    $(function() {
        $('[data-authstrategy]').click(function () {
            var el = $(this);
            var url = $.param({
                ajaxID: "opauth",
                pluginName: "Auth",
                controllerName: "Authentification",
                actionName: el.data('action'),
                scopetype: el.data('scope'),
                arguments: {
                    strategy: el.data('authstrategy')
                }
            });
            window.open(
                decodeURIComponent('ajax.php?' + url),
                'Authenticate with ' + el.data('authstrategy'),
                'width=640,height=480,resizable=yes'
            )
        });
    });

    return OauthPopupInvoker;
});
