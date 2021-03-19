<?php
	/**
	 * Core Framework System - Database
	 *
	 * @copyright	Copyright (c) 2021, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.0.1
	 * @package    	Core
	 * @subpackage 	Database
	 * @category	Library
	 * 
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */
 
	class Core_Database extends PDO {
		/** 
		 * Constants of the class
		 *
		 * 	- Version informations
		 */
		const CLASS_VERSION	= '0.0.1-beta';
		const CLASS_BUILD	= '00001';
		const CLASS_DATE	= '2021-02-06';

		/**
		 * Start of Class with connecting to the PDO
		 */
		public function __construct($c_dbhost=DB_HOST, $c_dbname=DB_NAME, $c_username=DB_USER, $c_password=DB_PASS) {
			$dsn = 'mysql:host='.$c_dbhost.';dbname='.$c_dbname;
			try {
				parent::__construct($dsn, $c_username, $c_password);
			} 
			catch (PDOException $e) {
				DisplayError ("Database Connection Error", $e->getMessage());
			}
			echo "dB -> Open <br/>";
		}
		
		/**
		 * Count all rows form a table
		 */
		public function rowcount($f_table) {
			$statement = $this->prepare("SELECT * FROM $f_table");
			$statement->execute();
			return $statement->rowCount();
		}

		/**
		 * Close the Database Connection
		 */
		public function close() {
			echo "dB-> Close<br>";
			$this->connection = null;
		}
		/**
		 * Close database connection on the end
		 */
		public function __destruct() {
			$this->close();
		}

	}
?>