<?php
session_start(); //Запускаем сессии

include('AuthClass.php');

$auth = new AuthClass();

if (isset($_POST["login"]) && isset($_POST["password"])) { //Если логин и пароль были отправлены
    if (!$auth->auth($_POST["login"], $_POST["password"])) { //Если логин и пароль введен не правильно
        echo "<h2 style=\"color:red;\">Логин и пароль введен не правильно!</h2>";
    }
}

if (isset($_GET["is_exit"])) { //Если нажата кнопка выхода
    if ($_GET["is_exit"] == 1) {
        $auth->out(); //Выходим
        header("Location: index.php"); //Редирект после выхода
   //     header("Location: ?is_exit=0"); //Редирект после выхода
    }
}

if ($auth->isAuth()) { // Если пользователь авторизован, приветствуем:  
    echo "Здравствуйте, " . $auth->getLogin() ;
    echo "<br/><br/><a href=\"?is_exit=1\">Выйти</a>"; //Показываем кнопку выхода
	header("Location: find.php");
} 
else { //Если не авторизован, показываем форму ввода логина и пароля
?>
<form method="post" action="">
    Логин: <input type="text" name="login" value="<?php echo (isset($_POST["login"])) ? $_POST["login"] : null; // Заполняем поле по умолчанию ?>" /><br/>
    Пароль: <input type="password" name="password" value="" /><br/>
    <input type="submit" value="Войти" />
	
	<a href="register.php">Регистрация</a>
</form>
<?php } ?>