<?php
    require_once("../db/Database.php");
	require_once("../interfaces/ILoan.php");
	
	class Loan implements ILoan
	{
		private $con;
		private $loanID;
		private $clientID;
		private $loan_date;
		private $return_date;
		private $librarianID;
		
		public function __construct(Database $db){
    		$this->con = new $db;
		}
		
		public function setLoanID($loanID)
		{
			$this -> loanID = $loanID;
		}

		public function setClientID($clientID)
		{
			$this -> clientID = $clientID;
		}

		public function setLibrarianID($librarianID)
		{
			$this -> librarianID = $librarianID;
		}

		public function save()
		{
			try {
				$query = $this -> con -> prepare ('INSERT INTO loans (clientID, loan_date, return_date, librarianID) VALUES (?, CURRENT_DATE, CalculateReturnDate(CURRENT_DATE), ?)');
				$query = bindParam(1, $this -> clientID, PDO::PARAM_INT);
				$query = bindParam(2, $this -> librarianID, PDO::PARAM_INT);
				$query = execute();

				$this -> con -> close();
			}
			catch (PDOException $e)
			{
				echo $e -> getMessage();
			}
		}

		public function update()
		{
			try {
				$query = $this -> con -> prepare('UPDATE loans SET clientID = ?, librarianID = ? WHERE loanID = ?');
				$query -> bindParam(1, $this -> clientID, PDO::PARAM_INT);
				$query -> bindParam(2, $this -> librarianID, PDO::PARAM_INT);
				$query -> bindParam(3, $this -> loanID, PDO::PARAM_INT);
				$query -> execute();

				$this -> con -> close();
			}
			catch (PDOException $e)
			{
				echo $e -> getMessage();
			}
		}

		public function get()
		{
			try {
				if (is_int($this -> loanID))
				{
					$query = $this -> con -> prepare ('SELECT * FROM loans WHERE loanID = ?');
					$query -> bindParam(1, $this -> loanID, PDO::PARAM_INT);
					$query -> execute();

					$this -> con -> close();
					return $query -> fetch (PDO::FETCH_OBJ);
				}
				else
				{
					$query = $this -> con -> prepare ('SELECT * FROM loans');
					$query -> execute();
					$this -> con -> close();
					return $query -> fetchAll (PDO::FETCH_OBJ);
				}
			}
			catch(PDOException $e){
    	        echo  $e->getMessage();
    	    }
		}

		public static function baseurl() {
			return stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . "/LibraryADB/";
	   	}

	   public function checkBook($user) {
		   if( ! $user ) {
			   header("Location:" . User::baseurl() . "app/list.php");
		   }
	   }
	}
?>