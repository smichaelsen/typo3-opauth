<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$_EXTCONF = unserialize($TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY]);

// Add field to setup module
$GLOBALS['TYPO3_USER_SETTINGS']['columns']['tx_opauth_strategies'] = array(
	'type' => 'user',
	'table' => 'be_users',
	'label' => 'Authentification Services',
	'userFunc' => 'Butenko\\Opauth\\Controller\\UserSetupModuleController->renderFieldsAction',
);
ExtensionManagementUtility::addFieldsToUserSettings('--div--;Authentification Services,tx_opauth_strategies');
ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Opauth');

/**
 * Register as backend plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Butenko.' . $_EXTKEY,
	'Authentification',
	'Opauth Authentification'
);

ExtensionManagementUtility::addPlugin(array(
	'LLL:EXT:opauth/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

if (isset($_EXTCONF['enableBE']) && (bool)$_EXTCONF['enableBE']) {

	$TBE_STYLES['htmlTemplates']['templates/login.html'] = ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Private/Templates/Login.html';
	$TBE_STYLES['stylesheet2'] = ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Stylesheets/opauth.css';
}
?>
