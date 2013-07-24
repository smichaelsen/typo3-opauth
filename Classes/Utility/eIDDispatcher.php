<?php
namespace Butenko\Opauth\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Connect to datebase
 */
\TYPO3\CMS\Frontend\Utility\EidUtility::connectDB();

/**
 * Init TSFE for database access
 */
$GLOBALS['TSFE'] = GeneralUtility::makeInstance('tslib_fe', $TYPO3_CONF_VARS, 0, 0, true);
$GLOBALS['TSFE']->sys_page = GeneralUtility::makeInstance('t3lib_pageSelect');
$GLOBALS['TSFE']->initFEuser();
$GLOBALS['TSFE']->determineId();
$GLOBALS['TSFE']->getCompressedTCarray();
$GLOBALS['TSFE']->initTemplate();
$GLOBALS['TSFE']->getConfigArray();
/** @var $dispatcher \Butenko\Opauth\Utility\AjaxDispatcher */
$dispatcher = GeneralUtility::makeInstance('Butenko\\Opauth\\Utility\\AjaxDispatcher');
$dispatcher->initAndDispatch();

?>
