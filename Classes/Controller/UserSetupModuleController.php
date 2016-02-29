<?php
namespace Smichaelsen\Opauth\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

class UserSetupModuleController
{

    /**
     * @var string
     */
    protected $extKey = 'opauth';

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationUtility = $this->objectManager->get(ConfigurationUtility::class);
        $nestedConfiguration = $configurationUtility->getCurrentConfiguration('opauth');
        $this->extensionConfiguration = $configurationUtility->convertValuedToNestedConfiguration($nestedConfiguration);
    }

    /**
     * @return string
     */
    public function renderFieldsAction()
    {
        $content = '';
        $strategiesToCheck = 'Facebook, Twitter, Google';

        $strategiesToCheckArray = array_map('strtolower', GeneralUtility::trimExplode(',', $strategiesToCheck));
        foreach ($strategiesToCheckArray as $strategy) {
            if ($this->extensionConfiguration[$strategy . 'Enable']) {
                $content .= '<h3>' . ucfirst($strategy) . '</h3>';

                if (isset($GLOBALS['BE_USER']->uc[$this->extKey]['providers'][$strategy])) {
                    $content .= '<span style="color: green;">Successfully connected</span> [<a href="#" data-action="removeFromUC" data-scope="be" data-authstrategy="' . $strategy . '">disconnect</a>]';
                } else {
                    $content .= '<a href="#" data-action="saveToUC" data-scope="be" data-authstrategy="' . $strategy . '">Authenticate with ' . ucfirst($strategy) . '</a>';
                }
            }
        }
        return $content;
    }

    /**
     * @return string
     */
    public function jsAction()
    {
        $jsCode = '<script src="contrib/jquery/jquery-1.8.2.js" type="text/javascript"></script>';
        $jsCode .= '<script src="' . ExtensionManagementUtility::extRelPath('opauth') . 'Resources/Public/Javascript/setupmodule.js" type="text/javascript"></script>';
        return $jsCode;
    }

}
