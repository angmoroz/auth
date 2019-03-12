<?php
//session_start(); //Запускаем сессии
 
/** 
 * Класс для авторизации
 * @author дизайн студия ox2.ru 
 */ 
class AuthClass {
    private $_login; //Устанавливаем логин
    private $_password; //Устанавливаем пароль
	
	/*function __construct() {
		$link = $this->ConnectBD();
		
		$query = $link->query("SELECT id,login, password FROM users WHERE login='".$link->real_escape_string($_POST['login'])."' LIMIT 1");

		$data = $query->fetch_assoc();
		
		$this->_login  = $data['login'];
		$this->_password = $data['password'];
	}*/
	
	public function ConnectBD() {
		$link = @mysqli_connect('localhost', 'root', '', 'ww');

		if (!$link) {
			die('Connect Error: ' . mysqli_connect_errno());
		}
		return $link;
	}
 
    /**
     * Проверяет, авторизован пользователь или нет
     * Возвращает true если авторизован, иначе false
     * @return boolean 
     */
    public function isAuth() {
        if (isset($_SESSION["is_auth"])) { //Если сессия существует
            return $_SESSION["is_auth"]; //Возвращаем значение переменной сессии is_auth (хранит true если авторизован, false если не авторизован)
        }
        else return false; //Пользователь не авторизован, т.к. переменная is_auth не создана
    }
     
    /**
     * Авторизация пользователя
     * @param string $login
     * @param string $passwors 
     */
    public function auth($login, $passwors) {
		
		$link = $this->ConnectBD();
		
		$query = $link->query("SELECT id,login, password FROM users WHERE login='".$link->real_escape_string($login)."' and role = 3 LIMIT 1");

		$data = $query->fetch_assoc();
		
        if ($login == $data['login'] && md5(md5($passwors)) == $data['password']) { //Если логин и пароль введены правильно
            $_SESSION["is_auth"] = true; //Делаем пользователя авторизованным
            $_SESSION["login"] = $login; //Записываем в сессию логин пользователя
            return true;
        }
        else { //Логин и пароль не подошел
            $_SESSION["is_auth"] = false;
            return false; 
        }
    }
	
	 /**
     * Метод возвращает есть ли пользователь с таким именем
     */
	
	public function check($login) {
		
		$row = 1;
		
		$link = $this->ConnectBD();
		
		$query = $link->query("SELECT COUNT(id) FROM users WHERE login='".$link->real_escape_string($login)."'");
		
		$row = mysqli_fetch_row($query);

		return $row[0];

	}
	
	public function insert($login, $password) {
		
		$link = $this->ConnectBD();
		
        # Убераем лишние пробелы и делаем двойное шифрование

        $passw = md5(md5(trim($password)));

        $link->query("INSERT INTO users SET login='".$login."', password='".$passw."'");
	}
     
    /**
     * Метод возвращает логин авторизованного пользователя 
     */
    public function getLogin() {
        if ($this->isAuth()) { //Если пользователь авторизован
            return $_SESSION["login"]; //Возвращаем логин, который записан в сессию
        }
    }
     
     
    public function out() {
        $_SESSION = array(); //Очищаем сессию
        session_destroy(); //Уничтожаем
    }
	
	
	public function generateCode($length=6) {

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";

		$code = "";

		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length) {

				$code .= $chars[mt_rand(0,$clen)];  
		}

		return $code;

	}
}
 
?>