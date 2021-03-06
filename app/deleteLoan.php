<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	require_once "../models/Loan.php";
	$db = new Database;
	$loan = new Loan($db);
	$loanID = filter_input(INPUT_GET, 'loan', FILTER_VALIDATE_INT);
	
	if( $loanID ){
		$loan->setLoanID($loanID);
		$loan->delete();
	}
	header("Location:" . Loan::baseurl() . "app/splash.html");
?>