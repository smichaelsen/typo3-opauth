<?php
namespace T3SEO\Opauth\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
class AuthentificationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \T3SEO\Opauth\Opauth
	 * @inject
	 */
	protected $opauth;

	public function initializeAction() {
		$configuration = include(ExtensionManagementUtility::extPath('opauth') . 'Configuration/OpauthConfiguration.php');
		$this->opauth->setConfig($configuration);
	}

	/**
	 * @param string $strategy
	 */
	public function authenticateAction($strategy) {
		if(strpos($strategy, '/int_callback') !== FALSE) {
			$this->forward('callback');
		}
		$this->opauth->setStrategy($strategy);
		$this->opauth->run();
	}

	/**
	 * @param string $strategy
	 */
	public function callbackAction($strategy) {
		$this->opauth->setStrategy('callback');
		$this->opauth->run();
		die('callback!');
	}

}

?>