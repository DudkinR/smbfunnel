<?php
if(isset($_POST['delete_cname']))
{
		// delete subdomain
		$mysqli = $info['mysqli'];
		$dbpref = $info['dbpref'];
		$table = $dbpref . "subdomians";
		$id=$_POST['delcname'];
		$sql_delete="
		DELETE FROM `".$table."` WHERE `id` = '$id'";
		$mysqli->query($sql_delete);
		echo "<script>alert('Subdomain/CNAME Deleted Successfully');</script>";
}
$Location = "Location: http://".$_SERVER['HTTP_HOST']."?page=subdomains";
header($Location);

?>