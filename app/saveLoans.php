<?php
    require_once "../models/Loan.php";
    require_once "../models/Book.php";
	if (empty($_POST['submit'])){
	      header("Location:" . Loan::baseurl() . "app/listLoans.php");
	      exit;
	}

	$args = array(
	    'loan_date'  => FILTER_SANITIZE_STRING,
        'book' => FILTER_SANITIZE_NUMBER_INT
	);

	$post = (object)filter_input_array(INPUT_POST, $args);

	$db = new Database;
	$loan = new Loan($db);
	$loan->setLoanDate($post->loan_date);
    $loan->save();
    $db = new Database;
	$book = new Book($db);
	$book->setLoanBookID($post->book);
	$book->setLoanLoanID($loan->save());
	$book->save();
	header("Location:" . Loan::baseurl() . "app/listLoans.php");

?>