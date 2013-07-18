<?php
namespace Butenko\OAuth\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * based on the AjaxDispatcher of EXT:pt_extbase, so credits to Daniel Lienert
 */
class AjaxDispatcher {

	/**
	 * @var string
	 */
	protected $vendorName = 'Butenko';

	/**
	 * @var string
	 */
	protected $extensionName = 'Opauth';

	/**
	 * @var array
	 */
	protected $requestArguments = array();

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var string
	 */
	protected $pluginName;

	/**
	 * @var string
	 */
	protected $controllerName;

	/**
	 * @var string
	 */
	protected $actionName;

	/**
	 * @var array
	 */
	protected $arguments;

	/**
	 *
	 */
	public function initAndDispatch() {
		$this->initCallArguments();
		$this->dispatch();
	}

	/**
	 * @return string rendered content of the performed request string
	 */
	public function dispatch() {
		$configuration = array(
			'extensionName' => $this->extensionName,
			'pluginName' => $this->pluginName
		);
		/** @var $bootstrap \TYPO3\CMS\Extbase\Core\Bootstrap */
		$bootstrap = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Core\\Bootstrap');
		$bootstrap->initialize($configuration);

		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

		$request = $this->buildRequest();
		/** @var $response \TYPO3\CMS\Extbase\Mvc\Web\Response */
		$response = $this->objectManager->create('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Response');
		/** @var $dispatcher \TYPO3\CMS\Extbase\Mvc\Dispatcher */
		$dispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\Dispatcher');
		$dispatcher->dispatch($request, $response);

		$response->sendHeaders();
		return $response->getContent();
	}

	/**
	 *
	 */
	protected function initCallArguments() {
		$this->setRequestArgumentsFromGetPost();
		if (isset($this->requestArguments['vendorName'])) {
			$this->vendorName = $this->requestArguments['vendorName'];
		}
		if (isset($this->requestArguments['extensionName'])) {
			$this->extensionName = $this->requestArguments['extensionName'];
		}
		$this->pluginName = $this->requestArguments['pluginName'];
		$this->controllerName = $this->requestArguments['controllerName'];
		$this->actionName = $this->requestArguments['actionName'];
		$this->arguments = is_array($this->requestArguments['arguments']) ? $this->requestArguments['arguments'] : array();
	}

	/**
	 *
	 */
	protected function setRequestArgumentsFromGetPost() {
		$validArguments = GeneralUtility::trimExplode(',', 'extensionName, pluginName, controllerName, actionName, arguments, vendorName');
		foreach ($validArguments as $argument) {
			if (GeneralUtility::_GP($argument) !== NULL) {
				$this->requestArguments[$argument] = GeneralUtility::_GP($argument);
			}
		}
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	protected function buildRequest() {
		/** @var $request \TYPO3\CMS\Extbase\Mvc\Web\Request */
		$request = $this->objectManager->create('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Request');
		$request->setControllerExtensionName($this->extensionName);
		$request->setPluginName($this->pluginName);
		$request->setControllerName($this->controllerName);
		$request->setControllerActionName($this->actionName);
		$request->setControllerVendorName($this->vendorName);
		$request->setArguments($this->arguments);
		return $request;
	}

}

?>
