<?php
namespace T3SEO\Opauth\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
class AuthentificationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \T3SEO\Opauth\Opauth
	 */
	protected $opauth;

	public function initializeAction() {
		$configuration = include(ExtensionManagementUtility::extPath('opauth') . 'Configuration/OpauthConfiguration.php');
		$this->opauth = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('T3SEO\\Opauth\\Opauth', $configuration, FALSE);
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

	public function callbackAction() {
		$response = $this->opauth->getResponse();
		// @todo: save the user token etc here
		$this->redirectToUri(ExtensionManagementUtility::extRelPath('opauth') . 'Resources/Public/Html/popupclose.html');
	}

}

?>