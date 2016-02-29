<?php
namespace Smichaelsen\Opauth\UserFunction;

use Smichaelsen\Opauth\OpauthService;
use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class Logoff {

    /**
     * @param array $params
     * @param AbstractUserAuthentication $reference
     */
    public function logoff($params, AbstractUserAuthentication $reference) {

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $authService = $objectManager->get(OpauthService::class);
        $requestedFile = basename($_SERVER['REQUEST_URI']);
        $referer = basename($_SERVER['HTTP_REFERER']);
        if ($requestedFile === 'logout.php' || $referer === 'logout.php' || $referer === 'backend.php') {
            if ($reference instanceof BackendUserAuthentication === TRUE) {
                $authService->setScope('be');
                $authService->logoff();
            }
        }
    }

}
