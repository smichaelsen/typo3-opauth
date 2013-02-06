<?php
if (TYPO3_MODE === 'BE') {
	// AJAX Extbase Dispatcher
	$TYPO3_CONF_VARS['BE']['AJAX']['opauth'] = 'T3SEO\\Opauth\\Utility\\AjaxDispatcher->initAndDispatch';
}
?>