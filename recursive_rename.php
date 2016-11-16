<?php

//$dir = "V:\Video\Telesensation\Star Trek - Voyager";
//$dir = "H:\Install";
//$dir = "H:\Install\Games\Windows";
//$dir = "H:\Install\Programs\Windows";
$dir = "V:\Video\Telesensation\Bleach";
recursive_rename($dir);

function recursive_rename($dir) {
	if($handle = opendir($dir)) {
		//echo "Directory handle: $handle<br>\r\n";
		//echo "Files:<br>\r\n";
		$exists_a_dir = false;
		while(false !== ($file = readdir($handle))) {
			if($file === "." || $file === "..") {
				continue;
			}
			if(is_dir($dir . "\\" . $file)) {
				$exists_a_dir = true;
				break;
			}
		}
		while(false !== ($file = readdir($handle))) {
			//print($dir . "\\" . $file . "<br>\r\n");
			if($file === "." || $file === "..") {
				continue;
			}
			//print("is_dir .. ");var_dump(is_dir(".."));print("<br>\r\n");
			//print("is_dir Games ");var_dump(is_dir("Games"));print("<br>\r\n");
			//print("is_dir $file ");var_dump(is_dir($file));print("<br>\r\n");
			//print("is_dir $dir ");var_dump(is_dir($dir));print("<br>\r\n");
			//print("is_dir $dir\\$file ");var_dump(is_dir($dir . "\\" . $file));print("<br>\r\n");
			//var_dump($handle);exit(0);
			if(is_dir($dir . "\\" . $file)) {
				//print("is dir<br>\r\n");
		//		recursive_rename($dir . "\\" . $file);
			} else {
				if($exists_a_dir) {
					//print($dir . "\\" . $file . "<br>\r\n");
					$old = $dir . "\\" . $file;
					$new = $dir . "\\" . preg_replace('/(.{1,})\.\w{1,}/is', '\1', $file) . "\\" . $file;
					print($old . "||||" . $new . "<br>\r\n");
					if(!is_dir($dir . "\\" . preg_replace('/(.{1,})\.\w{1,}/is', '\1', $file))) {
						mkdir($dir . "\\" . preg_replace('/(.{1,})\.\w{1,}/is', '\1', $file));
					}
				//	rename($old, $new);
				}
			}

			//rename($old, $new);
			//print($old . "||||" . $new . "<br>\r\n");
		}

		closedir($handle);
	}

}

?>