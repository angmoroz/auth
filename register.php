<?php

include('AuthClass.php');

$auth = new AuthClass();



if(isset($_POST['submit']))

{

    $err = array();


    # проверям логин

    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))

    {

        $err[] = "Логин может состоять только из букв английского алфавита и цифр";

    }

    

    if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)

    {

        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";

    }

    

    # проверяем, не сущестует ли пользователя с таким именем

    //$query = $link->query("SELECT COUNT(id) FROM users WHERE login='".$link->real_escape_string($_POST['login'])."'");
	
	
	var_dump($auth->check($_POST['login']) );

    if($auth->check($_POST['login']) > 0)

    {

        $err[] = "Пользователь с таким логином уже существует в базе данных";

    }

    

    # Если нет ошибок, то добавляем в БД нового пользователя

    if(count($err) == 0)

    {

       $auth->insert($_POST['login'], $_POST['password']); 

        header("Location: index.php"); exit();

    }

    else

    {

        print "<b>При регистрации произошли следующие ошибки:</b><br>";

        foreach($err AS $error)

        {

            print $error."<br>";

        }

    }

}

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



<form class="form-signin"  method="POST">

<input class="form-control" name="login" placeholder="Логин" type="text">

<input class="form-control" name="password" placeholder="Пароль" type="password">

<input class="btn btn-lg btn-primary btn-block" name="submit" type="submit" value="Зарегистрироваться">

</form>
	</div>
</div>

</body>