jQuery(function() {
	jQuery('[data-authstrategy]').click(function() {
		var el = jQuery(this);
		var url = jQuery.param({
			ajaxID: "opauth",
			pluginName: "Auth",
			controllerName: "Authentification",
			actionName: el.data('action'),
			scopetype: el.data('scope'),
			arguments: {
				strategy: el.data('authstrategy'),
			}
		});

		var authpopup = window.open(
			decodeURIComponent('ajax.php?'+url),
			'Authenticate with ' + el.data('authstrategy'),
			'width=640,height=480,resizable=yes'
		)
	});
});
