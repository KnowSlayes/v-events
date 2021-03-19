<?php
	if (isset($_FILES['file1'])) {
		$fileinformation = pathinfo($_FILES['file1']['name']);
		$uploadfolder="";
		if ($fileinformation['extension']=="pdf"){ $uploadfolder ="pdf"; }
		if ($fileinformation['extension']=="mp4"){ $uploadfolder ="video"; }
		if ($fileinformation['extension']=="mov"){ $uploadfolder ="video"; }
		if ($fileinformation['extension']=="mpg"){ $uploadfolder ="video"; }
		if ($fileinformation['extension']=="avi"){ $uploadfolder ="video"; }
		if ($uploadfolder!="") {
			if (is_uploaded_file($_FILES['file1']['tmp_name'])) {
				move_uploaded_file($_FILES['file1']['tmp_name'], '../../mediathek/'.$uploadfolder.'/'.$_FILES['file1']['name']);

				require ("../core_class.php");
				$core = new Core;
				$db= New Core_Database;
				$temp_thumnail = './img/mediathek_unknow_'.$uploadfolder.'.jpg';
				$statement = $db->prepare("INSERT INTO `txb_medialibrary` (filename, typetext, theme, filename_thumbnail, mediafilter, orderid) VALUES (?,?,?,?,?,?)");
				$statement->execute(array($_FILES['file1']['name'], $_POST['media_type'], $_POST['media_theme'],$temp_thumnail, $uploadfolder,0));   
				}
		}
	}
?>
