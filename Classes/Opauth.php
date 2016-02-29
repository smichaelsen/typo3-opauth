<?php
namespace Smichaelsen\Opauth;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Wrapper for the Opauth class to make autoloading possible and make it singleton
 */
class Opauth extends \Opauth implements SingletonInterface
{

    /**
     * @var string
     */
    protected $extKey = 'opauth';

    /**
     * @param string $strategy
     */
    public function setStrategy($strategy)
    {
        $this->env['params']['strategy'] = $strategy;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->env['params']['action'] = $action;
    }

    public function getResponse()
    {
        $response = NULL;
        switch ($this->env['callback_transport']) {
            case 'session':
                session_start();
                $response = $_SESSION[$this->extKey];
                unset($_SESSION[$this->extKey]);
                break;
            case 'post':
                $response = unserialize(base64_decode($_POST[$this->extKey]));
                break;
            case 'get':
                $response = unserialize(base64_decode($_GET[$this->extKey]));
                break;
            default:
                throw new Exception('Unsupported callback_transport: ' . htmlspecialchars($this->env['callback_transport']));
                break;
        }
        return $response;
    }

}
