<?php
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$currentExtensionConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['opauth']);

$absolutePath = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'typo3/ajax.php?ajaxID=opauth&pluginName=authentification&controllerName=Authentification&actionName=authenticate&arguments[strategy]=';
$host = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
$relativePath = substr($absolutePath, strlen($host));

return array(

	/**
	 * Path where Opauth is accessed.
	 *  - Begins and ends with /
	 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
	 *  - if Opauth is reached via http://auth.example.org/, path is '/'
	 */
	'path' => $relativePath,
	'strategy_dir' => ExtensionManagementUtility::extPath('opauth') . 'ThirdParty/Strategies/',
	'lib_dir' => ExtensionManagementUtility::extPath('opauth') . 'ThirdParty/Opauth/lib/Opauth/',

	/**
	 * Callback URL: redirected to after authentication, successful or otherwise
	 */
	'callback_url' => '{path}callback',

	'callback_transport' => 'get',

	/**
	 * A random string used for signing of $auth response.
	 */
	'security_salt' => md5($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . '|opauth'),

	/**
	 * Strategy
	 * Refer to individual strategy's documentation on configuration requirements.
	 */
	'Strategy' => array(
		// Define strategies and their respective configs here

		'Facebook' => array(
			'app_id' => $currentExtensionConfig['facebookAppId'],
			'app_secret' => $currentExtensionConfig['facebookAppSecret']
		),

		'Google' => array(
			'client_id' => 'YOUR CLIENT ID',
			'client_secret' => 'YOUR CLIENT SECRET'
		),

		'Twitter' => array(
			'key' => 'YOUR CONSUMER KEY',
			'secret' => 'YOUR CONSUMER SECRET'
		),

	),
);
?>