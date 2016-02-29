<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Unserialize extension configuration
$_EXTCONF = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

// Register opauth as authentification service
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService($_EXTKEY, 'auth', \Smichaelsen\Opauth\OpauthService::class,
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
		'className' => \Smichaelsen\Opauth\OpauthService::class
	)
);

if (TYPO3_MODE === 'BE') {
	// Change BE loginSecurityLevel to normal
	$TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
	// AJAX Extbase Dispatcher
	$TYPO3_CONF_VARS['BE']['AJAX'][$_EXTKEY] = \Smichaelsen\Opauth\Utility\AjaxDispatcher::class . '->initAndDispatch';
	// Add popup js to user setup module
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/setup/mod/index.php']['setupScriptHook'][$_EXTKEY] = \Smichaelsen\Opauth\Controller\UserSetupModuleController::class . '->jsAction';
	$GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['BE_alwaysAuthUser'] = TRUE;
	$GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['BE_fetchUserIfNoSession'] = TRUE;
}

if (TYPO3_MODE === 'FE') {
	// Change FE loginSecurityLevel to normal
	$TYPO3_CONF_VARS['FE']['loginSecurityLevel'] = 'normal';
	$GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_alwaysAuthUser'] = TRUE;
	$GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'] = TRUE;
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


// Add eID dispatcher
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$_EXTKEY] = 'EXT:opauth/Classes/Utility/EidDispatcher.php';
// Add form hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] = \Smichaelsen\Opauth\Hooks\UserAuthHook::class . '->postUserLookUp';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing'][] = \Smichaelsen\Opauth\UserFunction\Logoff::class . '->logoff';
