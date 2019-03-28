<?php
session_start(); //Запускаем сессии
set_time_limit(0);
include('AuthClass.php');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
	<script src="js/jquery.js"></script>
	<link rel="stylesheet" href="css/bootstrap.min.css" >
	
    <link rel="stylesheet" href="css/main.css">
	<script type="text/javascript" language="javascript">
		function checkfile(sender) {
			var validExts = new Array(".xlsx", ".xls");
			var fileExt = sender.value;
			fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
			if (validExts.indexOf(fileExt) < 0) {
			  alert("Формат данных не подходящий, используйте только " +
					   validExts.toString() + " типы.");
			  return false;
			}
			else return true;
		}
	</script>


</head>

<body class="text-center">

<div class="container">

	<div class="row">
		<div class="row">

		<!--<a class="form-control" href="index.php?is_exit=1">Exit</a>-->
		</div>

		<form class="form-signin" enctype="multipart/form-data" method="POST">
			<input id="number" class="form-control" type="tel" name="number" placeholder="XXXXXXXXXX" pattern="[0-9]{10}">
			 <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
			<input id="file" class="form-control" type="file" name="file" onchange="checkfile(this)";  title="Открыть файл"/>  <a href="#"><img title="Для получения результата загрузите файл формата .xls или .xlsx. Номера телефонов запишите в столбик А один за другим. В номере должно содержаться 10 символов без 8 и +7" src="css/Ball.png"/></a>
			<input id="sbmt" class="btn btn-lg btn-primary btn-block" type="submit" value="Искать" title="Искать"/>
			 <br>
		   <textarea id="result" hidden></textarea>
		   <input id="copy" class="btn btn-lg btn-primary btn-block" type="button" value="Скопировать" title="Скопировать" hidden>
			<a href="#" class="reset" title="Очистите форму от старых данных, для выведения новых" >Очистить форму</a>
		</form>
		
	</div>
</div>

</body>

</html>

<?php

if(isset($_POST["number"]) && is_numeric($_POST["number"])) {
	
	$res = $auth->selectByNum($_POST['number']);
	
	echo "<script>
			document.getElementById('result').hidden = false;
			document.getElementById('copy').hidden = false;
			
			document.getElementById('number').value = '';
			document.getElementById('result').value = '".$res[1]."';
			
		</script>";
}

if ( isset($_FILES["file"]["name"]) && ($_FILES["file"]["name"]) != '' && preg_match("/\.xls[x]*/", $_FILES["file"]["name"])) {
	
	$spreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES["file"]["tmp_name"]);
	$number = '';
	$arr = array();
	$arr_n = array();
	$i = 1;
	$spreadsh = new Spreadsheet();
	
	while($number !== null /*  && $i < 20 */ ) {
		
		if ($i % 3 == 0) {
			sleep(1);
		} else {
			//	sleep(3);
		}
		
		$number = $spreadSheet->getActiveSheet()->getCell('A'.$i)->getValue();
		//echo "<br>$number<br>";
		if(mb_strlen($number) > 10) {
			
			$c = mb_strlen($number) - 10;
			$number = substr($number, $c);
		}
		
		if($number !== null) {
			$arr_n[] = $number;
		}
		
		$i++;

	}
	$i = 1;

	if($arr_n) {
		
		$r = $auth->selectByNums($arr_n);
		$writer = new Xlsx($spreadsh);
		$sheet = $spreadsh->getActiveSheet();
		
		foreach ($r as $vals) {
			
			$sheet->setCellValue('A'.$i, $vals[0]);
			$sheet->setCellValue('B'.$i, $vals[1]);
			$i++;
		}	
	}
	
	//var_dump($r);
	
	$num = time();
	
	$writer->save($num.'.xlsx');
	$inputFileName = $num.'.xlsx';

	if (file_exists($inputFileName)) {
		// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
		// если этого не сделать файл будет читаться в память полностью!
		if (ob_get_level()) {
		  ob_end_clean();
		}
		// заставляем браузер показать окно сохранения файла
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($inputFileName));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($inputFileName));
		// читаем файл и отправляем его пользователю
		readfile($inputFileName);
		unlink($inputFileName);
		exit;
	} 
}
?>

<script>
	var button = document.getElementById('copy');
	
	button.addEventListener('click', function () {
	  
		var ta = document.getElementById('result'); 
		  
		var range = document.createRange();
		range.selectNode(ta); 
		window.getSelection().addRange(range); 
		 
		try { 
			document.execCommand('copy'); 
		} catch(err) { 
			console.log('Can`t copy, boss'); 
		} 
		  
		window.getSelection().removeAllRanges();
	});
	
	var button = document.getElementById('sbmt');
	
	button.addEventListener('click', function () {
		window.getSelection().removeAllRanges();
		location.reload(true);
		document.getElementById('result').hidden = true;
		document.getElementById('copy').hidden = true;
	});
	
	$(".reset").click(function() {
		$(this).closest('form').find("input[type=text],input[type=file],input[type=tel], textarea").val("");
	});
	
</script>