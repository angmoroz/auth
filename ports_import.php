<?php
include('DataClass.php');
set_time_limit(0);

$dir = scandir('operator');

$defs = new DataClass();
if($defs->unzip('operator')) {

	$del_query = '	DROP TABLE IF EXISTS `operator`;';
	$defs->insert_defs($del_query);
	
	$del_query = 'CREATE TABLE `operator` (
					  `id` int(11) NOT NULL,
					  `orgcode` varchar(50) NOT NULL,
					  `mnc` varchar(6) NOT NULL,
					  `tin` varchar(100) NOT NULL,
					  `orgname` varchar(1024) NOT NULL,
					  `rowcount` varchar(12) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
					';
	$defs->insert_defs($del_query);

	$dir = scandir('operator');
	$y = 0;
	foreach($dir as $dr) {
		$i=0;
		if (preg_match("/\.csv/", $dr)) {
			
			var_dump($dr."\n");
			$query = "INSERT INTO `operator`(`id`,`orgcode`, `mnc`, `tin`, `orgname`, `rowcount`)  VALUES";
			
			if (($handle = fopen('operator/'.$dr, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					
					if ($i != 0) {
						
						$num = count($data);
						
						for ($c=0; $c < $num; $c++) {
							
							if (!isset($data[$c]) || $data[$c] == '' || $data[$c] == ' ' || $data[$c] == null) {
								$data[$c] = "''";
							}
							if(!is_numeric($data[2])) {
								$data[2] = "''";
							} 
							if(!is_numeric($data[4])) {
								$data[4] = "''";
							}
						}
						
						$query .= " ($y, '$data[0]',$data[1],$data[2],'$data[3]',$data[4]),";
						$is_last_hundred = false;
					}
					$i++;
					
					if ($i == 1000) {
						
						$query = rtrim($query, ',');
						$defs->insert_defs(mb_convert_encoding($query, "UTF-8"));
						echo "\n --------query --------\n";
						$query = "INSERT INTO `operator`(`id`, `orgcode`,`mnc`, `tin`, `orgname`, `rowcount`)  VALUES";
						
						$i = 0;
						$is_last_hundred = true;
					}
					$y++;
				}
				
				if(!$is_last_hundred) {
					$query = rtrim($query, ',');
					$defs->insert_defs(mb_convert_encoding($query, "UTF-8"));
					echo "\n --------query last --------\n";
				}
				
				fclose($handle);
				
			} else {
				echo 'No defs files or failed';
			}
		}
	}


	$del_query = 'ALTER TABLE `operator`
					  ADD PRIMARY KEY (`id`);';
	$defs->insert_defs($del_query);				  
	
	$del_query = 'ALTER TABLE `operator`
					  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127; ';
					  
					  
	$defs->insert_defs($del_query);

	$dir = scandir('operator');
	foreach($dir as $dr) {
		if($dr != '.' && $dr != '..') {
			echo "operator/".$dr;
			unlink("operator/".$dr);
		}
	}
}

?>