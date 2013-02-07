<?php

// Add field to setup module
$GLOBALS['TYPO3_USER_SETTINGS']['columns']['tx_opauth_strategies'] = array(
	'type' => 'user',
	'table' => 'be_users',
	'label' => 'Authentification Services',
	'userFunc' => 'T3SEO\\Opauth\\Controller\\UserSetupModuleController->renderFieldsAction',
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings('--div--;Authentification Services,tx_opauth_strategies');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Opauth',
	'authentification',
	'Opauth Authentification'
);

?>