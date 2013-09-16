<?php
namespace Butenko\Opauth\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class UserSetupModuleController {

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
	public function __construct() {
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		/** @var $configurationUtility \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility */
		$configurationUtility = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\ConfigurationUtility');
		$nestedConfiguration = $configurationUtility->getCurrentConfiguration('opauth');
		$this->extensionConfiguration = $configurationUtility->convertValuedToNestedConfiguration($nestedConfiguration);
	}

	/**
	 * @param array $parameters
	 * @param \TYPO3\CMS\Setup\Controller\SetupModuleController $parent
	 * @return string
	 */
	public function renderFieldsAction(array $parameters, \TYPO3\CMS\Setup\Controller\SetupModuleController $parent) {
		$content = '';
		$strategiesToCheck = $this->extensionConfiguration['enableStrategies'];
		$connectedStrategies = $GLOBALS['BE_USER']->uc['connectedStrategies'];

		$strategiesToCheckArray = array_map('strtolower', \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $strategiesToCheck));
		foreach($strategiesToCheckArray as $strategy) {
			if ($this->extensionConfiguration[$strategy . 'Enable']) {
				$content .= '<h3>' . ucfirst($strategy) . '</h3>';

				if(array_key_exists($strategy, $connectedStrategies)) {
					$content .= '<span style="color: green;">Already connected</span> [<a href="#" data-action="disconnect" data-authstrategy="' . $strategy . '">disconnect</a>]';
				} else {
					$content .= '<a href="#" data-action="authenticate" data-authstrategy="' . $strategy . '">Authenticate with ' . ucfirst($strategy) .'</a>';
				}
			}
		}
		return $content;
	}

	/**
	 * @param array $parameters
	 * @param \TYPO3\CMS\Setup\Controller\SetupModuleController $parent
	 * @return string
	 */
	public function jsAction(array $parameters, \TYPO3\CMS\Setup\Controller\SetupModuleController $parent) {
		$jsCode = '<script src="contrib/jquery/jquery-1.8.2.js" type="text/javascript"></script>';
		$jsCode .= '<script src="' . ExtensionManagementUtility::extRelPath('opauth') . 'Resources/Public/Javascript/setupmodule.js" type="text/javascript"></script>';
		return $jsCode;
	}

}
?>
