<?php 

$db_host = "127.0.0.1";
$db_name ="db_ticketsystem";
$db_user = "root";
$db_pass = "";


//Accepts database credentials and attempts to connect to the database
function dataBase($db_host,$db_name,$db_user,$db_pass){
	try{
		
		$DBH = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
		$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $DBH;
	
	} catch (PDOException $e) {
		
		error_log($e);
		return "ERROR - failed to connect.";

	}
	
}

?>