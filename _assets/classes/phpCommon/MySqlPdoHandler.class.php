<?php
/**
 * Singleton Pattern
 *
 * @author Phia
 * @version 1.0
 * @created 24-Jul-2013 9:35:55 AM
 */
class MySqlPdoHandler{

	private static $_singleton;
	private $_username;
	private $_password;
	private $_connection;

	/**
	 * Return: Void
	 */
	private function __construct(){  }

	/**
	 * Return: Void
	 */
	public function __destruct(){
		$this->_connection = null;	//Close connection. Destroy the object.
	}

	/**
	 * Return: Instance
	 */
	public static function getInstance(){
		if(!self::$_singleton)
			self::$_singleton = new MySqlPdoHandler();
		return self::$_singleton;
	}

	/**
	 * Return: Resource ID
	 */
	public function testGetConnection(){
		return $this->_connection;
	}


	/**
	 * Return: Void
	 */
	public function connect($dbname, $host="localhost"){
		//Guasaneri
		if($_SERVER["SERVER_ADDR"]=="185.224.138.210"){
			$this->_username = 'root';
			$this->_password = 'pA2D@b*6s';
		}
		else{
			$this->_username = 'root';
			$this->_password = '';
		}


		//MySQL with PDO_MYSQL
		try{
			$this->_connection = null;	//Close connection. Destroy the object.
			$this->_connection = new PDO("mysql:host=$host;dbname=$dbname", $this->_username, $this->_password);
			$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}catch(PDOException $e) {
			//echo $e->getMessage();
			die("Database connection could not be established!<br/>");
		}
	}

	/**
	 * NOTE: For debugging purposes, this can be email to admin.
	 * Return: Void
	 */
	protected function _debug($error_string=NULL, $exception=NULL){
		$errors=$this->_connection->errorInfo();
		if(empty($error_string) && $errors[1] > 0)
			$error_string= "SQLSTATE: ".$errors[0]." - Code: ".$errors[1]." - Message: ".$errors[2];
		elseif($exception != NULL)
			$error_string="Caught Exception: ".$exception->getMessage();
		$subject='SQL Error - '.$errors[0].'';
		$body="The error <b>$error_string</b> occurred in the file ".$_SERVER['SCRIPT_FILENAME']." on line $line<p>";
		$body.='REQUEST METHOD='.$_SERVER['REQUEST_METHOD'].'<br>
				REQUEST TIME='.$_SERVER['REQUEST_TIME'].'<br>
				QUERY STRING='.$_SERVER['QUERY_STRING'].'<br>
				HTTP REFERER='.$_SERVER['HTTP_REFERER'].'<br>
				HTTP USER_AGENT='.$_SERVER['HTTP_USER_AGENT'].'<br>
				REMOTE ADDR='.$_SERVER['REMOTE_ADDR'].'<br>
				SCRIPT FILENAME='.$_SERVER['SCRIPT_FILENAME'].'<br>
				REQUEST URI='.$_SERVER['REQUEST_URI'].'<p>';
		$backtrace=debug_backtrace();
//print_r($backtrace);
		foreach($backtrace as $b){
			foreach($b as $key=>$val){
				if(gettype($key) !="object" && gettype($val) !="object"){
					if(is_array($val)){
						$body.=$key;
						foreach($val as $k=>$v){
							if(gettype($k) !="object" && gettype($v) !="object")
								$body.=$k.' = '.$v.'<br>';
						}
					}else
						$body .=$key.' = '.$val.'<br>';
				}
			}
		}

		$to='junior.gibson.lp@gmail.com';
		$sender='PHP Error Master <junior.gibson.lp@gmail.com>';
		$message='<font color="#ff0000">'.$body.'</font>';
		$headers ="From: " . $sender."\r\n";
		$headers.="MIME-Version: 1.0\r\n";
		$headers.="Content-Type: text/html; charset=iso-8859-1\r\n";
		$headers.="X-Priority: 1\r\n";
		$headers.="X-Mailer: PHP / ".phpversion()."\r\n";

		//Display error in local server
		
			echo $message;
		//Email error in live server
	}

	/**
	 * Return: Bool
	 */
	public function begin(){
		if(!empty($this->_connection)){
			if($this->_connection->beginTransaction())
				return true;
			$this->_debug();
		}
		return false;
	}

	/**
	 * Return: Bool
	 */
	public function commit(){
		if(!empty($this->_connection)){
			if($this->_connection->commit())
				return true;
			$this->_debug();
		}
		return false;
	}

