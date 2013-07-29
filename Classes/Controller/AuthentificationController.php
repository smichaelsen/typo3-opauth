<?php
namespace Butenko\Opauth\Controller;

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
		$this->response = $this->opauth->getResponse();

		if (array_key_exists('error', $this->response)) {
			throw new \TYPO3\CMS\Core\Exception('Authentication error: Opauth returns error auth response.');
		} else {
			if (empty($this->response['auth']) || empty($this->response['timestamp']) || empty($this->response['signature']) || empty($this->response['auth']['provider']) || empty($this->response['auth']['uid'])) {
				throw new \TYPO3\CMS\Core\Exception('Invalid auth response: Missing key auth response components.');
			} elseif (!$this->opauth->validate(sha1(print_r($this->response['auth'], true)), $this->response['timestamp'], $this->response['signature'], $reason)) {
				throw new \TYPO3\CMS\Core\Exception('Invalid auth response: '.$reason);
			} else {
				$this->authService->responseFromController($this->response);
				$this->authService->getUserInformation();
				$this->forward('final');
			}
		}
	}

	/**
	 * Final Action to redirect user to finalUrl
	 * @return void
	 */
	public function finalAction() {
		$finalUrl = $this->authService->getFinalUrl();
		$this->redirectToUri('typo3/init.php', 0, 303);
	}

}

?>
