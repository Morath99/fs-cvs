<?php

//$dir = "V:\Video\Telesensation\Star Trek - Voyager";
//$dir = "H:\Install";
//$dir = "H:\Install\Games\Windows";
//$dir = "H:\Install\Programs\Windows";
$dir = "V:\Video\Telesensation\Bleach";
//$rename_array = array();
recursive_rename($dir);
//print('$rename_array :');var_dump($rename_array);

function recursive_rename($dir) {
	if($handle = opendir($dir)) {
		//echo "Directory handle: $handle<br>\r\n";
		//echo "Files:<br>\r\n";
		/*$exists_a_dir = false;
		while(false !== ($file = readdir($handle))) {
			if($file === "." || $file === "..") {
				continue;
			}
			if(is_dir($dir . "\\" . $file)) {
				$exists_a_dir = true;
				break;
			}
		}*/
		$file = true;
		while($file) {
			$file = readdir($handle);
			if($file === "." || $file === "..") {
				continue;
			}
			//print($dir . "\\" . $file . "<br>\r\n");
			//print('$file :');var_dump($file);
			if($file === "." || $file === "..") {
				continue;
			}
			preg_match('/bleach/is', $file, $name_matches);
			if(sizeof($name_matches[0]) !== 1) {
				print('Bah, I don\'t know how to handle this file: ' . $file . '<br>');
				//print('$name_matches :');var_dump($name_matches);
			} else {
				preg_match('/[0-9\-]{3,}/is', $file, $number_matches);
				if(sizeof($number_matches[0]) !== 1) {
					print('Bah, not sure how to number this file: ' . $file . '<br>');
				} else {
					$old = $dir . DIRECTORY_SEPARATOR . $file;
					$new = $dir . DIRECTORY_SEPARATOR . 'Bleach ' . $number_matches[0] . '.zip';
					print($old . "||||" . $new . "<br>\r\n");
					if($old === $new) { // do nothing
						
					} else {
						if(strpos($old, ".zip") !== false) { // it's already a .zip file; just rename it
							rename($old, $new);
							print('.zip file renamed<br>');
						} else { // create a .zip archive
							$zip = new ZipArchive;
							// kinda dangerous... (if we have a unzipped and zipped file for the same episode
							if(file_exists($new)) {
								if($zip->open($new, ZipArchive::OVERWRITE) === true) {
									$zip->addFile($old, $file);
									$zip->close;
									//unlink($old);
									print('.zip file overwritten<br>');
								} else {
									print('Could not create .zip archive; bleh!');exit(0);
								}
							} else {
								if($zip->open($new, ZipArchive::CREATE) === true) {
									$zip->addFile($old, $file);
									$zip->close;
									//unlink($old);
									print('.zip file created<br>');
								} else {
									print('Could not create .zip archive 2; bleh!');exit(0);
								}
							}
						}
					}
					//$rename_array[$number_matches[0]] = array($file, 'Bleach ' . $number_matches[0] . '.zip');
				}
			}
			//print("is_dir .. ");var_dump(is_dir(".."));print("<br>\r\n");
			//print("is_dir Games ");var_dump(is_dir("Games"));print("<br>\r\n");
			//print("is_dir $file ");var_dump(is_dir($file));print("<br>\r\n");
			//print("is_dir $dir ");var_dump(is_dir($dir));print("<br>\r\n");
			//print("is_dir $dir\\$file ");var_dump(is_dir($dir . "\\" . $file));print("<br>\r\n");
			//var_dump($handle);exit(0);
		//	if(is_dir($dir . "\\" . $file)) {
				//print("is dir<br>\r\n");
		//		recursive_rename($dir . "\\" . $file);
		//	} else {
		//		if($exists_a_dir) {
					//print($dir . "\\" . $file . "<br>\r\n");
					//$old = $dir . "\\" . $file;
					//$new = $dir . "\\" . preg_replace('/(.{1,})\.\w{1,}/is', '\1', $file) . "\\" . $file;
					//print($old . "||||" . $new . "<br>\r\n");
					//if(!is_dir($dir . "\\" . preg_replace('/(.{1,})\.\w{1,}/is', '\1', $file))) {
					//	mkdir($dir . "\\" . preg_replace('/(.{1,})\.\w{1,}/is', '\1', $file));
					//}
				//	rename($old, $new);
		//		}
		//	}

			//rename($old, $new);
			//print($old . "||||" . $new . "<br>\r\n");
		}

		closedir($handle);
	}
	//return $rename_array;
}

?>