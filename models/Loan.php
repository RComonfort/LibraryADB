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
		private $bookCount;
		
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

		public function setLoan_date($loan_date)
		{
			$this -> loan_date = $loan_date;
		}

		public function setReturn_date($return_date)
		{
			$this -> return_date = $return_date;
		}

		public function save()
		{
			try {
				$query = $this -> con -> prepare ('INSERT INTO loans (clientID, loan_date, return_date, librarianID) VALUES (1, CURRENT_DATE, CalculateReturnDate(CURRENT_DATE), 1) RETURNING loanID as loanID');
				$query -> execute();

				$this -> con -> close();

				return $query ->fetch(PDO::FETCH_OBJ);
			}
			catch (PDOException $e)
			{
				echo $e -> getMessage();
			}
		}

		public function update()
		{
			try {
				$query = $this -> con -> prepare('UPDATE loans SET loan_date = ?, return_date = ? WHERE loanID = ?');
				$query -> bindParam(1, $this -> loan_date, PDO::PARAM_INT);
				$query -> bindParam(2, $this -> return_date, PDO::PARAM_INT);
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
					$query = $this -> con -> prepare ('SELECT l.loanID, l.clientID,l.loan_date,l.return_date,l.librarianID, COUNT(bl.bookID) AS bookCount FROM loans l LEFT JOIN books_loans bl ON l.loanID=bl.loanID GROUP BY l.loanID ORDER BY l.loanID');
					$query -> execute();
					$this -> con -> close();
					return $query -> fetchAll (PDO::FETCH_OBJ);
					
				}
			}
			catch(PDOException $e){
    	        echo  $e->getMessage();
    	    }
		}

		public function delete(){
            try{
                $query = $this->con->prepare('SELECT DeleteLoan(?)');
                $query->bindParam(1, $this->loanID, PDO::PARAM_INT);
                $query->execute();
                $this->con->close();
                return true;
            }
            catch(PDOException $e){
                echo  $e->getMessage();
            }
        }

		public static function baseurl() {
			return stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . "/usuario4/LibraryADB/";
	   	}

	   public function checkLoan($loan) {
		   if( ! $loan ) {
			   header("Location:" . Loan::baseurl() . "app/listLoans.php");
		   }
	   }
	}
?>