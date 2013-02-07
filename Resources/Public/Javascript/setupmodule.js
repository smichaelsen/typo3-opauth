$(function() {
	$(document).on('click', '[data-authstrategy]', function() {
		var el = $(this);
		var url = '';
		var authpopup = window.open(
			url,
			'Authenticate with ' + el.data('authstrategy'),
			'width=640,height=480,resizable=yes'
		);
	});
});