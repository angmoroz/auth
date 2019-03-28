<?php
//session_start(); //Запускаем сессии
 
/** 
 * Класс для авторизации
 * @author дизайн студия ox2.ru 
 */ 
class AuthClass {
    //private $_login; //Устанавливаем логин
    //private $_password; //Устанавливаем пароль
	

	public function ConnectBD() {
		$link = @mysqli_connect('localhost', 'root', '', 'ww');
		mysqli_set_charset($link,"utf8");


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
		//var_dump($_SESSION);
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
			
			//$hash = md5(generateCode(10)); //hash='".$hash."'

			$_SESSION["is_auth"] = true; //Делаем пользователя авторизованным
			//$_SESSION["hash"] = $hash;
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
	
	// not in use yet
	public function generateCode($length=6) {

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";

		$code = "";

		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length) {

				$code .= $chars[mt_rand(0,$clen)];  
		}

		return $code;

	}
	
	public function selectMigrant($num) {
		
		$link = $this->ConnectBD();
		$result = array();
		
		$query = $link->query("SELECT region, operator FROM `defs` WHERE ibegin <= ".$link->real_escape_string($num)." and iend >= ".$link->real_escape_string($num));
		$row = mysqli_fetch_row($query);
		if($row)
		{
			$res = "Номер телефона: ".$num."; регион: ".$row[0]."; оператор: ".$row[1].";" ;
			$result[0] = $num;
			$result[1] = $res;
		} else {
			$result = array();
		}
		 return $result;
	}
	
	
	public function selectByNum($num) {
		
		$link = $this->ConnectBD();
		
		$res = '';
		
		$query = $link->query("SELECT number, RegionCode, ownerid, donorid,  mnc, portdate from portsall where Number = ".$link->real_escape_string($num));
		
		if($query) {
			$row = mysqli_fetch_row($query);
			
			if($row)
			{
				$res[0] = $num;
				$res[1] = "Номер телефона: ".$row[0]."; регион: ".$row[1]."; оператор: ".$row[2]."; старый оператор: ".$row[3]."; сеть: ".$row[4]."; дата переноса: ".$row[5];
				
			} else {
				$res = $this->selectMigrant($num);
			}
		}
		
		return $res;
		
	}
	
	public function selectByNums($nums) {
		
		$link = $this->ConnectBD();
		
		$res = array();
		$arr = array();
		$migrants = array();
		$result = array();
		$all = array();
		
		$query = "SELECT number, RegionCode, ownerid, donorid,  mnc, portdate from portsall where ";
		
		foreach($nums as $num) {
			$query .= "number = ".$num." or ";
		}
		
		$query = rtrim($query, 'or ');

		$query1 = $link->query($query);

		while ($row = $query1->fetch_row()) {
			
			$arr[]=$row[0];
			$result[0] = $row[0];
			$result[1] = "Номер телефона: ".$row[0]."; регион: ".$row[1]."; оператор: ".$row[2]."; старый оператор: ".$row[3]."; сеть: ".$row[4]."; дата переноса: ".$row[5];
			$all[] = $result;
		}

		foreach ($nums as $key) {
			
			if(!in_array($key, $arr)) {
				$migrants[] = $this->selectMigrant($key);
			}
		}
		
		return array_merge($all,$migrants);
	}
}
 
?>