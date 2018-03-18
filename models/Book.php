<?php
	require_once("../db/Database.php");
	require_once("../interfaces/IBook.php");

	class Book implements IBook {
		private $con;
		private $bookID;
		private $title;
		private $editorialID; 
		private $edition;
		private $translator;
		private $language;
		private $daily_fine_amount;
		private $stock;
		private $pages;
		private $publishing_date;
		private $loanID;

		public function __construct (Database $db)
		{
			$this -> con = new $db;
		}
		public function setBookID($bookID)
		{
			$this -> bookID = $bookID;
		}
		public function setLoanID($bookID)
		{
			$this -> loanID = $loanID;
		}
		public function setTitle($title)
		{
			$this -> title = $title;
		}
		public function setEditorialID($editorialID)
		{
			$this -> editorialID = $editorialID;
		}
		public function setEdition($edition)
		{
			$this -> edition = $edition;			
		}
		public function setTranslator($translator)
		{
			$this -> translator = $translator;
		}
		public function setLanguage ($language)
		{
			$this -> language = $language;
		}
		public function setDaily_fine_amount($daily_fine_amount)
		{
			$this -> daily_fine_amount = $daily_fine_amount;
		}
		public function setStock ($stock)
		{
			$this -> stock = $stock;
		}
		public function setPages ($pages)
		{
			$this -> pages = $pages;
		}
		public function setPublishing_date($publishing_date)
		{
			$this -> publishing_date = $publishing_date;
		}

		public function save()
		{
			try {
				$query = $this -> con -> prepare ('SELECT LoanBook(?, ?)');
				$query = bindParam(1, $this -> loanID, PDO::PARAM_INT);
				$query = bindParam(2, $this -> bookID, PDO::PARAM_INT);
				$query = execute();

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
					$query = $this -> con -> prepare('SELECT b.bookID, b.title FROM books b INNER JOIN books_loans bl ON b.bookID = bl.bookID WHERE bl.loanID = ?');
					$query -> bindParam (1, $this->loanID, PDO::PARAM_INT);
					$query -> execute();
					$this -> con -> close();
					return $query -> fetchAll(PDO::FETCH_OBJ);
				}
				else{
					$query = $this -> con -> prepare('SELECT bookID, title FROM books');
					$query -> execute();
					$this -> con -> close();
					return $query -> fetchAll(PDO::FETCH_OBJ);
				}
			}
			catch (PDOException $e)
			{
				echo $e -> getMessage();
			}
		}

		public function delete(){
            try{
				$query = $this->con->prepare('DELETE FROM books_loans WHERE bookID=? AND loanID=?');
				$query->bindParam(1, $this->bookID, PDO::PARAM_INT);
                $query->bindParam(2, $this->loanID, PDO::PARAM_INT);
                $query->execute();
                $this->con->close();
                return true;
            }
            catch(PDOException $e){
                echo  $e->getMessage();
            }
        }

		public static function baseurl() {
			return stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . "/LibraryADB/";
	   }

	   public function checkBook($book) 
	   {
			if( ! $book ) {
				header("Location:" . User::baseurl() . "app/list.php");
			}
		}
	}
?>