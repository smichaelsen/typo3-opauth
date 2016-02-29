<?php
namespace Butenko\Opauth\Utility;

use TYPO3\CMS\Core\Exception;

/**
 * @var $TSFE \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
 */
$TSFE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $TYPO3_CONF_VARS, 0, 0, TRUE);
\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();
// Get FE User Information
$TSFE->initFEuser();
// Important: no Cache for Ajax stuff
$TSFE->set_no_cache();
//$TSFE->checkAlternativCoreMethods();
$TSFE->checkAlternativeIdMethods();
$TSFE->determineId();
$TSFE->getCompressedTCarray();
$TSFE->initTemplate();
//$TSFE->getConfigArray();
\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadConfigurationAndInitialize();

/**
 * Prepare user for auth.
 */
$loginData = array(
	'uname' => $_SESSION['opauth']['user']['fe']['username'],
	'uident' => $_SESSION['opauth']['user']['fe']['password'],
	'status' => 'login'
);

$GLOBALS['TSFE']->fe_user->checkPid = 0;
$info = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
$user = $GLOBALS['TSFE']->fe_user->fetchUserRecord( $info['db_user'], $loginData['uname'] );
if ( $GLOBALS['TSFE']->fe_user->compareUident($user,$loginData) );
{
	$GLOBALS["TSFE"]->fe_user->user = $GLOBALS["TSFE"]->fe_user->fetchUserSession();
	$GLOBALS['TSFE']->loginUser = 1;
	$GLOBALS['TSFE']->fe_user->fetchGroupData();
	$GLOBALS['TSFE']->fe_user->start();
	$GLOBALS["TSFE"]->fe_user->createUserSession($user);
	$GLOBALS["TSFE"]->fe_user->loginSessionStarted = TRUE;
}

/** @var $dispatcher \Butenko\Opauth\Utility\AjaxDispatcher */
$dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Butenko\\Opauth\\Utility\\AjaxDispatcher');
$dispatcher->initAndDispatch();
?>
