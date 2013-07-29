<?php
namespace Butenko\Opauth\Hooks;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UserAuthHook {
    function postUserLookUp(&$params, &$caller) {
        if (TYPO3_MODE == 'BE') {
            $user = $GLOBALS['BE_USER'];
        } elseif (TYPO3_MODE == 'FE') {
            $user = $GLOBALS['FE_USER'];
        }
        if ($user) {
            //throw new \TYPO3\CMS\Core\Exception('$user:' . var_dump($user) );
            $this->setValidSession($user);
        }
    }

    /**
     * @param \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication $user
     * @return boolean TRUE if the user is already authenticated
     */
    function isValidSession($user) {
        return $user->getSessionData('opauthIsValid') === TRUE;
    }

    /**
     * @param \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication $user
     */
    function setValidSession($user) {
        $user->setAndSaveSessionData('opauthIsValid', TRUE);
    }

    /**
     * Render the form and exit execution
     *
     * @param string $token Provided (wrong) token
     */
    function showForm($token) {
        $error = ($token != '');

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $view->setTemplatePathAndFilename(
            ExtensionManagementUtility::extPath('opauth') . 'Resources/Private/Templates/Login.html'
        );
        $view->assign('error', $error);
        echo $view->render();
        die();
    }

}
