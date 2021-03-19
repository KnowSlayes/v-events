<?php
	/**
	 * Core Framework System - Functions
	 *
	 * @copyright	Copyright (c) 2021, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.0.1
	 * @package    	Core
	 * @subpackage 	Installation Part
	 * @category	Backend
	 *
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */
	if (!isset($_SESSION['installtoken'])||$_SESSION['installtoken']!='coreinstall') {die;}

	function Error_MsgBox($f_errtext) {
		echo '
		<div class="alert alert-danger" role="alert">
			'.$f_errtext.'
  		</div>
		';
	}

	function Create_TblCoreUser($f_dbhost, $f_dbport='', $f_dbname, $f_dbuser, $f_dbpass, $f_adminusername, $f_adminpassword, $f_salt='salt'){
		if ($f_dbport!='') { 
			$temp_dbport = ';port='.$f_dbport;
		} else {
			$temp_dbport = '';
		}
		try {
			$db_install = new PDO('mysql:host='.$f_dbhost.$temp_dbport.';dbname='.$f_dbname, $f_dbuser, $f_dbpass);
			$stmt_install = $db_install->prepare("CREATE TABLE `testpage`.`core_user` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `username` VARCHAR(128) NOT NULL , `userpassword` VARCHAR(128) NOT NULL , `usergroup`  TINYINT(3) NULL , `lastlogin` DATETIME NULL , `faildlogin` TINYINT(2) NULL , `blocked` BOOLEAN NULL , `blockedtime` TIMESTAMP NULL , `loginstate` BOOLEAN NULL , `loginttl` TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
			$stmt_install->execute();
			$stmt_install = $db_install->prepare("INSERT INTO `core_user` (`id`, `username`, `userpassword`, `usergroup`, `lastlogin`, `faildlogin`, `blocked`, `blockedtime`, `loginstate`, `loginttl`) VALUES (NULL, :adminuser, :adminpass, 99, NULL, NULL, NULL, NULL, NULL, NULL)");
			$stmt_install->bindParam(':adminuser', $f_adminusername);
			$options = ['cost' => 8,];
			$temp_adminpassword = password_hash(hash('sha512', $f_adminpassword . $f_salt), PASSWORD_BCRYPT, $options);
			$stmt_install->bindParam(':adminpass', $temp_adminpassword);
			$stmt_install->execute();
			return true;
		}
		catch (PDOException $e) { return false ; }
	}


	$err_handle="";
	if (isset($_POST['INPUT_HOST']) && isset($_POST['INPUT_PORT']) 
									&& isset($_POST['INPUT_NAME'])
									&& isset($_POST['INPUT_USER'])
									&& isset($_POST['INPUT_PASS'])
									&& isset($_POST['INPUT_SALT'])
									&& isset($_POST['INPUT_ADMINUSERNAME'])
									&& isset($_POST['INPUT_ADMINPASSWORD'])) {
		if (strlen(trim($_POST['INPUT_ADMINUSERNAME']))<8 || strlen(trim($_POST['INPUT_ADMINPASSWORD']))<8) {
			$err_handle="Admin or Password too short";
		} else {
			if (Create_TblCoreUser($_POST['INPUT_HOST'], $_POST['INPUT_PORT'],$_POST['INPUT_NAME'],$_POST['INPUT_USER'],$_POST['INPUT_PASS'],$_POST['INPUT_ADMINUSERNAME'],$_POST['INPUT_ADMINPASSWORD'],$_POST['INPUT_SALT'])) {
				$temp_configfile = 'core_config.php';
				// Delete old Config File
				if (file_exists($temp_configfile)) {
					unlink ($temp_configfile);
				}
				// Create new Config File
				$temp_configsetting  = "<?php \n";
				$temp_configsetting .= "	#Database Config\n";
				$temp_configsetting .= "	define ('DB_HOST', '".$_POST['INPUT_HOST']."');\n";
				$temp_configsetting .= "	define ('DB_PORT', '".$_POST['INPUT_PORT']."');\n";
				$temp_configsetting .= "	define ('DB_NAME', '".$_POST['INPUT_NAME']."');\n";
				$temp_configsetting .= "	define ('DB_USER', '".$_POST['INPUT_USER']."');\n";
				$temp_configsetting .= "	define ('DB_PASS', '".$_POST['INPUT_PASS']."');\n";
				$temp_configsetting .= "	\n";
				$temp_configsetting .= "	#Core Config\n";
				$temp_configsetting .= "	define ('CORE_PASSWORDSALT', '".$_POST['INPUT_SALT']."');\n";
				$temp_configsetting .= "	define ('CORE_INSTALLED', 'true');\n";
				$temp_configsetting .= "?>\n";
				$file_handle = fopen($temp_configfile, "w");
				fwrite ($file_handle, $temp_configsetting);
				fclose ($file_handle);
				$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
				header ("Location: $uri/index.php");
			} else {
				$err_handle="Database connection could not be established or table could not be created.";
			}
		}
		
	}
?>
<html>
	<head>
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<title>Core Config File Installation</title>
	</head>
	<body>
		<div class="container">
			<div class="card mx-auto mt-3" style="max-width: 40rem;">
				<div class="card-header">
					Core System
				</div>
				<div class="card-body">
					<h5 class="card-title">Installation</h5>
<?php
	if ($err_handle!='') {
		Error_MsgBox($err_handle);
	}
?>					
					<h6>Database Informationen</h6>
					<form method="post" action="">
						<p class="card-text">
							<div class="mb-3 row">
								<label for="LabelDBHOST" class="col-md-2 col-form-label">DB Host</label>
								<div class="col-md-10">
									<input type="text" class="form-control" id="LabelDBHOST" name="INPUT_HOST" placeholder="Server host" value="
<?php
if (defined('DB_HOST')) { echo DB_HOST; }
?>
">
								</div>
							</div>
							<div class="mb-3 row">
								<label for="LabelDBPORT" class="col-md-2 col-form-label">DB Port</label>
								<div class="col-md-10">
									<input type="text" class="form-control" id="LabelDBPORT" name="INPUT_PORT" placeholder="Server Port" value="
<?php
if (defined('DB_PORT')) { echo DB_PORT; }
?>
">
								</div>
							</div>
							<div class="mb-3 row">
								<label for="LabelDBNAME" class="col-md-2 col-form-label">DB Name</label>
								<div class="col-md-10">
									<input type="text" class="form-control" id="LabelDBNAME" name="INPUT_NAME" placeholder="Database name" value="
<?php
if (defined('DB_NAME')) { echo DB_NAME; }
?>
">
								</div>
							</div>
							<div class="mb-3 row">
								<label for="LabelDBUSER" class="col-md-2 col-form-label">DB User</label>
								<div class="col-md-10">
									<input type="text" class="form-control" id="LabelDBUSER" name="INPUT_USER" placeholder="Database username" value="
<?php
if (defined('DB_USER')) { echo DB_USER; }
?>
">
								</div>
							</div>
							<div class="mb-3 row">
								<label for="LabelDBPASS" class="col-md-2 col-form-label">DB Pass</label>
								<div class="col-md-10">
									<input type="text" class="form-control" id="LabelDBPASS" name="INPUT_PASS" placeholder="Database password" value="
<?php
if (defined('DB_PASS')) { echo DB_PASS; }
?>
">
								</div>
							</div>
							<hr />
							<h6>Addon Securtiy</h6>
							<div class="mb-3 row">
								<label for="LabelSALT" class="col-md-2 col-form-label">Salt</label>
								<div class="col-md-10">
									<input type="text" class="form-control" id="LabelSALT" name="INPUT_SALT" placeholder="Password extra salt" value="
<?php
if (defined('CORE_PASSWORDSALT')) { echo CORE_PASSWORDSALT; }
?>
">
								</div>
							</div>
							<hr />
							<h6>Admin Informationen</h6>
							<div class="mb-3 row">
								<label for="LabelAdminUsername" class="col-md-2 col-form-label">Username</label>
								<div class="col-md-10">
									<input type="text" class="form-control" id="LabelAdminUsername" name="INPUT_ADMINUSERNAME" placeholder="Username for the Admin" require>
								</div>
							</div>
							<div class="mb-3 row">
								<label for="LabelAdminPassword" class="col-md-2 col-form-label">Password</label>
								<div class="col-md-10">
									<input type="password" class="form-control" id="LabelAdminPassword" name="INPUT_ADMINPASSWORD" placeholder="Password for the Admin" require>
								</div>
							</div>


							Fill in all fields to connect to the database server
						</p>
						<button type="submit" class="btn btn-primary">Try to Install</button>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>