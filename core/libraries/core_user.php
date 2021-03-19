<?php
	/**
	 * Core Framework System - Functions
	 *
	 * @copyright	Copyright (c) 2021, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.0.1
	 * @package    	Core
	 * @subpackage 	User
	 * @category	Library
	 *
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */
	 
	class Core_User {
		/** 
		 * Constants of the class
		 *
		 * 	- Version informations
		 */
		const CLASS_VERSION	= '0.0.1-beta';
		const CLASS_BUILD	= '00002';
		const CLASS_DATE	= '2021-02-08';

		/**
		 * Give the password a little Salt
		 */
		private function password_sha512 ($f_password, $f_salt) {
			return hash('sha512', $f_password . $f_salt);
		}
		
		/**
		 * Password encrypt
		 */
		public function password_encrypt ($f_password) {
			if (!defined('CORE_PASSWORDSALT')) {
				define ('CORE_PASSWORDSALT', 'salt');
			}
			//echo CORE_PASSWORDSALT;
			$options = ['cost' => 8,];
			return password_hash($this->password_sha512($f_password, CORE_PASSWORDSALT), PASSWORD_BCRYPT, $options);
		}
		
		/**
		 * Comparison the Password with the saved hash
		 */
		public function password_check ($f_password, $f_hash) {
			if (password_verify($this->password_sha512($f_password, CORE_PASSWORDSALT), $f_hash)) {
				 return true;
			} else {
				 return false;
			}
		}

		/**
		 * Generate a authentication code
		 */
		public function authcodegenerator($f_authcodelenght, $f_authcodestrong) {
			mt_srand((double) microtime() * 1000000); // Randomize starts
			if ($f_authcodestrong >= 1) {$authcodeset = "abcdefghijklmnopqrstuvxyz";}
			if ($f_authcodestrong >= 2) {$authcodeset .= "ABCDEFGHIKLMNPQRSTUVWXYZ";}
			if ($f_authcodestrong >= 3) {$authcodeset .= "1234567890";}
			if ($f_authcodestrong >= 4) {$authcodeset .= "!ยง$%&/()=";}
			for ($n=1;$n<=$f_authcodelenght;$n++) {
				$authcode .= $authcodeset[mt_rand(0,(strlen($authcodeset)-1))];	
			}
			return ($authcode);
		}	
	}
?>