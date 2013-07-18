<?php
namespace Butenko\OAuth;

class OpauthService extends \TYPO3\CMS\Sv\AbstractAuthenticationService {

    public function __construct() {
        session_start();
        $this->config = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['opauth']['setup'];
    }

    /**
     * @return array User Array or FALSE
     */
    public function getUser() {
        $data = $_SESSION[$this->sessionKey]['user'][$this->scope];
        $url  = $this->api->createAuthUrl();
        if ($_POST['logintype'] === 'logout') {
            $this->logoff();
        } else
        if (isset($_POST['user']) === TRUE) {
            $this->setScope('fe');
            header("Location: {$url}");
            exit();
        } elseif (isset($_POST['username']) === TRUE) {
            $this->setScope('be');
            header("Location: {$url}");
            exit();
        } elseif ($data['email'] || $data['uid'] > 0) {
            if ($data['uid'] > 0) {
                return $data;
            } else {
                $username = $this->scope === 'fe' ? $data['email'] : substr($data['email'], 0, strpos($data['email'], '@'));
                return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', $this->scope . '_users', "username = '" . $username . "'");
            }
        }
    }

}

?>
