<?php
namespace Butenko\Opauth;

use \TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


class OpauthService extends \TYPO3\CMS\Sv\AuthenticationService {

	/**
	 * Mode to use
	 *
	 * @var string
	 */
	public $mode = 'getUserFE';

	/**
	 * @var string
	 */
	protected $sessionKey = 'opauth';

	/**
	 * @var array
	 */
	protected $config = array();

	/**
	 * @var string
	 */
	protected $scope = 'fe';

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		session_start();
		$this->config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['opauth']);

		$translation = array('{domain}' => $_SERVER['HTTP_HOST']);
		$urlNames = array('returnUrl');
		foreach ($urlNames as $urlName) {
			$this->config[$urlName] = strtr($this->config[$urlName], $translation);
		}

		$this->setScope(strtolower($_SESSION[$this->sessionKey]['currentScope'] ? $_SESSION[$this->sessionKey]['currentScope'] : TYPO3_MODE));
		$this->mode = $this->scope === 'be' ? 'getUserBE' : 'getUserFE';
	}

	/**
	 * @return void
	 */
	public function logoff() {
		unset($_SESSION[$this->sessionKey]['user'][$this->scope]);
	}

	/**
	 * @return void
	 */
	public function resetScope() {
		unset($_SESSION[$this->sessionKey]['currentScope']);
	}

	/**
	 * @param string $scope
	 * @return void
	 */
	public function setScope($scope) {
		$this->scope = $scope;
		$_SESSION[$this->sessionKey]['currentScope'] = $scope;
	}

	/**
	 * @return string
	 */
	public function getFinalUrl() {
		if ($this->scope === 'be') {
			return "/typo3/backend.php";
		} else {
			return $this->config['finalUrl'];
		}
	}

	/**
	 * @return string
	 */
	public function getErrorUrl() {
		return $this->config['errorUrl'];
	}

	/**
	 * @param mixed $user
	 */
	public function authUser(&$user) {
		if ($user['email']) {
			return 200;
		} else {
			return 100;
		}
	}

	/**
	 * @return array User Array or FALSE
	 */
	public function getUser() {
		$data = $_SESSION[$this->sessionKey]['user'][$this->scope];

		if ($_POST['logintype'] === 'logout') {
			$this->logoff();
		} else
		if (isset($_POST['user']) === TRUE) {
			$this->setScope('fe');
		} elseif (isset($_POST['username']) === TRUE) {
			$this->setScope('be');
		} elseif ($data['email'] || $data['uid'] > 0) {
			if ($data['uid'] > 0) {
				return $data;
			} else {
				$username = $this->scope === 'fe' ? $data['email'] : substr($data['email'], 0, strpos($data['email'], '@'));
				return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', $this->scope . '_users', "username = '" . $username . "'");
			}
		}
	}

	/**
	 * @return void
	 */
	public function getUserInformation($response) {
		if ($this->scope === 'fe') {
			$this->getFrontendUserInformation($response);
		} elseif ($this->scope === 'be') {
			$this->getBackendUserInformation($response);
		}
	}

	/**
	 * @return void
	 */
	public function getBackendUserInformation($response) {
		$username = substr($userInfo['email'], 0, strpos($userInfo['email'], '@'));
		$userInfo['email'] = filter_var($userInfo['email'], FILTER_SANITIZE_EMAIL);
		$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'be_users', "email = '" . $userInfo['email'] . "'");
		if ($record['disable'] > 0 || $record['deleted'] > 0) {
			return;
		}
		if (!$record) {
			// user has no DB record (yet), create one using defaults registered in extension config
			// password is not important, username is set to the user's default email address
			// fist though, we need to fetch that information from Google
			$record = array(
				'username' => $username,
				'password' => substr(sha1($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . (microtime(TRUE) * time())), -8),
				'realName' => $userInfo['name'],
				'email' => $userInfo['email'],
				'tstamp' => time(),
				'disable' => '0',
				'deleted' => '0',
				'pid' => 0,
				//'usergroup' => $this->config['addBeUsersToGroups'],
				'admin' => 1
			);
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $record);
			$uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
			$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'be_users', 'uid = ' . intval($uid));
		}
		$_SESSION[$this->sessionKey]['user']['be'] = $record;
	}

	/**
	 * @return void
	 */
	public function getFrontendUserInformation($response) {
		$userInfo = $response;
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
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $record);
			$uid = $GLOBALS['TYPO3_DB']->sql_insert_id();
			$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'fe_users', 'uid = ' . intval($uid));
		}
		$_SESSION[$this->sessionKey]['user']['fe'] = $record;
	}
}
?>
