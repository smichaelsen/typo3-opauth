<?php
namespace Butenko\Opauth\Controller;


use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class AuthentificationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \Butenko\Opauth\Opauth
	 */
	protected $opauth;

	/**
	 * @var \Butenko\Opauth\OpauthService
	 */
	protected $authService;

	/**
	 * @var array
	 */
	protected $response = array();

	/**
	 * @param \Butenko\Opauth\OpauthService $opauthService
	 */
	public function injectAuthService(\Butenko\Opauth\OpauthService $authService) {
		$this->authService = $authService;
	}

	/**
	 * @return void
	 */
	public function initializeAction() {
		$configuration = include(ExtensionManagementUtility::extPath('opauth') . 'Configuration/OpauthConfiguration.php');
		$this->opauth = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Butenko\\Opauth\\Opauth', $configuration, FALSE);
	}

	/**
	 * @param integer $errorCode
	 */
	public function errorAction($errorCode) {
		throw new \TYPO3\CMS\Core\Exception('Error action, with code: ' . $errorCode);
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
	 * Callback action with user data
	 * redirect to final url
	 * @return void
	 */
	public function callbackAction() {
		$response = $this->opauth->getResponse();
		if (array_key_exists('error', $response)) {
			throw new Exception('Authentication error: Opauth returns error auth response.');
		} else {
			if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid'])) {
				throw new Exception('Invalid auth response: Missing key auth response components.');
			} elseif (!$this->opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason)) {
				throw new Exception('Invalid auth response: '.$reason);
			} else {
				$this->authService->responseFromController($response);
				$this->authService->getUserInformation();
				$this->authService->authUser($response['auth']['info']);
				$this->forward('final');
			}
		}
	}

	/**
	 * Final Action to redirect user to finalUrl
	 * @return void
	 */
	public function finalAction() {
		\TYPO3\CMS\Core\Utility\GeneralUtility::cleanOutputBuffers();
		$backendURL = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . TYPO3_mainDir . 'backend.php';
		\TYPO3\CMS\Core\Utility\HttpUtility::redirect($backendURL);
		die();
	}

}

?>
