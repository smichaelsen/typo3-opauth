<?php
namespace Butenko\Opauth\UserFunction;

class Logoff {

    /**
     * @param mixed $params
     * @param mixed $reference
     */
    public function logoff($params, $reference) {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager  */
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        /** @var $authService \Butenko\Opauth\Service\Authentification */
        $authService = $this->objectManager->get('Butenko\\Opauth\\Service\\Authentification');
        $requestedFile = basename($_SERVER['REQUEST_URI']);
        $referer = basename($_SERVER['HTTP_REFERER']);
        if ($requestedFile === 'logout.php' || $referer === 'logout.php' || $referer === 'backend.php') {
            if ($reference instanceof t3lib_beUserAuth === TRUE) {
                $authService->setScope('be');
                $authService->logoff();
            }
        }
    }

}
