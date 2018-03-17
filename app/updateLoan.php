<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	require_once "../models/Loan.php";
	if (empty($_POST['submit'])){
	      header("Location:" . Loan::baseurl() . "app/listLoans.php");
	      exit;
	}
	$args = array(
	    'loanID'        => FILTER_VALIDATE_INT,
	    'loan_date'  => FILTER_SANITIZE_STRING,
	    'return_date'  => FILTER_SANITIZE_STRING,
	);

	$post = (object)filter_input_array(INPUT_POST, $args);

	if( $post->loanID === false ){
	    header("Location:" . Loan::baseurl() . "app/listLoans.php");
	}

	$db = new Database;
	$loan = new Loan($db);
	$loan->setLoanID($post->loanID);
	$loan->setLoan_date($post->loan_date);
	$loan->setReturn_date($post->return_date);
	$loan->update();
	header("Location:" . Loan::baseurl() . "app/listLoans.php");
?>