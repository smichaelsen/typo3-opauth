<?php
namespace Butenko\Opauth\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class AuthentificationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \Butenko\Opauth\Opauth
	 */
	protected $opauth;

	/**
	 * @var \Butenko\Opauth\Service\Authentification
	 */
	protected $authService;

	/**
	 * @param \Butenko\Opauth\Service\Authentification $opauthService
	 */
	public function injectAuthService(\Butenko\Opauth\Service\Authentification $authService) {
		$this->authService = $authService;
	}

	public function initializeAction() {
		$configuration = include(ExtensionManagementUtility::extPath('opauth') . 'Configuration/OpauthConfiguration.php');
		$this->opauth = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Butenko\\Opauth\\Opauth', $configuration, FALSE);
	}

	/**
	 * @param integer $errorCode
	 */
	public function errorAction($errorCode) {

	}

	/**
	 * @param string $strategy
	 */
	public function authenticateAction($strategy) {
		$strategy = rtrim($strategy, '/');
		$action = '';
		if(strpos($strategy, '/') !== FALSE) {
			list($strategy, $action) = explode('/', $strategy);
		}
		$this->opauth->setStrategy($strategy);
		$this->opauth->setAction($action);
		if($strategy == 'callback') {
			$this->forward('callback');
		}
		$this->opauth->run();
	}

	/**
	 *
	 */
	public function callbackAction() {
		$response = $this->opauth->getResponse();

		if (array_key_exists('error', $response))
		{
			throw new \TYPO3\CMS\Core\Exception('Authentication error: Opauth returns error auth response.');
		}
		else
		{
			if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid']))
			{
				throw new \TYPO3\CMS\Core\Exception('Invalid auth response: Missing key auth response components.');
			}
			elseif (!$this->opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason))
			{
				throw new \TYPO3\CMS\Core\Exception('Invalid auth response: '.$reason);
			}
			else
			{
				// Action to auth
			}
		}

		$this->closePopup();
	}

	public function setConnectedStrategy($strategy) {
		$GLOBALS['BE_USER']->uc['connectedStrategies'][$strategy] = 1;
		$GLOBALS['BE_USER']->overrideUC();
		$GLOBALS['BE_USER']->writeUC();
	}

	public function disconnectAction() {
		$strategy = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('arguments')['strategy'];
		unset($GLOBALS['BE_USER']->uc['connectedStrategies'][$strategy]);
		$GLOBALS['BE_USER']->overrideUC();
		$GLOBALS['BE_USER']->writeUC();
		$this->closePopup();
	}

	public function closePopup() {
		echo '<html><head><title>Authentication success</title></head><body onload="opener.console.log(\'hi, im the popup and im finished\');window.close();"></body></html>';
		die();
	}

}

?>
