<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
    require_once "../models/Loan.php";
    require_once "../models/Book.php";
	if (empty($_POST['submit'])){
	      header("Location:" . Loan::baseurl() . "app/splash.php");
	      exit;
	}

	$args = array(
        'book' => FILTER_SANITIZE_NUMBER_INT
	);

	$post = (object)filter_input_array(INPUT_POST, $args);

	$db = new Database;
	$loan = new Loan($db);
	$l = $loan->save()->loanid;
	$db = new Database;
	$book = new Book($db);
	$book->setBookID($post->book);
	$book->setLoanID($l);
	$book->save();
	header("Location:" . Loan::baseurl() . "app/listLoans.php");

?>