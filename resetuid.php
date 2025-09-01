<?php
include"dbconnection.php";
$real_key = "newmodff";
if (isset($_GET["key"])) {
	if($_GET["key"] == $real_key) {
		$response = mysqli_query($con, "UPDATE tokens SET UID=NULL");
		if ($response) {
			$today = date("Y-m-d H:i:s");
			file_put_contents("logs.txt", "[".$today."] Success Reset All UID" . PHP_EOL, FILE_APPEND | LOCK_EX);
		}
	}
}
?>