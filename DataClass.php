<?php
class DataClass 
{
	public function ConnectBD() {
		$link = @mysqli_connect('localhost', 'root', '', 'ww');
		mysqli_set_charset($link,"utf8");

		if (!$link) {
			die('Connect Error: ' . mysqli_connect_errno());
		}
		return $link;
	}
	
	public function insert_defs($query) {
		$link = $this->ConnectBD();
		
		if (!$link->query($query)) {
			printf("<br>Сообщение ошибки: %s\n", $link->error."<br>");
			printf("<br>Сообщение ошибки query: %s\n", $query."<br>");
		} else {
			printf("<br>Сообщение inserted<br>");
		}
				 
		//var_dump($query);
	}
	
	public function unzip($direct) {
	
		$dir = scandir($direct);
		$flag = false;
		
		foreach($dir as $dr) {
			
			if($dr != '.' && $dr != '..') {
				$zip = new ZipArchive;
				
				if (preg_match('/\.zip/',$dr))
					if ($zip->open($direct.'/'.$dr) === TRUE) {
						
						$zip->extractTo($direct);
						$zip->close();
						echo 'ok';
						
					} else {
						
						echo 'failed';
					}
				$flag = true;
			} else {
				
				echo $dr;
			}
		}
		
		if(!$flag)
				echo "<br>Никакой файл не найден<br>";
		 return $flag;
	}
	public function csv_parts($path) {
	
		
		$outputFile = $path.'/output/';
		$dir = scandir($outputFile);
		$splitSize = 10000;
		
		foreach ($dir as $dr) {
			if (preg_match('/\.csv/', $dr)) {
				$file = $dr;
			}
		}
		$in = fopen($path."/".$file, 'r');

		$rowCount = 0;
		$fileCount = 1;
		while (!feof($in)) {
			
		 	if ($rowCount % 3 == 0) {
				sleep(1);
				echo "\n --------slept csvs-------- \n ";
			} else {
                    //	sleep(3);
			} 
			
			if (($rowCount % $splitSize) == 0) {
				if ($rowCount > 0) {
					fclose($out);
				}
				$out = fopen($outputFile . $fileCount++ . '.csv', 'w');
			}
			$data = fgetcsv($in);
			if ($data)
				fputcsv($out, $data);
			$rowCount++;
		}

		fclose($out);
		unlink($path .'/'. $file);
	}
}
?>