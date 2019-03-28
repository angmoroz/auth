<?php
session_start(); //Запускаем сессии

include('AuthClass.php');

$auth = new AuthClass();

if (isset($_POST["login"]) && isset($_POST["password"])) { //Если логин и пароль были отправлены
    if (!$auth->auth($_POST["login"], $_POST["password"])) { //Если логин и пароль введен не правильно
        echo "<h2 style=\"color:red;\">Логин и пароль введен не правильно! Или необходимо подтверждение нового пользователя администратором</h2>";
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Whois call services</title>
	<script src="js/jquery.js"></script>
	<link rel="stylesheet" href="css/bootstrap.min.css" >
	
    <link rel="stylesheet" href="css/main.css">

</head>

<body class="text-center">

<div class="container">

	<div class="row">
		<div class="row">

		<!--<a class="form-control" href="index.php?is_exit=1">Exit</a>-->
		</div>
<form method="post" class="form-signin" action="">
    <input class="form-control" type="text" name="login" placeholder="Логин" value="<?php echo (isset($_POST["login"])) ? $_POST["login"] : null; // Заполняем поле по умолчанию ?>" autofocus />
    <input class="form-control" type="password"  placeholder="Пароль" name="password" value="" />
    <input class="btn btn-lg btn-primary btn-block" type="submit" value="Войти" />
	
	<a href="register.php">Регистрация</a>
</form>
	</div>
</div>

</body>

</html>
<?php } ?>