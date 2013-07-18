<?php
namespace Butenko\OAuth;

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

	public function getResponse() {
		$response = NULL;
		switch ($this->env['callback_transport']) {
			case 'session':
				session_start();
				$response = $_SESSION['opauth'];
				unset($_SESSION['opauth']);
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

	/**
	 * @return void
	 */
	public function getUserInformation() {
		if ($this->scope === 'fe') {
			$this->getFrontendUserInformation();
		} elseif ($this->scope === 'be') {
			$this->getBackendUserInformation();
		}
	}

	/**
	 * @return void
	 */
	public function getFrontendUserInformation() {
		$userInfo = $this->authService->userinfo->get();
		$userInfo['email'] = filter_var($userInfo['email'], FILTER_SANITIZE_EMAIL);
		$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'fe_users', "username = '" . $userInfo['email'] . "' AND disable = 0 AND deleted = 0");
		if (!$record) {
				// user has no DB record (yet), create one using defaults registered in extension config
				// password is not important, username is set to the user's default email address
				// fist though, we need to fetch that information from Google
			$record = array(
				'username' => $userInfo['email'],
				'password' => substr(sha1($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . (microtime(TRUE) * time())), -8),
				'name' => $userInfo['name'],
				'email' => $userInfo['email'],
				'disable' => '0',
				'deleted' => '0',
				'pid' => $this->config['storagePid'],
				'usergroup' => $this->config['addUsersToGroups'],
				'tstamp' => time(),
			);
			if (t3lib_extMgm::isLoaded('extbase')) {
				$record['tx_extbase_type'] = $this->config['recordType'];
			}
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $record);
			$uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
			$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'fe_users', 'uid = ' . intval($uid));
		}
		$_SESSION[$this->sessionKey]['user']['fe'] = $record;
	}

}

?>
