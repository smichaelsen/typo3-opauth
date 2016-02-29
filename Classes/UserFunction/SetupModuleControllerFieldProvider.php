<?php
namespace Smichaelsen\Opauth\UserFunction;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class SetupModuleControllerFieldProvider
{

    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['opauth']);
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
                if (isset($GLOBALS['BE_USER']->uc['opauth']['providers'][$strategy])) {
                    $content .= '<span style="color: green;">Successfully connected</span> [<a href="#" data-action="removeFromUC" data-scope="be" data-authstrategy="' . $strategy . '">disconnect</a>]';
                } else {
                    $content .= '<a href="#" data-action="saveToUC" data-scope="be" data-authstrategy="' . $strategy . '">Authenticate with ' . ucfirst($strategy) . '</a>';
                }
            }
        }
        return $content;
    }

}
