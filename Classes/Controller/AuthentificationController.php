<?php
namespace Smichaelsen\Opauth\Controller;

use Smichaelsen\Opauth\Opauth;
use Smichaelsen\Opauth\OpauthService;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AuthentificationController extends ActionController
{

    /**
     * @var string
     */
    protected $extKey = 'opauth';

    /**
     * @var Opauth
     */
    protected $opauth;

    /**
     * @var \Smichaelsen\Opauth\OpauthService
     */
    protected $authService;

    /**
     * @return void
     */
    public function initializeAction()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->authService = $objectManager->get(OpauthService::class);
        $configuration = include(ExtensionManagementUtility::extPath('opauth') . 'Configuration/OpauthConfiguration.php');
        $this->opauth = $objectManager->get(Opauth::class, $configuration, FALSE);
    }

    /**
     * @param int $errorCode
     * @return string|void
     * @throws Exception
     */
    public function errorAction($errorCode)
    {
        throw new Exception('Error action, with code: ' . $errorCode);
    }

    /**
     * @param string $strategy
     */
    public function authenticateAction($strategy)
    {
        $strategy = rtrim($strategy, '/');
        $action = '';
        if (strpos($strategy, '/') !== FALSE) {
            list($strategy, $action) = explode('/', $strategy);
        }
        $this->opauth->setStrategy($strategy);
        $this->opauth->setAction($action);
        if ($strategy == 'callback') {
            $this->forward('callback');
        }
        $this->opauth->run();
    }

    /**
     * Callback action with user data
     * redirect to final url
     * @throws Exception
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function callbackAction()
    {
        $response = $this->opauth->getResponse();
        if (array_key_exists('error', $response)) {
            $error = $response['error'];
            throw new Exception('Authentication error: Opauth returns error auth response.' . 'code: ' . $error['code'] . ' and message ' . $error['message']);
        } else {
            if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid'])) {
                throw new Exception('Invalid auth response: Missing key auth response components.');
            } elseif (!$this->opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason)) {
                throw new Exception('Invalid auth response');
            } else {
                $this->authService->responseFromController($response);
                $this->authService->getUserInformation();
                $this->forward('final');
            }
        }
    }

    /**
     * Save social data to UC (User Config)
     * @param string $strategy
     * @return void
     */
    public function saveToUCAction($strategy)
    {
        $provider = $_SESSION[$this->extKey]['response'][$strategy];
        if ($provider) {
            $strategy = strtolower($strategy);
            if (!is_array($GLOBALS['BE_USER']->uc[$this->extKey]['providers'][$strategy])) {
                $GLOBALS['BE_USER']->uc[$this->extKey]['providers'][$strategy] = $provider;
                $GLOBALS['BE_USER']->overrideUC();
                $GLOBALS['BE_USER']->writeUC();
            }
        } else {
            $arguments['strategy'] = $strategy;
            $this->forward('authenticate', NULL, NULL, $arguments);
        }
        $this->closePopup();
    }

    /**
     * Remove provider social data from UC (User Config)
     * @param string $strategy
     * @return void
     */
    public function removeFromUCAction($strategy)
    {
        if (isset($GLOBALS['BE_USER']->uc[$this->extKey]['providers'][$strategy])) {
            unset($GLOBALS['BE_USER']->uc[$this->extKey]['providers'][$strategy]);
            $GLOBALS['BE_USER']->overrideUC();
            $GLOBALS['BE_USER']->writeUC();
        }
        $this->closePopup();
    }

    /**
     * @return void
     */
    public function closePopup()
    {
        echo '<html><head><title>Authentication success</title></head><body onload="opener.console.log(\'hi, im the popup and im finished\');window.close();"></body></html>';
        die();
    }

    /**
     * Final Action to redirect user to finalUrl
     * @throws \Exception
     */
    public function finalAction()
    {
        $scope = $this->authService->getScope();
        if ($scope === 'fe') {
            $redirectURL = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
        } elseif ($scope === 'be') {
            $redirectURL = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . TYPO3_mainDir . 'backend.php';
        } else {
            throw new \Exception('Invalid scope', 1456744104);
        }
        HttpUtility::redirect($redirectURL);
    }

}
