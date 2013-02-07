<?php
namespace T3SEO\Opauth;

use \OpauthStrategy as OpauthStrategy;
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('opauth') . 'ThirdParty/Opauth/lib/Opauth/Opauth.php');

/**
 * Wrapper for the Opauth class to make autoloading possible and make it singleton
 */
class Opauth extends \Opauth implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Override __construct method to have a non functional contructor (for dependency injection)
	 */
	public function __construct() {
	}

	/**
	 * @param array $config
	 */
	public function setConfig(array $config) {
		/**
		 * Configurable settings
		 */
		$this->config = array_merge(array(
			'host' => ((array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'],
			'path' => '/',
			'callback_url' => '{path}callback',
			'callback_transport' => 'session',
			'debug' => FALSE,

			/**
			 * Security settings
			 */
			'security_salt' => 'LDFmiilYf8Fyw5W10rx4W1KsVrieQCnpBzzpTBWA5vJidQKDx8pMJbmw28R1C4m',
			'security_iteration' => 300,
			'security_timeout' => '2 minutes'

		), $config);

		/**
		 * Environment variables, including config
		 * Used mainly as accessors
		 */
		$this->env = array_merge(array(
			'request_uri' => $_SERVER['REQUEST_URI'],
			'complete_path' => $this->config['host'].$this->config['path'],
			'lib_dir' => dirname(__FILE__).'/',
			'strategy_dir' => dirname(__FILE__).'/Strategy/'
		), $this->config);

		if (!class_exists('OpauthStrategy')){
			require $this->env['lib_dir'].'OpauthStrategy.php';
		}

		foreach ($this->env as $key => $value){
			$this->env[$key] = OpauthStrategy::envReplace($value, $this->env);
		}

		if ($this->env['security_salt'] == 'LDFmiilYf8Fyw5W10rx4W1KsVrieQCnpBzzpTBWA5vJidQKDx8pMJbmw28R1C4m'){
			trigger_error('Please change the value of \'security_salt\' to a salt value specific to your application', E_USER_NOTICE);
		}
	}

	/**
	 * @param string $strategy
	 */
	public function setStrategy($strategy) {
		$this->env['params']['strategy'] = $strategy;
	}

	public function run() {
		$this->loadStrategies();
		parent::run();
	}

}

?>