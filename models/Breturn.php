<?php
    require_once("../db/Database.php");
    require_once("../interfaces/IBreturn.php");

    class Breturn implements IBreturn {
    	private $con;
        private $returnID;
        private $loanID;
        private $actual_return_date;
        private $fineID;

    	public function __construct(Database $db){
    		$this->con = new $db;
    	}

        public function setReturnID($returnID){
            $this->returnID = $returnID;
        }

        public function setLoanID($loanID){
            $this->loanID = $loanID;
        }

        public function setActual_return_date($actual_return_date){
            $this->actual_return_date = $actual_return_date;
        }

        public function setFineID($fineID){
            $this->fineID = $fineID;
        }

    	//insertamos usuarios en una tabla con postgreSql
    	public function save() {
    		try{
    			$query = $this->con->prepare('INSERT INTO breturns (loanID, actual_return_date) values (?,CURRENT_DATE)');
    			$query->bindParam(1, $this->loanID, PDO::PARAM_STR);
    			$query->execute();
    			$this->con->close();
    		}
            catch(PDOException $e) {
    	        echo  $e->getMessage();
    	    }
    	}

        public function update(){
    		try{
    			$query = $this->con->prepare('UPDATE breturns SET loanID = ?, fineID = ? WHERE returnID = ?');
    			$query->bindParam(1, $this->loanID, PDO::PARAM_STR);
    			$query->bindParam(2, $this->fineID, PDO::PARAM_STR);
                $query->bindParam(3, $this->returnID, PDO::PARAM_INT);
    			$query->execute();
    			$this->con->close();
    		}
            catch(PDOException $e){
    	        echo  $e->getMessage();
    	    }
    	}

        public function getLoans(){
            try{
                $query = $this->con->prepare('SELECT * FROM loans l WHERE l.loanID NOT IN (SELECT br.loanID FROM breturns br)');
                $query->execute();
        		$this->con->close();
            
                return $query->fetchAll(PDO::FETCH_OBJ);
    		}
            catch(PDOException $e){
    	        echo  $e->getMessage();
    	    }
        }

    	//obtenemos usuarios de una tabla con postgreSql
    	public function get(){
    		try{
                $query = $this->con->prepare('SELECT br2.returnID, br2.loanID, br2.fineID, l2.loan_date, br2.actual_return_date as return_date FROM breturns br2 INNER JOIN (SELECT * FROM loans l WHERE l.loanID IN (SELECT br.loanID FROM breturns br)) AS l2 ON br2.loanID = l2.loanID');
        		$query->execute();
        		$this->con->close();
                    
        		return $query->fetchAll(PDO::FETCH_OBJ);
    		}
            catch(PDOException $e){
    	        echo  $e->getMessage();
    	    }
    	}

        public function delete(){
            try{
                $query = $this->con->prepare('DELETE FROM breturns WHERE returnID = ?');
                $query->bindParam(1, $this->returnID, PDO::PARAM_INT);
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

        public function checkBreturn($breturn) {
            if( ! $breturn ) {
                header("Location:" . Breturn::baseurl() . "app/list.php");
            }
        }
    }
?>