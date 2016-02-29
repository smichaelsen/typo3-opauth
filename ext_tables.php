<?php

$_EXTCONF = unserialize($TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY]);

// Add field to setup module
$GLOBALS['TYPO3_USER_SETTINGS']['columns']['tx_opauth_strategies'] = array(
    'type' => 'user',
    'table' => 'be_users',
    'label' => 'Authentification Services',
    'userFunc' => \Smichaelsen\Opauth\UserFunction\SetupModuleControllerFieldProvider::class . '->renderFieldsAction',
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings('--div--;Authentification Services,tx_opauth_strategies');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Opauth');

/**
 * Register as backend plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Auth',
    'Opauth Authentification'
);

// Overwrite backend login form template
if (isset($_EXTCONF['enableBE']) && (bool)$_EXTCONF['enableBE']) {
    $TBE_STYLES['htmlTemplates']['templates/login.html'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Resources/Private/Templates/Login.html';
    $TBE_STYLES['htmlTemplates']['EXT:backend/Resources/Private/Templates/login.html'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Resources/Private/Templates/Login.html';
    $TBE_STYLES['stylesheet2'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Stylesheets/opauth.css';
}

// Replace the default user module to laod required js modules
$GLOBALS['TBE_MODULES']['_configuration']['user_setup']['routeTarget'] = \Smichaelsen\Opauth\Controller\SetupModuleController::class . '::mainAction';
