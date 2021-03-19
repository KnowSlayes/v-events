<?php
	/**
	 * Core Framework System - Functions
	 *
	 * @copyright	Copyright (c) 2021, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.0.1
	 * @package    	Core
	 * @subpackage 	Index Page
	 * @category	Backend
	 *
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */
	session_start();

	function Check_Install($f_configfile){
		if (file_exists($f_configfile)) {
			require_once ($f_configfile);
			if (defined('CORE_INSTALLED')) {
				return true;
			} else {
				return false;
			}
		} else { 
			return false;
		}
	}

	if (Check_Install('core_config.php')) {
		// Core Install Go to the Backend Login
		require_once ('backend.php');
	} else {
		// Core not installed
		$_SESSION['installtoken']='coreinstall';
		require_once ('install.php');
	}
?>