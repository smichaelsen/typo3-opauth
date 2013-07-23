$(function() {
	$(document).on('click', '[data-authstrategy]', function() {
		var el = $(this);
		var url = 'ajax.php?ajaxID=opauth&pluginName=authentification&controllerName=Authentification&actionName=' + el.data('action') + '&arguments[strategy]=' + el.data('authstrategy');
		var authpopup = window.open(
			url,
			'Authenticate with ' + el.data('authstrategy'),
			'width=640,height=480,resizable=yes'
		);
	});
    $(document).on('')
});
