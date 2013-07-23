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
	protected $opauthService;

	/**
	 * @param \Butenko\Opauth\Service\Authentification $opauthService
	 */
	public function injectAuthService(\Butenko\Opauth\Service\Authentification $opauthService) {
		$this->opauthService = $opauthService;
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
			echo '<strong style="color: red;">Authentication error: </strong> Opauth returns error auth response.'."<br>\n";
		}
		else
		{
			if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid']))
			{
				echo '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.'."<br>\n";
			}
			elseif (!$this->opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason))
			{
				echo '<strong style="color: red;">Invalid auth response: </strong>'.$reason.".<br>\n";
			}
			else
			{
				echo '<strong style="color: green;">OK: </strong>Auth response is validated.'."<br>\n";
				$this->setConnectedStrategy(strtolower($response['auth']['provider']));
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
