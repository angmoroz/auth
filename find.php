<?php
session_start(); //Запускаем сессии

/** 
 * Класс для авторизации
 * @author дизайн студия ox2.ru 
 */ 
include('AuthClass.php');

$auth = new AuthClass();

if(!$auth->getLogin()) {

	header("Location: index.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Whois call services</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>

<a href="index.php?is_exit=1">Exit</a>

<form action="#" class="form-input">
    <input class="phone-nomber" type="phone" placeholder="Введите номер телефона">
    <input class="btn-search" type="button" value="Искать" title="Искать">
    <input class="open-file" type="file"  title="Открыть файл"> <br>
    <input class="result" type="text" title="Результат">
    <input class="result-copy" type="button" value="Скопировать" title="Скопировать в буфер обмена">
</form>

</body>

</html>