	/**
	 * Return: Bool
	 */
	public function rollback(){
		if(!empty($this->_connection)){
			if($this->_connection->rollback())
				return true;
			$this->_debug();
		}
		return false;
	}

	/**
	 * Return: 2D Array
	 */
	public function select($query, $params=NULL){
		$records=array();
		//Make sure query contains the word "select" and connection is valid
		if(stristr($query,"select") && !empty($this->_connection)){
			try{
				$stmt = $this->_connection->prepare($query);
				$stmt->execute($params);
				$i=0;
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					$records[$i++] = $row;	//Put row into array
				}
				$stmt->closeCursor();	//Release database resources before issuing next call
			}catch(Exception $e){
				$this->_debug(NULL, $e);
			}
		}else	 $this->_debug("Invalid Select Query or No Connection - $query");
		return $records;
	}

	/**
	 * Return: Bool
	 */
	public function update($query, $params){
		$records=array();
		//Make sure query contains the word "update", connection is valid, and params is valid
		if(stristr($query,"update") && !empty($this->_connection) && !empty($params)){
			try{
				$stmt = $this->_connection->prepare($query);
				$status=$stmt->execute($params);
				//$rowsAffected = $stmt->rowCount();
				$stmt->closeCursor();	//Release database resources before issuing next call
				if($status)
					return true;
			}catch(Exception $e){
				$this->_debug(NULL, $e);
			}
		}else	 $this->_debug("Invalid Update Query or No Connection - $query");
		return false;
	}

	/**
	 * Return: Primary key or true
	 */
	public function insert($query, $params){
		$records=array();
		$pk=0;
		//Make sure query contains the word "insert", connection is valid, and params is valid
		if(stristr($query,"insert") && !empty($this->_connection) && !empty($params)){
			try{
				$stmt = $this->_connection->prepare($query);
				$status=$stmt->execute($params);
				$pk=$this->_connection->lastInsertId();
				$stmt->closeCursor();	//Release database resources before issuing next call
				if(is_numeric($pk) && $pk>0)
					return $pk;
				//If table doesn't have pk and insert is successful, return true
				elseif($status)
					return true;
			}catch(Exception $e){
				$this->_debug(NULL, $e);
			}
		}else	 $this->_debug("Invalid Insert Query or No Connection - $query");
		return $pk;
	}

	/**
	 * Return: Bool
	 */
	public function delete($query, $params){
		$records=array();
		//Make sure query contains the word "delete", connection is valid, and params is valid
		if(stristr($query,"delete") && !empty($this->_connection) && !empty($params)){
			try{
				$stmt = $this->_connection->prepare($query);
				$status=$stmt->execute($params);
				$stmt->closeCursor();	//Release database resources before issuing next call
				if($status)
					return true;
			}catch(Exception $e){
				$this->_debug(NULL, $e);
			}
		}else	 $this->_debug("Invalid Delete Query or No Connection - $query");
		return false;
	}

	/**
	 * NOTE: This function allows for flexibility. Only use this function, if you know what you are doing.
	 * Return: Bool or 2D array
	 */
	public function QUERY($query, $params=NULL){
		$records=array();
		//Make sure connection is valid
		if(!empty($query) && !empty($this->_connection)){
			try{
				$stmt = $this->_connection->prepare($query);
				$status=$stmt->execute($params);
				$stmt->closeCursor();	//Release database resources before issuing next call
				if($status)
					return true;
			}catch(Exception $e){
				$this->_debug(NULL, $e);
			}
		}else	 $this->_debug("Invalid Query or No Connection - $query");
		return false;
	}

	/**
	 * Return: String
	 */
	public function stripslashes_deep($value){
		$value = is_array($value)?array_map('stripslashes_deep', $value):stripslashes($value);
		return $value;
	}


	/**
	 * Return: String
	 */
	public function getPlaceHolders($keys){
		$output="";
		if(is_array($keys) && count($keys)>0)
			$output = join(',', array_fill(0, count($keys), '?'));
		return $output;
	}

	/**
	 * Return: String
	 */
	public function getUpdatePlaceHolders($keys){
		$output="";
		if(is_array($keys) && count($keys)>0){
			foreach($keys as $k=>$v){
				$temp[]="$k=?";
			}
			$output=implode(", ",$temp);
		}
		return $output;
	}
}
?>
