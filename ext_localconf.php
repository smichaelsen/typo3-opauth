<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Unserialize extension configuration
$_EXTCONF = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

// Register opauth as authentification service
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService($_EXTKEY, 'auth', 'Butenko\\Opauth\\OpauthService',
	array(
		'title' => 'Opauth Authentication',
		'description' => 'Opauth authentication service for Frontend and Backend',
		'subtype' => 'getUserFE,authUserFE,getUserBE,authUserBE',
		'available' => TRUE,
		// Must be higher than for tx_sv_auth (50) or tx_sv_auth will deny request unconditionally
		'priority' => $_EXTCONF['priority'],
		'quality' => $_EXTCONF['priority'],
		'os' => '',
		'exec' => '',
		'className' => 'Butenko\\Opauth\\OpauthService'
	)
);

if (TYPO3_MODE === 'BE') {
	//
	$TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
	// AJAX Extbase Dispatcher
	$TYPO3_CONF_VARS['BE']['AJAX'][$_EXTKEY] = 'Butenko\\Opauth\\Utility\\AjaxDispatcher->initAndDispatch';
	// Add popup js to user setup module
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/setup/mod/index.php']['setupScriptHook'][$_EXTKEY] = 'Butenko\\Opauth\\Controller\\UserSetupModuleController->jsAction';
}

// Configure plugin for backend and frontend
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	$_EXTKEY,
	'Auth',
	array(
		'Authentification' => 'authenticate,callback,final',
	),
	array(
		'Authentification' => 'authenticate,callback,final',
	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['extTablesInclusion-PostProcessing'];
// Add eID dispatcher
$TYPO3_CONF_VARS['FE']['eID_include'][$_EXTKEY] = 'EXT:opauth/Classes/Utility/EidDispatcher.php';
// Add form hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'][] = 'Butenko\\Opauth\\UserFunction\\Logoff->logoff';
?>
