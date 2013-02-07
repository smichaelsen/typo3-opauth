<?php
namespace T3SEO\Opauth\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
class AuthentificationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \T3SEO\Opauth\Opauth
	 * @inject
	 */
	protected $opauth;

	/**
	 * @param string $strategy
	 */
	public function authenticateAction($strategy) {
		$configuration = include(ExtensionManagementUtility::extPath('opauth') . 'Configuration/OpauthConfiguration.php');
		$this->opauth->setConfig($configuration);
		$this->opauth->setStrategy($strategy);
		$this->opauth->run();
	}

}

?>