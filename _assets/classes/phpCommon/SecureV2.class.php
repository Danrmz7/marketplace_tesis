<?php
// The code contained within this file was created by and is owned by NuTerra LLC
// You must obtain permission from NuTerra before using this file, or any of it's contents
// Use without permission may result in procecution.

/*************************************************
The class is meant to allow for easy, standard and feature rich secure areas on a site
 FEATURES:
  username/password style logins
  flexable tables / fields
  ability to have a "Remember Me" style feature
  multiple secure areas on a single domain
  easy user role verification
  ability to edit the account of the currently logged in user without risking logging them out
 USAGE:
  Secure() - Constructor:
   Sets up the class and checks if the user is logged in/logging in.
   Parameters:
    $sql - An active sql handler with functions such as Select and Update
    $table - The user table in the database.
      If you are using the permission functionality, you may need to join a table to the main user table.
      To do this simply provide it in the following syntax: "user_table INNER JOIN role_table ON (user_table.role_id = role_table.id)"
       using your own table and fields.
      If you do use the join, when you call getCurrentUser(), you will get all the data from both of the joined tables (with the first table's id field)
      However, using updateUser will only update the main user table, and an error will occur if you try to update the role table
    $unField - The name of the field in the database for the username. The field on the login form must also match this
    $psField - The name of the field in the database for the password. The field on the login form must also match this
    $idField - The name of the unique (and consistant) id in the database.
    $remField - The name of the field on the login form that if present, triggers "Remember Me" functionality
    $lvlField - The field in the database that defines the access level the user has.
    $secureName - The prefix for all session and cookie vars, this must be unique if you have mulitple logins on the same domain
    $loginPage - The page that contains the login form. This is where users are redirected when they try to access a page without logging in or when they logout
    $use_md5 - A flag that determines if md5 encryption will be used for passwords
  _CheckLogin():
   *** There is no need to manually call this, it is called in the constructor
   Checks if the user is logged in, or logs in the user.
   $_POST is used for the login information
  checkPermission():
   Checks if the logged in user meets the permission requirements provided.
   Matches the value of the $lvlField specifed in the constructor to the provided permission requirments.
   Returns true if the logged in user has permission, otherwise returns false.
   Parameters:
    $validPermissions - A string with the required permission, or an array of strings with the required permissions
  getCurrentUser():
   Returns all the data on currently logged in user as found in the database.
  updateUser():
   Updates the user's information, without risking logging the user out, even if username or password have changed.
   Prevents either the username or password from being empty, and the id from being used.
   Prevents there from being multiple users with the same username
   Parameters:
    $fields - A keyed array with the fields and the data to put into the database.
     If this is left empty, $_POST will be used.
     Must be formmated so the keys match fields in the database. Ex:
      array('name' => 'John Doe', 'state' => 'WI', 'password' => 'pass324');
     Errors will occur if there are any keys that to not have corresponding fields in the database
     Values for the password field should not be encrypted, md5 will be automaticall added if $use_md5 is set to be true
*************************************************/
require_once("addStripSlashes.php");

class Secure {
    public $sql;
    public $table;
    public $unField;
    public $remField;
    public $psField;
    public $idField;
    //public $remField;
    public $lvlField;
    public $secureName;
    public $use_md5; 
    
    public $primaryTable;
    public $currentUsername;
    public $currentPassword;
    public $currentId;

    private $_nuterraDb="DanielAyala";
    private $_db;
  
