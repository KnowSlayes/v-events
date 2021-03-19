<?php
	/**
	 * Core Framework System - Functions
	 *
	 * @copyright	Copyright (c) 2021, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.0.1
	 * @package    	Core
	 * @subpackage 	Error
	 * @category	Library
	 *
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 */

	function DisplayError ($f_errdescripton, $f_technicalinformation = 'none', $f_errnumber=0) {
		
		if ($f_errnumber>=400) {
			http_response_code($f_errnumber);
		}
?>
<html lang="en">
	<head>
		<title>Error Page</title>
		<link href="core/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
		<div class="page-wrap d-flex flex-row align-items-center">
			<div class="container" style="max-width: 720px;">
				<div class="row justify-content-center">
					<div class="col text-center">
						<h1>Ooopps!</h1>
						<h2><?php if ($f_errnumber>=400) { echo $f_errnumber;} ?></h2><hr />
						<div class="mb-4 lead">
							Sorry, an error has occured!
						</div>
						<div class="mb-4">
							<?php echo $f_errdescripton; ?>
						</div>
<?php
	if ($f_technicalinformation!='none') {
?>
						<div class="card">
							<div class="card-header">
								Technical description
							  </div>
							<div class="card-body">
								<?php echo $f_technicalinformation; ?>
							</div>
						</div>
<?php
	}
?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
<?php
		exit;
	}
?>
