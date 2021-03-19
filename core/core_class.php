<?php
	/**
	 * Project:     Core Framework System
	 *
	 * @copyright	Copyright (c) 2021, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.0.1
	 * @package    	Core
	 *
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */ 
 

	 /**
	 * Set the Core Direction if not define
	 */
	if (!defined('CORE_DIR')) {
			define('CORE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	}


	/**
	 * Set the Core Library Direction if not define
	 */
	if (!defined('CORE_LIBRARY_DIR')) {
			define('CORE_LIBRARY_DIR', CORE_DIR . 'libraries' . DIRECTORY_SEPARATOR);
	}	


	/**
	 * Set the Direction for the Plugins if not define
	 */
	if (!defined('CORE_PLUGIN_DIR')) {
			define('CORE_PLUGIN_DIR', CORE_DIR . 'plugins' . DIRECTORY_SEPARATOR);
	}	
	

	/**
	 * Load the Config File for the Core and Check all defined
	 */
	function Check_Configfile($f_configfile){
		if (file_exists($f_configfile)) {
			require_once $f_configfile;
			if (!defined('DB_HOST') || !defined('DB_PORT')
									|| !defined('DB_NAME') 
									|| !defined('DB_USER') 
									|| !defined('DB_PASS') 
									|| !defined('CORE_PASSWORDSALT')
									|| !defined('CORE_INSTALLED')) {
				return false;
			} else {
				return true;
			}
		} else { 
			return false;
		}
	}

	/** 
	 * Load all Libraries
	 */
	if (is_dir(CORE_LIBRARY_DIR)) {
		if ($handle = opendir(CORE_LIBRARY_DIR)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != "." && $file != "..") {
					require_once CORE_LIBRARY_DIR . $file;
				}
			}
		}
	}

	/** 
	 * Load all Plugins
	 */
	if (is_dir(CORE_PLUGIN_DIR)) {
		if ($handle = opendir(CORE_PLUGIN_DIR)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != "." && $file != "..") {
					require_once CORE_PLUGIN_DIR . $file;
				}
			}
		}
	}

	 // require_once CORE_PLUGIN_DIR . 'plugin_template.php';
	// require_once CORE_PLUGIN_DIR . 'plugin_error.php';


	/**
	 * Check whether the core system is installed
	 */
	if (!Check_Configfile(CORE_DIR . 'core_config.php')) {
		DisplayError ("Error into the config file");
	}


	class Core {
		/** 
		 * Constants of the class
		 *
		 * 	- Version informations
		 */
		const CORE_VERSION	= '0.0.1-beta';
		const CORE_BUILD	= '00001';
		const CORE_DATE		= '2021-02-06';
		
		public $ErrorHandle = false;
		public $debugging;

		/**
		 * Database Connection on Load
		 * Load Templatesystem on Load
		 * Set Default systemvariables
		 */
		function __construct() {
			$db = new Core_Database;
			// $_SESSION['username']='';
			// $_SESSION['uid']=0;
			// $_SESSION['gid']=0;
			// $this->tpl = new Template(CORE_DIR .'site');
			// $this->error = new Error_Handle();
			// $this->refreshSystemVars();
		}
		
		/**
		 * Refresh of system variables
		 */
		// private function refreshSystemVars() {
		// 	$this->tpl->assign('COREVERSION', strstr($this::CORE_VERSION, '-', true),'s');
		// 	$this->tpl->assign('SYSTEMTIME', $this->CurrentDatetime(),'s');
		// 	$this->tpl->assign('USERNAME', $_SESSION['username'],'s');
		// 	$this->tpl->assign('UID', $_SESSION['uid'],'s');
		// 	$this->tpl->assign('GID', $_SESSION['gid'],'s');
		// }
		

		
		/**
		 * Display the Template Output Cache
		 */
		// public function display() {
		// 	if (isset($_GET['page'])){
		// 		if (file_exists("./pages/".$_GET['page'].".php")){
		// 			include_once ('./pages/'.$_GET['page'].'.php');
		// 		} else {
		// 			// Fehler Seite
		// 		}
		// 	} else {
		// 		include_once ('./pages/main.php');
		// 	}
		// 	$this->tpl->display();
		// }
		
		
		
		
		
		
		







		### New Function ### (Please sort in the right place)
		#### TEST AREA - IMPORTEN DELETE ####
	 	public function db_test() {
			echo $this->db->rowcount('test');
		}
		public function db_test2() {
			$sth = $this->db->prepare("SELECT value FROM test");
			$sth->execute();
			echo "<p>COUNT: ".$sth->rowCount()."</p>";
		}

	}

	class User extends Core_Database {
		public function isLogin() {
			echo $this->rowcount('test')."<br>";
		}
	}

?>