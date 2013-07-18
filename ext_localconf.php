<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$TYPO3_CONF_VARS['EXTCONF']['opauth']['setup'] = unserialize($_EXTCONF);

$subTypes = array();

if ($_EXTCONF['enableBE']) {
	array_push($subTypes, 'getUserBE');
	array_push($subTypes, 'authUserBE');

	if (TYPO3_MODE === 'BE') {
		// AJAX Extbase Dispatcher
		$TYPO3_CONF_VARS['BE']['AJAX']['opauth'] = 'Butenko\\OAuth\\Utility\\AjaxDispatcher->initAndDispatch';
		// Add popup js to user setup module
		$TYPO3_CONF_VARS['SC_OPTIONS']['ext/setup/mod/index.php']['setupScriptHook']['opauth'] = 'Butenko\\OAuth\\Controller\\UserSetupModuleController->jsAction';
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
			'Opauth',
			'authentification',
			array(
				'Authentification' => 'authenticate,callback'
			)
		);
	}
}

if ($_EXTCONF['enableFE']) {
	array_push($subTypes, 'getUserFE');
	array_push($subTypes, 'authUserFE');

	ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.php', '_pi1', 'list_type', 0);
}

if (count($subTypes) > 0) {
	// Register Opauth authentication service with TYPO3
	ExtensionManagementUtility::addService($_EXTKEY, 'auth', 'Butenko\\OAuth\\OpauthService',
		array(
			'title' => 'Opauth Authentication',
			'description' => 'Opauth authentication service for Frontend and Backend',
			'subtype' => implode(',', $subTypes),
			'available' => TRUE,
			'priority' => 40,
			// Must be higher than for tx_sv_auth (50) or tx_sv_auth will deny request unconditionally
			'quality' => 50,
			'os' => '',
			'exec' => '',
			'className' => 'Butenko\\OAuth\\OpauthService'
		)
	);
}
unset($subTypes);

?>
