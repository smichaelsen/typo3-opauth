<?php
namespace Butenko\Opauth;

class OpauthService extends \TYPO3\CMS\Sv\AbstractAuthenticationService {

    public function __construct() {
        session_start();
        $this->config = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['opauth']['setup'];
    }

}

?>
