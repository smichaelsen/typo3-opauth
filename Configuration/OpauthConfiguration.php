<?php

$config = array(

	/**
	 * Path where Opauth is accessed.
	 *  - Begins and ends with /
	 *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
	 *  - if Opauth is reached via http://auth.example.org/, path is '/'
	 */
	'path' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('opauth') . 'Authenticate/',
	'strategy_dir' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('opauth') . 'ThirdParty/Strategies/',

	/**
	 * Callback URL: redirected to after authentication, successful or otherwise
	 */
	'callback_url' => '{path}callback.php',

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
			'app_id' => 'YOUR APP ID',
			'app_secret' => 'YOUR APP SECRET'
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