<?php
	/**
	 * Core Framework System - Functions
	 *
	 * @copyright	Copyright (c) 2021, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.0.1
	 * @package    	Core
	 * @subpackage 	Function
	 * @category	Library
	 *
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */
	 
 	class Core_Function {
		/** 
		 * Constants of the class
		 *
		 * 	- Version informations
		 */
	  	const CLASS_VERSION	= '0.0.1-beta';
		const CLASS_BUILD	= '00003';
		const CLASS_DATE	= '2021-02-08';
		
		/**
		 * Current Time Function
		 */
		public function CurrentTimetamp () {
			return time();
		}

		public function CurrentDatetime () {
			return date("Y-m-d H:i:s", $this->CurrentTimetamp());
		}

		/**
		 *Date format conversion
		 */
		public function dateformat ($f_originalDate, $f_newFormat="Y-m-d") {
			return date($f_newFormat, strtotime($f_originalDate));
		}
		
		/**
		 * Check if the last character is the Sepreator, if not add the Spereator 
		 */
		public function chkSeparator ($f_dir) {
			if (substr($f_dir, -1, 1)!= DIRECTORY_SEPARATOR) {
				$f_dir = $f_dir . DIRECTORY_SEPARATOR;
			}
			return $f_dir;
		}

	}
?>