    function Secure($sql, $db, $table, $unField /*= 'username'*/, $psField /*= 'password'*/, $idField /*= 'id'*/, $remField = 'remember_me', $lvlField = '', $secureName = 'secure', $loginPage /*= 'login.php'*/, $use_md5 = true) {     
    $this->sql = $sql; 
    $this->_db = $db;
    $this->table = $table;
    $this->unField = $unField;
    $this->psField = $psField;
    $this->idField = $idField;
    $this->remField = $remField;
    $this->lvlField = $lvlField;
    $this->secureName = $secureName;
    $this->use_md5 = $use_md5;

    $this->primaryTable = substr($table, 0, strpos($table.' ', ' '));

    //if the session variables are set, set them as the currently logged in username/password/id
    if ($_SESSION[$this->secureName.'_username'] && $this->currentPassword = $_SESSION[$this->secureName.'_password']) {
      $this->currentUsername = $_SESSION[$this->secureName.'_username'];
      $this->currentPassword = $_SESSION[$this->secureName.'_password'];
      $this->currentId = $_SESSION[$this->secureName.'_id'];
    //if the remember me cookies are set, set those as the current username/password
    } elseif ($_COOKIE[$this->secureName.'_remember_username'] && $_COOKIE[$this->secureName.'_remember_password']) {
      $this->currentUsername = $_COOKIE[$this->secureName.'_remember_username'];
      $this->currentPassword = $_COOKIE[$this->secureName.'_remember_password'];
    }

      //attempt to login/check login status
      if ($loginError = $this->_checkLogin()) {
        //$_SESSION[$this->secureName.'_redirect_on_login'] = $_SERVER['REQUEST_URI'].'?'.ereg_replace('logout=[^\&]+\&?', '', $_SERVER['QUERY_STRING']);
        $_SESSION[$this->secureName.'_redirect_on_login'] = preg_replace('/logout=.&?/', '', $_SERVER['REQUEST_URI']);
        header('Location: ./'.$loginPage.'?'.$unField.'='.$_POST[$unField].'&error='.$loginError);
        exit();
      }
    }
    
