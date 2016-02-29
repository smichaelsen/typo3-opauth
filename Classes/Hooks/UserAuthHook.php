<?php
namespace Smichaelsen\Opauth\Hooks;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class UserAuthHook
{

    function postUserLookUp()
    {
        if (TYPO3_MODE == 'BE') {
            $user = $GLOBALS['BE_USER'];
        } elseif (TYPO3_MODE == 'FE') {
            $user = $GLOBALS['FE_USER'];
        }
        if (isset($user)) {
            $this->setValidSession($user);
        }
    }

    /**
     * @param \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication $user
     * @return boolean TRUE if the user is already authenticated
     */
    function isValidSession($user)
    {
        return $user->getSessionData('opauthIsValid') === TRUE;
    }

    /**
     * @param \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication $user
     */
    function setValidSession($user)
    {
        $user->setAndSaveSessionData('opauthIsValid', TRUE);
    }

    /**
     * Render the form and exit execution
     *
     * @param string $token Provided (wrong) token
     */
    function showForm($token)
    {
        $error = ($token != '');

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            ExtensionManagementUtility::extPath('opauth') . 'Resources/Private/Templates/Login.html'
        );
        $view->assign('error', $error);
        echo $view->render();
        die();
    }

}
