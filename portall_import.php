<?php

include('DataClass.php');
set_time_limit(0);

$inputFile = 'port_all/';

$defs = new DataClass();

//if($defs->unzip($inputFile)){
	if(1){
	
//	$defs->csv_parts($inputFile);

	$dir = scandir('/port_all/output');
	
	$del_query = '	DROP TABLE IF EXISTS `portsall`';
	
	$defs->insert_defs($del_query);
	
	$del_query = 'CREATE TABLE `portsall` (
					  `id` int(11) NOT NULL,
					  `Number` varchar(10) NOT NULL,
					  `OwnerId` varchar(50) NOT NULL,
					  `MNC` varchar(16) NOT NULL,
					  `Route` varchar(8) NOT NULL,
					  `RegionCode` varchar(16) NOT NULL,
					  `PortDate` varchar(50) NOT NULL,
					  `RowCount` varchar(8) NOT NULL,
					  `NPId` varchar(16) NOT NULL,
					  `DonorId` varchar(50) NOT NULL,
					  `RangeHolderId` varchar(50) NOT NULL,
					  `OldRoute` varchar(8) NOT NULL,
					  `OldMNC` varchar(16) NOT NULL,
					  `ProcessType` varchar(20) NOT NULL
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;
					';
	$defs->insert_defs($del_query);
	$y = 0;

	foreach($dir as $dr) {
		$i=0;
		if (preg_match("/\.csv/", $dr)) {
			
			var_dump($dr."\n");
			
			$query = "INSERT INTO `portsall`(`id`,`Number`, `OwnerId`, `MNC`, `Route`, `RegionCode`, `PortDate`, `RowCount`, `NPId`, `DonorId`, `RangeHolderId`, `OldRoute`, `OldMNC`, `ProcessType`) VALUES";
			
			if (($handle = fopen('port_all/output/'.$dr, "r")) !== FALSE) {

				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					
					if ($i != 0) {
						
						$num = count($data);
						
						/*for ($c=0; $c < $num; $c++) {
						}*/	
						
						$query .= "  ($y, $data[0],'$data[1]',$data[2],'$data[3]',$data[4],'$data[5]',$data[6],$data[7],'$data[8]','$data[9]','$data[10]','$data[11]','$data[12]'),";
						
						$is_last_hundred = false;
						
						$y++;
					}
					$i++;
					if ($i == 1000) {
						
						$query = rtrim($query, ',');
						$defs->insert_defs(mb_convert_encoding($query, "UTF-8"));
						echo "\n --------query --------\n";
						$query = "INSERT INTO `portsall`(`id`,`Number`,  `OwnerId`, `MNC`, `Route`, `RegionCode`, `PortDate`, `RowCount`, `NPId`, `DonorId`, `RangeHolderId`, `OldRoute`, `OldMNC`, `ProcessType`) VALUES";
						
						$i = 0;
						$is_last_hundred = true;
						sleep(3);
					}
				}
				
				if(!is_last_hundred) {
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

	$del_query = 'ALTER TABLE `portsall`
					  ADD PRIMARY KEY (`id`);';
	$defs->insert_defs($del_query);

	$del_query = 'ALTER TABLE `portsall`
					  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
	$defs->insert_defs($del_query);

	foreach($dir as $dr) {
		if($dr != '.' && $dr != '..') {
			echo 'port_all/output/'.$dr;
			unlink('port_all/output/'.$dr);
		}
	}
}
?>