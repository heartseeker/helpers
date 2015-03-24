<?php defined('INFINIT') or die(basename(__FILE__));

class Login {

	private $_username;
	private $_password;
	private $_userLevel;
	private $_fullName;
	private $_login;
	private $_access = 0;
	private $_id;
	private $_countryCode;
	private $_errors = array();

	public function __construct() {

		@$this->_username = ($_POST['username']);
		@$this->_password = ($_POST['password']);
		$this->_login = isset($_POST['login']) ? 1 : 0;

	}

	public function isLoggedIn() {
		($this->_login) ? $this->verifyPost() : $this->verifySession();

		return $this->_access;
	}

	public function filter($var) {
		return preg_replace('/[^a-zA-Z0-9]/', '', $var);
	}

	public function verifyPost() {
		try {
			if(!$this->isDataValid())
         		throw new Exception('Invalid Form Data');
			
			if (!$this->verifyDatabase())
				throw new Exception('Invalid Username/Password');

			$this->_access = 1;
			$this->registerSession();
		} catch(Exception $e) {
			$this->_errors[] = $e->getMessage();
		}
	}
	
	public function verifySession() {
		if($this->sessionExist())
		$this->_access = 1;
	}
	
	public function verifyDatabase() {
    	
		$sql = "SELECT * FROM dash_user WHERE user_name = :username AND user_pass = :userpass AND is_active = 1";
		$values = array('username' => $this->_username, 'userpass' => $this->_password);
		$rs = DataManager::fetchQuery($sql, $values);
		
		if(count($rs)) {
			$this->_id = $rs[0]['user_id'];
			$this->_userLevel = $rs[0]['user_level'];
			$this->_fullName = $rs[0]['fullname'];
			$this->_countryCode = $rs[0]['countrycode'];
			return true;
		} else {
			return false;
		}

 	}

	public function isDataValid() {
    	return (preg_match('/^[a-zA-Z0-9]{5,12}$/',$this->_username) && preg_match('/^[a-zA-Z0-9]{5,12}$/',$this->_password))? 1 : 0;
	}
	
	public function registerSession() {
	    $_SESSION['ID'] = $this->_id;
	    $_SESSION['USERNAME'] = $this->_username;
		$_SESSION['USERLEVEL'] = $this->_userLevel;
		$_SESSION['FULLNAME'] = $this->_fullName;
		$_SESSION['COUNTRYCODE'] = $this->_countryCode;
		$this->insertLog();
  	}
	
	public function sessionExist() {
    	return (isset($_SESSION['username']))? 1 : 0;
  	}
	
	public function getErrors() {
		return $this->_errors;
	}

	public function insertLog() {
		session_regenerate_id();
		
		$sql = "INSERT INTO `dash_log` (`user_id`,`session_id`,`user_agent`,`ip_address`,`log_in`) VALUES(:user_id, :session_id, :user_agent, :ip_address, NOW())";
		$values = array("user_id" => $_SESSION['ID'], 
						"session_id" => session_id(),
						"user_agent" =>  $_SERVER['HTTP_USER_AGENT'], 
						"ip_address" => $_SERVER['REMOTE_ADDR'], 
					   );
		$rs = DataManager::insert($sql, $values) or die("ERROR!");
		
		
	}

}
