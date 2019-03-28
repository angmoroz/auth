<?php
include('DataClass.php');
set_time_limit(0);

$dir = scandir('defs');

$defs = new DataClass();

$del_query = 'DROP TABLE IF EXISTS `defs`;';
$defs->insert_defs($del_query);
	
$del_query = 'CREATE TABLE `defs` (
					  `id` int(11) NOT NULL,
					  `operator` varchar(1024) NOT NULL,
					  `region` varchar(1024) NOT NULL,
					  `kod` varchar(20) NOT NULL,
					  `begin` varchar(20) NOT NULL,
					  `end` varchar(20) NOT NULL,
					  `ibegin` bigint(11) NOT NULL,
					  `iend` bigint(11) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
						
$defs->insert_defs($del_query);
$y = 0;

foreach($dir as $dr) {
	$i=0;
	if ($dr != '.' && $dr != '..') {
		
		$query = "INSERT INTO `defs`( `id`, `operator`, `region`, `kod`, `begin`, `end`, `ibegin`, `iend`) VALUES ";
		
		if (($handle = fopen('defs/'.$dr, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE ) {

				if ($i != 0) {
					
					$num = count($data);
					
					for ($c=0; $c < $num; $c++) {

						$ibegin = $data[0].$data[1];
						$iend = $data[0].$data[2];
						$operator = preg_replace("/'/", '\"',$data[4]);
						$region = preg_replace("/'/", '\"',$data[5]);
						
						$operator = mb_convert_encoding($operator, "UTF-8");
						$region = mb_convert_encoding($region , "UTF-8");
					}
					
					$query .="($y,'$operator','$region',$data[0],$data[1],$data[2],$ibegin,$iend) ,";
					$is_last_hundred = false;
				}
				$i++;
				
				if ($i == 1000) {
					
					$query = rtrim($query, ',');
					$defs->insert_defs(mb_convert_encoding($query, "UTF-8"));
					echo "\n --------query --------\n";
					$query = "INSERT INTO `defs`( `id`,`operator`,  `region`, `kod`, `begin`, `end`, `ibegin`, `iend`) VALUES ";
					
					$i = 0;
					$is_last_hundred = true;
					sleep(5);
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

foreach($dir as $dr) {
	if($dr != '.' && $dr != '..') {
		echo "<br> delete: $dr <br>";
		unlink($dr);
	}
}

?>