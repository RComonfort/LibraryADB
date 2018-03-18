<?php
    require_once "../models/Loan.php";
    require_once "../models/Book.php";
	if (empty($_POST['submit'])){
	      header("Location:" . Book::baseurl() . "app/listLoans.php");
	      exit;
	}

	$args = array(
	    'book'  => FILTER_SANITIZE_NUMBER_INT,
        'loanID' => FILTER_SANITIZE_NUMBER_INT
	);

	$post = (object)filter_input_array(INPUT_POST, $args);

	$db = new Database;
	$book = new Book($db);
	$book->setBookID($post->book);
	$book->setLoanID($post->loanID);
	$book->save();
	header("Location:" . Book::baseurl() . "app/listLoans.php");

?>