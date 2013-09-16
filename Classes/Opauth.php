<?php
namespace Butenko\Opauth;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('opauth') . 'ThirdParty/Opauth/lib/Opauth/Opauth.php');

/**
 * Wrapper for the Opauth class to make autoloading possible and make it singleton
 */
class Opauth extends \Opauth implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var string
	 */
	protected $extKey = 'opauth';

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

	public function getResponse() {
		$response = NULL;
		switch ($this->env['callback_transport']) {
			case 'session':
				session_start();
				$response = $_SESSION[$extKey];
				unset($_SESSION[$extKey]);
				break;
			case 'post':
				$response = unserialize(base64_decode($_POST['opauth']));
				break;
			case 'get':
				$response = unserialize(base64_decode($_GET['opauth']));
				break;
			default:
				throw new \TYPO3\CMS\Core\Exception('Unsupported callback_transport: ' . htmlspecialchars($this->env['callback_transport']));
				break;
		}
		return $response;
	}

}

?>
