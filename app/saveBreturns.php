<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
    require_once "../models/Breturn.php";
	if (empty($_POST['submit'])){
	      header("Location:" . Breturn::baseurl() . "app/listLoansToBreturns.php");
	      exit;
	}

	$args = array(
        'loan' => FILTER_SANITIZE_NUMBER_INT
	);

	$post = (object)filter_input_array(INPUT_POST, $args);

	$db = new Database;
	$breturn = new Breturn($db);
	$breturn->setLoanID($post->loan);
	$breturn->save();
	header("Location:" . Breturn::baseurl() . "app/listBreturns.php");

?>