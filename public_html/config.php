<?php 
		    $dbpref='smbf_';
			$mysqli= new mysqli('localhost','smbf_smbfunnels','Viktor@2018one','smbf_smbfunnels',3306);
			if(mysqli_connect_errno()>0)
			{
				echo 'Unable to connect db';
				die();
			}
			require_once('library/options.php');
		 ?>