<?php
namespace Smichaelsen\Opauth\Controller;

class SetupModuleController extends \TYPO3\CMS\Setup\Controller\SetupModuleController
{

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->moduleTemplate->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Opauth/UserSetupModule/OauthPopupInvoker');
    }

}