    function _checkLogin(){
        //logout if any secure page gets a true value for "logout" in the q-string ex (index.php?logout=1)
        if ( isset($_GET['logout']) ) {
          unset($_SESSION[$this->secureName.'_username']);
          unset($_SESSION[$this->secureName.'_password']);
          unset($_SESSION[$this->secureName.'_id']);

          unset($_COOKIE[$this->secureName.'_remember_username']);
          unset($_COOKIE[$this->secureName.'_remember_password']);

          setcookie($this->secureName.'_remember_username', "", time()-3600);
          setcookie($this->secureName.'_remember_password', "", time()-3600);

          return 'You Have Logued Out The Session';
        }
    
    $query='SELECT *, '.$this->primaryTable.'.'.$this->idField.' 
        FROM '.$this->table.' 
        WHERE '.$this->primaryTable.'.'.$this->unField.' LIKE ? AND '.$this->primaryTable.'.'.$this->psField.' = ? 
        LIMIT 1';
    $params=array($this->currentUsername, $this->use_md5?md5($this->currentPassword):$this->currentPassword);
        //if logged in
        if($this->currentUsername && $this->currentPassword && ($account = $this->sql->select($query, $params))) {
          $this->currentId = $_SESSION[$this->secureName.'_id'] = $account[0][$this->idField];
          return false;
        //if logged in as admin
        } elseif ($this->loggedAsAdmin()) {
          $this->currentId = $_SESSION[$this->secureName.'_id'] = 'NuterraAdmin';
          return false;
        //if logging in as admin
        } elseif ($this->logingInAsAdmin()){ 
          $this->currentUsername = $_SESSION[$this->secureName.'_username'] = $_POST[$this->unField];
          $this->currentPassword = $_SESSION[$this->secureName.'_password'] = $_POST[$this->psField];
          $this->currentId = $_SESSION[$this->secureName.'_id'] = 'NuterraAdmin';
          if ($_POST[$this->remField]) {    
            setcookie($this->secureName.'_remember_username', $this->currentUsername, time()+315360000);
            setcookie($this->secureName.'_remember_password', $this->currentPassword, time()+315360000);
          }
          if($_SESSION[$this->secureName.'_redirect_on_login']) {
            header('Location: '.$_SESSION[$this->secureName.'_redirect_on_login']);
      die();
          }else{
            header('Location: '.$_SERVER['SCRIPT_NAME']);
      die();
          }
          return false;
        //if logging in
        }elseif($_POST){ 
      $query='SELECT *, '.$this->primaryTable.'.'.$this->idField.' 
          FROM '.$this->table.' 
          WHERE '.$this->primaryTable.'.'.$this->unField.' LIKE ? AND '.$this->primaryTable.'.'.$this->psField.' = ? 
          LIMIT 1';
      $params=array($_POST[$this->unField], $this->use_md5?md5($_POST[$this->psField]):stripsDecodeInjection($_POST[$this->psField]));    
      if(!$_POST[$this->unField]){
        return 'You must enter a username.';
      }elseif( !$_POST[$this->psField] ){
        return 'You must enter a password.';
      }elseif($account = $this->sql->select($query, $params)){
        $this->currentUsername = $_SESSION[$this->secureName.'_username'] = $_POST[$this->unField];
        $this->currentPassword = $_SESSION[$this->secureName.'_password'] = $_POST[$this->psField];
        $this->currentId = $_SESSION[$this->secureName.'_id'] = $account[0][$this->idField];
        if($_POST[$this->remField]){    
          setcookie($this->secureName.'_remember_username', $this->currentUsername, time()+315360000);
          setcookie($this->secureName.'_remember_password', $this->currentPassword, time()+315360000);
        }
        if ($_SESSION[$this->secureName.'_redirect_on_login']) {
          header('Location: '.$_SESSION[$this->secureName.'_redirect_on_login']);
          die();
        } else {
          header('Location: '.$_SERVER['SCRIPT_NAME']);
          die();
        }
        return false;
      }else {
            return 'The username or password are not correct';
          }
        } else {
          return ' ';
        }
        
    }
   /*---------------------------------------------------------
  logingInAsAdmin: Bool
    - NOTE: Added on 03/01/2012
  -----------------------------------------------------------*/
  public function logingInAsAdmin(){
    $username = trim($_POST[$this->unField]);
    $password = trim($_POST[$this->psField]);
    
    /*if(!empty($username) && !empty($password)){
      $this->sql->connect($this->_nuterraDb, "64.207.154.250");
      $record=$this->sql->select("SELECT * FROM staffusers WHERE username LIKE ? AND password =? LIMIT 1;", array($username, $password));
      $this->sql->connect($this->_db);
      //NOTE: This does not really check for admin status
      if(!empty($record))
        return true; 
    }*/
    return false;
  }
  /*---------------------------------------------------------
  loggedAsAdmin: Bool
    - NOTE: Added on 03/01/2012
  -----------------------------------------------------------*/
  public function loggedAsAdmin(){
    $username = $this->currentUsername;
    $password = $this->currentPassword;
    
    /*if(!empty($username) && !empty($password)){
      $this->sql->connect($this->_nuterraDb, "64.207.154.250");
      $record=$this->sql->select("SELECT * FROM staffusers WHERE username LIKE ? AND password =? LIMIT 1;", array($username, $password));
      $this->sql->connect($this->_db);
      //NOTE: This does not really check for admin status
      if(!empty($record))
        return true; 
    } */
    return false;
  }
    function checkPermission($validPermissions) {
        //check to make sure that a lvlField has been specified
        if (!$this->lvlField) {
            trigger_error('No Level Field Specified for Secure Class', E_USER_WARNING);
            return false;
        //makes sure a valid permission is specified
        }if (!$validPermissions) {
            trigger_error('No Permssions Provided', E_USER_WARNING);
            return false;
        }
        //if logged in using the universal password, permission is always granted
        if ($this->currentId == 'NuterraAdmin') {
            return true;
        }
        //makes sure that the supplied permission(s) are always an array
        if (!is_array($validPermissions)) {
            $validPermissions = array($validPermissions);
        }
        //check if the logged in user has permission
        if ($this->currentId && $this->sql->select('SELECT * FROM '.$this->table.' WHERE '.$this->primaryTable.'.'.$this->idField.' = \''.$this->currentId.'\' AND '.$this->lvlField.' IN (\''.implode('\', \'', $validPermissions).'\') LIMIT 1;')) {
            return true;
        } else {
            return false;
        }
    }
    
