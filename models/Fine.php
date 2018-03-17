<?php
    require_once("../db/Database.php");
    require_once("../interfaces/IFine.php");

    class Fine implements IFine {
    	private $con;
        private $fineID;
        private $loanID;
        private $total_amount;

    	public function __construct(Database $db){
    		$this->con = new $db;
    	}

        public function setFineID($returnID){
            $this->fineID = $fineID;
        }

        public function setLoanID($loanID){
            $this->loanID = $loanID;
        }

        public function setTotal_amount($actual_return_date){
            $this->total_amount = $total_amount;
        }

    	//insertamos usuarios en una tabla con postgreSql
    	public function save() {
    		try{
    			$query = $this->con->prepare('INSERT INTO fines (loanID, total_amount) values (?,?)');
                $query->bindParam(1, $this->fineID, PDO::PARAM_STR);
    			$query->bindParam(2, $this->total_amount, PDO::PARAM_STR);
    			$query->execute();
    			$this->con->close();
    		}
            catch(PDOException $e) {
    	        echo  $e->getMessage();
    	    }
    	}

        public function update(){
    		try{
    			$query = $this->con->prepare('UPDATE fines SET loanID = ?, total_amount = ? WHERE fineID = ?');
    			$query->bindParam(1, $this->loanID, PDO::PARAM_STR);
    			$query->bindParam(2, $this->total_amount, PDO::PARAM_STR);
                $query->bindParam(3, $this->fineID, PDO::PARAM_INT);
    			$query->execute();
    			$this->con->close();
    		}
            catch(PDOException $e){
    	        echo  $e->getMessage();
    	    }
    	}

    	//obtenemos usuarios de una tabla con postgreSql
    	public function get(){
    		try{
                if(is_int($this->returnID)){
                    
                    $query = $this->con->prepare('SELECT * FROM fines WHERE fineID = ?');
                    $query->bindParam(1, $this->fineID, PDO::PARAM_INT);
                    $query->execute();
        			$this->con->close();
        			return $query->fetch(PDO::FETCH_OBJ);
                }
                else{
                    
                    $query = $this->con->prepare('SELECT * FROM fines');
        			$query->execute();
        			$this->con->close();
                    
        			return $query->fetchAll(PDO::FETCH_OBJ);
                }
    		}
            catch(PDOException $e){
    	        echo  $e->getMessage();
    	    }
    	}

        public function delete(){
            try{
                $query = $this->con->prepare('DELETE FROM fines WHERE fineID = ?');
                $query->bindParam(1, $this->fineID, PDO::PARAM_INT);
                $query->execute();
                $this->con->close();
                return true;
            }
            catch(PDOException $e){
                echo  $e->getMessage();
            }
        }

        public static function baseurl() {
             return stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . "/crudpgsql/";
        }

        public function checkBreturn($fine) {
            if( ! $fine ) {
                header("Location:" . Fine::baseurl() . "app/list.php");
            }
        }
    }
?>