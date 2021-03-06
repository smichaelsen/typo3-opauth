<?php
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$currentExtensionConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['opauth']);
$extPath = ExtensionManagementUtility::extPath('opauth');
$absolutePath = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'typo3/ajax.php?ajaxID=opauth&pluginName=authentification&controllerName=Authentification&actionName=authenticate&arguments%5Bstrategy%5D=';
$host = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
$relativePath = substr($absolutePath, strlen($host));
$frontendAbsolutePath = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'index.php?eID=opauth&extensionName=Opauth&logintype=login&pluginName=Auth&controllerName=Authentification&actionName=authenticate&arguments%5Bstrategy%5D=';
$frontendRelativePath = substr($frontendAbsolutePath, strlen($host));

//throw new \TYPO3\CMS\Core\Exception('relativePath: ' . $relativePath);
$enableStrategies = $currentExtensionConfig['enableStrategies'];

return array(

	/**
	 * Path where Opauth is accessed.
	 *  - Begins and ends with /
	 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
	 *  - if Opauth is reached via http://auth.example.org/, path is '/'
	 */
	'path' => $frontendRelativePath,
	'strategy_dir' => $extPath . 'ThirdParty/Strategies/',
	'lib_dir' =>  $extPath . 'ThirdParty/Opauth/lib/Opauth/',

	/**
	 * Callback URL: redirected to after authentication, successful or otherwise
	 */
	'callback_url' => '{path}callback',
	'callback_transport' => $currentExtensionConfig['callbackTransport'],

	/**
	 * Debug mode
	 */
	'debug' => $currentExtensionConfig['enableDebug'],

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
			'app_secret' => $currentExtensionConfig['facebookAppSecret'],
			'scope' => $currentExtensionConfig['facebookScope'],
		),

		'Google' => array(
			'client_id' => $currentExtensionConfig['googleClientId'],
			'client_secret' => $currentExtensionConfig['googleClientSecret'],
		),

		'Twitter' => array(
			'key' => $currentExtensionConfig['twitterConsumerKey'],
			'secret' => $currentExtensionConfig['twitterConsumerSecret'],
		),

	),
);
?>
