<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("numbers.xlsx");
$number = '';
$arr = array();
$i = 1;

while($number !== null /* && $i < 20 */) {
	
	$number = $spreadSheet->getActiveSheet()->getCell('A'.$i)->getValue();
	$arr[] = $number;
	$i++;
}

var_dump($arr);
?>