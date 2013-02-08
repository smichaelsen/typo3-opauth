<?php
namespace T3SEO\Opauth;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('opauth') . 'ThirdParty/Opauth/lib/Opauth/Opauth.php');

/**
 * Wrapper for the Opauth class to make autoloading possible and make it singleton
 */
class Opauth extends \Opauth implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @param string $strategy
	 */
	public function setStrategy($strategy) {
		$this->env['params']['strategy'] = $strategy;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action) {
		$this->env['params']['action'] = $action;
	}

}

?>