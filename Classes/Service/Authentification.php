<?php
namespace Butenko\Opauth\Service;

class Authentification extends \TYPO3\CMS\Sv\AbstractAuthenticationService {

	/**
	 * Mode to use
	 *
	 * @var string
	 */
	public $mode = 'getUserFE';

	/**
	 * @var string
	 */
	protected $sessionKey = 'opauth';

	/**
	 * @var array
	 */
	protected $config = array();

	/**
	 * @var string
	 */
	protected $scope = 'fe';


	public function __construct() {
		session_start();
		$this->config = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['opauth']['setup'];
	}

	/**
	 * @param mixed $user
	 */
	public function authUser(&$user) {
		if ($user['email']) {
			if (!$user['email']) {
				return 101;
			}
			return 200;
		} else {
			return 100;
		}
	}

	/**
	 * @return array User Array or FALSE
	 */
	public function getUser() {
		return;
	}

}

?>
