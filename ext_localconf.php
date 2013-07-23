<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$_EXTCONF = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService($_EXTKEY, 'auth', 'Butenko\\Opauth\\Service\\Authentification',
	array(
		'title' => 'Opauth Authentication',
		'description' => 'Opauth authentication service for Frontend and Backend',
		'subtype' => 'getUserFE,authUserFE,getUserBE,authUserBE',
		'available' => TRUE,
		'priority' => $_EXTCONF['priority'],
		// Must be higher than for tx_sv_auth (50) or tx_sv_auth will deny request unconditionally
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'className' => 'Butenko\\Opauth\\Service\\Authentification'
	)
);

if (TYPO3_MODE === 'BE') {
	// AJAX Extbase Dispatcher
	$TYPO3_CONF_VARS['BE']['AJAX']['opauth'] = 'Butenko\\Opauth\\Utility\\AjaxDispatcher->initAndDispatch';
	// Add popup js to user setup module
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/setup/mod/index.php']['setupScriptHook']['opauth'] = 'Butenko\\Opauth\\Controller\\UserSetupModuleController->jsAction';
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		$_EXTKEY,
		'Auth',
		array(
			'Authentification' => 'authenticate,callback',
		),
		array(
			'Authentification' => 'authenticate,callback',
		)
	);
}

if (TYPO3_MODE === 'FE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		$_EXTKEY,
		'Authentification',
		array(
			'Authentification' => 'authenticate,callback',
		),
		array(
			'Authentification' => 'authenticate,callback',
		)
	);
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'][] = 'Butenko\\Opauth\\UserFunction\\Logoff->logoff';
?>
