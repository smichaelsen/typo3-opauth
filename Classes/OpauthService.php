<?php
namespace Smichaelsen\Opauth;

use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Sv\AbstractAuthenticationService;


class OpauthService extends AbstractAuthenticationService
{

    /**
     * @var string
     */
    protected $extKey = 'opauth';

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var string
     */
    protected $scope = 'be';

    /**
     * @var AbstractUserAuthentication
     */
    public $pObj;

    /**
     * @var string
     */
    public $mode;

    /**
     * @var array
     */
    public $login = array();

    /**
     * @var array
     */
    public $authInfo = array();

    /**
     * @var array
     */
    public $db_user = array();

    /**
     * @var array
     */
    public $db_groups = array();

    /**
     * @var boolean
     */
    public $writeAttemptLog = TRUE;

    /**
     * @var boolean
     */
    public $writeDevLog = TRUE;

    /**
     * @var array
     */
    protected $response;

    /**
     * @var array
     */
    protected $loginData;

    /**
     * CONSTRUCTOR
     */
    public function __construct()
    {
        session_start();
        $this->config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['opauth']);
        $this->setScope(strtolower(GeneralUtility::_GP('scopetype') ? GeneralUtility::_GP('scopetype') : $_SESSION[$this->extKey]['currentScope']));
        $this->mode = $this->scope === 'be' ? 'getUserBE' : 'getUserFE';
    }


    /**
     * Initializes authentication for this service.
     *
     * @param string $subType : Subtype for authentication (either "getUserFE" or "getUserBE")
     * @param array $loginData : Login data submitted by user and preprocessed by AbstractUserAuthentication
     * @param array $authInfo : Additional TYPO3 information for authentication services (unused here)
     * @param AbstractUserAuthentication $pObj Calling object
     * @return void
     */
    public function initAuth($subType, array $loginData, array $authInfo, AbstractUserAuthentication &$pObj)
    {
        $this->loginData = $loginData;
        $this->authInfo = $authInfo;
        $this->pObj = &$pObj;
        $this->loginData['status'] = 'login';
        $this->authInfo['loginType'] = strtoupper($this->scope);

    }

    /**
     * @param string $response : Response from auth service in controller
     * @return void
     */
    public function responseFromController($response)
    {
        $this->response = $response;
        $this->saveResponseToSession($response);
    }

    /**
     * Save response to $_SESSION
     * @param string $response
     * @return void
     */
    public function saveResponseToSession($response)
    {
        $provider = strtolower($response['auth']['provider']);
        $_SESSION[$this->extKey]['response'][$provider] = $response['auth'];
    }

    /**
     * @return void
     */
    public function logoff()
    {
        unset($_SESSION[$this->extKey]['user'][$this->scope]);
    }

    /**
     * @return void
     */
    public function resetScope()
    {
        unset($_SESSION[$this->extKey]['currentScope']);
    }

    /**
     * @param string $scope
     * @return void
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        $_SESSION[$this->extKey]['currentScope'] = $scope;
    }

    /**
     * @param array $user
     * @return int
     */
    public function authUser(array &$user)
    {
        $result = 100;
        if ($this->scope === 'be') {
            if (is_array($user)) {
                $result = 200;
                $this->writelog(255, 1, 0, 1, "[Opauth][BE] username with email: '%s' logged in successfully.", Array($user['email']));
            }
            if ($result == 100) {
                $this->writelog(255, 3, 3, 1, "[Opauth][BE] username with email: '%s' not logged in.", Array($user['email']));
            }
        }
        return $result;
    }

    /**
     * @return array User Array or FALSE
     */
    public function getUser()
    {
        $user = FALSE;
        if ($this->loginData['status'] == 'login') {
            $data = $_SESSION[$this->extKey]['user'][$this->scope];

            if ($_POST['logintype'] === 'logout') {
                $this->logoff();
            } else
                if (isset($_POST['user']) === TRUE) {
                    $this->setScope('fe');
                } elseif (isset($_POST['username']) === TRUE) {
                    $this->setScope('be');
                } elseif ($data['email'] || $data['uid'] > 0) {
                    if ($data['uid'] > 0) {
                        $user = $data;
                    } else {
                        $user = FALSE;
                    }
                }
        }
        return $user;
    }

    /**
     * @return void
     */
    public function getUserInformation()
    {
        if ($this->scope === 'fe') {
            $this->getFrontendUserInformation();
        } elseif ($this->scope === 'be') {
            $this->getBackendUserInformation();
        }
    }

    /**
     * @return string with unique password with 16 character
     */
    public function generatePassword()
    {
        return substr(sha1($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . (microtime(TRUE) * time())), -16);
    }

    /**
     * @param string $groupTitle : Get group by title or create.
     * @return int uid of group
     */
    public function getGroupOrCreate($groupTitle)
    {
        $group = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $this->scope . '_groups', "title = '" . $groupTitle . "'");
        if (!$group) {
            $pid = $this->scope === 'fe' ? $this->config['storagePid'] : 0;
            $group = array(
                'pid' => $pid,
                'title' => ucwords($groupTitle),
                'tstamp' => time(),
                'crdate' => time(),
                'deleted' => 0,
                'hidden' => 0,
                'description' => 'Group was created with Opauth App.',
            );
            $this->getDatabaseConnection()->exec_INSERTquery($this->scope . '_groups', $group);
            $uid = $this->getDatabaseConnection()->sql_insert_id();
            $group = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $this->scope . '_groups', 'uid = ' . intval($uid));
        }
        return $group['uid'];
    }

    /**
     * @throws Exception
     */
    public function getBackendUserInformation()
    {
        $userInfo = $this->response['auth']['info'];
        $provider = strtolower($this->response['auth']['provider']);
        if ($provider === 'twitter') {
            $username = $userInfo['nickname'];
            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'be_users', "username = '" . $username . "' AND disable = 0 AND deleted = 0");
        } else {
            $username = substr($userInfo['email'], 0, strpos($userInfo['email'], '@'));
            $userInfo['email'] = filter_var($userInfo['email'], FILTER_SANITIZE_EMAIL);
            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'be_users', "email = '" . $userInfo['email'] . "' AND disable = 0 AND deleted = 0");
        }

        if ($record['disable'] > 0 || $record['deleted'] > 0) {
            return;
        }
        if (!$record) {
            $groupArray = array_map('strtolower', GeneralUtility::trimExplode(',', $this->config['addBeUsersToGroups']));
            $groupIds = [];
            foreach ($groupArray as $title) {
                $groupIds[] = $this->getGroupOrCreate($title);
            }

            // user has no DB record (yet), create one using defaults registered in extension config
            // password is not important, username is set to the user's default email address
            // fist though, we need to fetch that information.
            $record = array(
                'username' => $username,
                'password' => $this->generatePassword(),
                'realName' => $userInfo['name'],
                'email' => $userInfo['email'],
                'tstamp' => time(),
                'crdate' => time(),
                'disable' => '0',
                'deleted' => '0',
                'pid' => 0,
                'usergroup' => implode(",", $groupIds),
                'admin' => $this->config['createAdminBeUsers']
            );
            $this->getDatabaseConnection()->exec_INSERTquery('be_users', $record);
            $uid = $this->getDatabaseConnection()->sql_insert_id();
            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'be_users', 'uid = ' . intval($uid));
        }
        if ($this->scope === 'be') {
            $_SESSION[$this->extKey]['user'][$this->scope] = $record;
        } else {
            throw new Exception('[BE] Scope is not correctly');
        }
    }

    /**
     * @throws Exception
     */
    public function getFrontendUserInformation()
    {
        $userInfo = $this->response['auth']['info'];
        $provider = strtolower($this->response['auth']['provider']);
        if ($provider === 'twitter') {
            $username = $userInfo['nickname'];
            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $this->scope . '_users', "username = '" . $username . "' AND disable = 0 AND deleted = 0");
        } else {
            $username = $userInfo['email'] = filter_var($userInfo['email'], FILTER_SANITIZE_EMAIL);
            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $this->scope . '_users', "username = '" . $userInfo['email'] . "' AND disable = 0 AND deleted = 0");
        }
        if (!$record) {
            // Check for exist group in DB or create if not-exist.
            $groupArray = array_map('strtolower', GeneralUtility::trimExplode(',', $this->config['addUsersToGroups']));
            $groupIds = [];
            foreach ($groupArray as $title) {
                $groupIds[] = $this->getGroupOrCreate($title);
            }

            // user has no DB record (yet), create one using defaults registered in extension config
            // password is not important, username is set to the user's default email address
            // fist though, we need to fetch that information.
            $record = array(
                'username' => $username,
                'password' => $this->generatePassword(),
                'name' => $userInfo['name'],
                'email' => $userInfo['email'],
                'disable' => '0',
                'deleted' => '0',
                'pid' => $this->config['storagePid'],
                'usergroup' => implode(",", $groupIds),
                'tstamp' => time(),
                'crdate' => time(),
            );
            if (ExtensionManagementUtility::isLoaded('extbase')) {
                $record['tx_extbase_type'] = $this->config['recordType'];
            }
            $this->getDatabaseConnection()->exec_INSERTquery('fe_users', $record);
            $uid = $this->getDatabaseConnection()->sql_insert_id();
            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $this->scope . '_users', 'uid = ' . intval($uid));
        }
        if ($this->scope === 'fe') {
            $_SESSION[$this->extKey]['user'][$this->scope] = $record;
        } else {
            throw new Exception('[' . strtoupper($this->scope) . '] Scope is not correctly');
        }
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @param string $scope
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
}
