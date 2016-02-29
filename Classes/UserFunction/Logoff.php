<?php
namespace Butenko\Opauth\UserFunction;

class Logoff {

    /**
     * @param mixed $params
     * @param mixed $reference
     */
    public function logoff($params, $reference) {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager  */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        /** @var $authService \Butenko\Opauth\OpauthService */
        $authService = $objectManager->get('Butenko\\Opauth\\OpauthService');
        $requestedFile = basename($_SERVER['REQUEST_URI']);
        $referer = basename($_SERVER['HTTP_REFERER']);
        if ($requestedFile === 'logout.php' || $referer === 'logout.php' || $referer === 'backend.php') {
            if ($reference instanceof \TYPO3\CMS\Core\Authentication\BackendUserAuthentication === TRUE) {
                $authService->setScope('be');
                $authService->logoff();
            }
        }
    }

}
