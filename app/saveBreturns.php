<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once "../models/Breturn.php";
	
	$db = new Database;
	$loan = filter_input(INPUT_GET, 'loan', FILTER_VALIDATE_INT);
	$breturn = new Breturn($db);

	if ( $loan ){
		$breturn->setLoanID($loan);
		$breturn->save();
	}
	
	header("Location:" . Breturn::baseurl() . "app/listBreturns.php");

?>