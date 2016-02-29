<?php
namespace Smichaelsen\Opauth\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * based on the AjaxDispatcher of EXT:pt_extbase, so credits to Daniel Lienert
 */
class AjaxDispatcher {

	/**
	 * @var string
	 */
	protected $vendorName = 'Smichaelsen';

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
		$bootstrap = GeneralUtility::makeInstance(Bootstrap::class);
		$bootstrap->initialize($configuration);

		$this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

		$request = $this->buildRequest();
		$response = $this->objectManager->get(Response::class);
		$dispatcher = $this->objectManager->get(Dispatcher::class);
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
		$validArguments = GeneralUtility::trimExplode(',', 'extensionName, pluginName, controllerName, actionName, arguments, vendorName, type');
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
		$request = $this->objectManager->get(Request::class);
		$request->setControllerExtensionName($this->extensionName);
		$request->setPluginName($this->pluginName);
		$request->setControllerName($this->controllerName);
		$request->setControllerActionName($this->actionName);
		$request->setControllerVendorName($this->vendorName);
		$request->setArguments($this->arguments);
		return $request;
	}

}