    function getCurrentUser() {
    $query='SELECT *, '.$this->primaryTable.'.'.$this->idField.' 
        FROM '.$this->table.' 
        WHERE '.$this->primaryTable.'.'.$this->idField.' = ?
        LIMIT 1';
    $params=array($this->currentId);
    
        //if the id is NuterraAdmin, only return the username
        if ($this->currentId == 'NuterraAdmin') {
            return array($this->unField => $this->currentUsername);
        } elseif($this->currentId && $account = $this->sql->select($query, $params)) {
            return $account[0];
        } else {
            return false;
        }
    }
    
    function updateAccount($fields = null) {
        /*****************************
        Updates the database entry for the logged in user
        
        $fields will include an array with (and only with) the desired fields/values to be modified the database EX:
          ('username' =>  'johnDoe', 'password' => 'mypassword42', 'real_name' => 'John Doe')
        
        There is no complex validation done, the only validation makes sure that there are no 
          empty usernames/passwords and that the username is unique across the users
        *****************************/
        if (!$fields) {
            $fields = $_POST;
        }
        if ($fields && is_array($fields) && $this->currentId != 'NuterraAdmin') {
            //do simple verification on username
            if (array_key_exists($this->unField, $fields)) {
        $query='SELECT * 
            FROM '.$this->table.' 
            WHERE '.$this->primaryTable.'.'.$this->unField.' LIKE ?
              AND '.$this->primaryTable.'.'.$this->idField.' != ?
            LIMIT 1;';
        $params=array($fields[$this->unField], $this->currentId);
    
                //make sure a username is entered
                if (!$fields[$this->unField]) {
                    return 'Username cannot be empty.';
                //make sure that the username is not already taken (only if username is changing)
                } elseif ($fields[$this->unField] != $this->currentUsername && $this->sql->select($query, $params)) {
                    return 'A user with this username already exists.';
                }
            }
            
            //make sure that if the username or password are changed, the user stays logged in
            if (array_key_exists($this->unField, $fields)) {
                $this->currentUsername = $_SESSION[$this->secureName.'_username'] = $fields[$this->unField];
                if ($_COOKIE[$this->secureName.'_remember_username']) {
                    setcookie($this->secureName.'_remember_username', $this->currentUsername, time()+315360000);
                }
            }
            if (array_key_exists($this->psField, $fields)) {
                $this->currentPassword = $_SESSION[$this->secureName.'_password'] = $fields[$this->psField];
                if ($_COOKIE[$this->secureName.'_remember_password']) {
                    setcookie($this->secureName.'_remember_password', $this->currentUsername, time()+315360000);
                }
            }
            
            //make sure that the password is entered
            if (array_key_exists($this->psField, $fields)) {
                if (!$fields[$this->psField]) {
                    return 'Password cannot be empty.';
                }
                if ($this->use_md5) {
                    $fields[$this->psField] = md5($fields[$this->psField]);
                }
            }
            //remove the id field if it is present
            if (array_key_exists($this->idField, $fields)) {
                unset($fields[$this->idField]);
            }
            
            $sqlParts = array();
            foreach(array_keys($fields) as $field) {
                $sqlParts[] = $field.' = \''.trim(stripsDecodeInjection($fields[$field])).'\'';
            }
            $query = 'UPDATE '.$this->primaryTable.' SET '.implode(', ', $sqlParts).' WHERE '.$this->idField.' = ?;';
      $params=array($this->currentId);
            $this->sql->update($query, $params);

            return false;
        }
    }
}

?>
