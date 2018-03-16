<?php
	require_once("../db/Database.php");
	require_once("../interfaces/IBook.");

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

		public function __construct (Database $db)
		{
			$this -> con = new $db;
		}
		public function setBookID($bookID)
		{
			$this -> bookID = $bookID;
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

		public function get()
		{
			try {
				if (is_int($this -> bookID))
				{
					$query = $this -> con -> prepare('SELECT * FROM books WHERE bookID = ?');
					$query -> bindParam (1, $this -> bookID, PDO::PARAM_INT);
					$query -> execute();
					$this -> con -> close();
					return $query -> fetch(PDO::FETCH_OBJ);
				}
				else
				{
					$query + $this -> con -> prepare('SELECT * from books');
					$query -> execute ();
					$this -> con -> close();

					return $query -> fetchAll(PDO::FETCH_OBJ);
				}
			}
			catch (PDOException $e)
			{
				echo $e -> getMessage();
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