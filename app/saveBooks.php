<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
    require_once "../models/Loan.php";
    require_once "../models/Book.php";
	if (empty($_POST['submit'])){
	      header("Location:" . Book::baseurl() . "app/listLoans.php");
	      exit;
	}

	$args = array(
	    'book'  => FILTER_SANITIZE_NUMBER_INT,
        'loan' => FILTER_SANITIZE_NUMBER_INT
	);

	$post = (object)filter_input_array(INPUT_POST, $args);
	echo $post->book;
	echo $post->loan;
	die;
	$db = new Database;
	$book = new Book($db);
	$book->setBookID($post->book);
	$book->setLoanID($post->loan);
	$book->save();
	header("Location:" . Book::baseurl() . "app/listLoans.php");

?>