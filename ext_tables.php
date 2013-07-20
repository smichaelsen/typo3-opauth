<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// Add field to setup module
$GLOBALS['TYPO3_USER_SETTINGS']['columns']['tx_opauth_strategies'] = array(
	'type' => 'user',
	'table' => 'be_users',
	'label' => 'Authentification Services',
	'userFunc' => 'Butenko\\Opauth\\Controller\\UserSetupModuleController->renderFieldsAction',
);
ExtensionManagementUtility::addFieldsToUserSettings('--div--;Authentification Services,tx_opauth_strategies');

$extConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY]);

/**
 * Register as backend plugin
 */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Opauth',
	'authentification',
	'Opauth Authentification'
);

if (isset($extConf['enableBE']) && (bool)$extConf['enableBE']) {

    $TBE_STYLES['htmlTemplates']['templates/login.html'] = ExtensionManagementUtility::extPath('opauth') . 'Resources/Private/Templates/Login.html';
    $TBE_STYLES['stylesheet2'] = ExtensionManagementUtility::extPath('opauth') . 'Resources/Public/Stylesheets/opauth.css';
}

/**
 * Register as frontend plugin
 */

/*\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Opauth',
    'authentification',
    'Opauth Authentification'
);*/

?>
