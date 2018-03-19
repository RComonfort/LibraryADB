<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

	require_once "../models/Book.php";
	$db = new Database;
	$book = new Book($db);
	$bookID = filter_input(INPUT_GET, 'bookid', FILTER_VALIDATE_INT);
	$loan = filter_input(INPUT_GET, 'loan', FILTER_VALIDATE_INT);
	
	if( $bookID && $loan){
        $book->setLoanID($loan);
        $book->setBookID($bookID);
		$book->delete();
	}
	header("Location:" . Book::baseurl() . "app/splash.html");
?>