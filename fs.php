<?php

class fs {

function __construct() {
	$this->file_counter = 0;
	$this->folder_counter = 0;
	$this->current_folder_array = array();
	$this->contents = "";
	$this->files = "";
	//$this->directories_counter = 0;
	//$this->space_threshold = 0.05;
	$this->space_threshold = 0.10;
}

function set_backup_directories() {
	$this->backup_directories = array(
	'C:/Backup',
	//'D:/Backup',
	//'E:/Backup', // USB
	'P:/Backup',
	//'S:/Backup',
	'V:/Backup',
	'W:/Backup',
	);
}

function set_important_directories() {
	$this->important_directories = array(
	'C:/wamp/www',
	//'C:/Games/Battle for Wesnoth 1.12.0 dev/userdata/data/add-ons',
	//'P:/Program Files/renpy-6.15.7-sdk/the_question2',
	//'P:/Games/Shadowrun Returns/Shadowrun_Data/StreamingAssets/ContentPacks/test',
	//'C:/Users/scott/AppData/Local/Eushully/神採りアルケミーマイスター/SAVE',
	);
}

function backup_important_files() {
	//print('Backing up important files...');exit(0);
	fs::set_backup_directories();
	fs::set_important_directories();
	
	/*print('<table>
<tr>
<th>Original</th>
<th>Copy</th>
</tr>');*/
	foreach($this->backup_directories as $index1 => $value1) {
		$notes = 'This was a full backup of:
';
		if(is_dir($value1 . '/' . date("Y-m-d"))) {
			print('Hmm, seems like a backup was already done today since ' . $value1 . '/' . date("Y-m-d") . ' already exists; stopping.');exit(0);
		}
		foreach($this->important_directories as $index2 => $value2) {
			fs::recursive_copy($value2, $value1 . '/' . date("Y-m-d") . '/' . str_replace(':/', '/', $value2));
			//exit(0);
			$notes .= $value2 . '
';
		}
		file_put_contents($value1 . '/' . date("Y-m-d") . '/notes.txt', $notes);
	}
	//print('</table>');
	print('Important files successfully backed up. <a href="do.php">Back to menu</a>');
}

function clean_backup_folders() {
	fs::set_backup_directories();
	foreach($this->backup_directories as $index1 => $value1) {
		fs::recurse_clean_backup_folders($value1);
	}
	print('Backup folders successfully cleaned. <a href="do.php">Back to menu</a>');
}

function differentially_backup_important_files() {
	//print('Differentially backing up important files...');exit(0);
	fs::set_backup_directories();
	fs::set_important_directories();
	
	/*print('<table>
<tr>
<th>Original</th>
<th>Copy</th>
</tr>');*/
	foreach($this->backup_directories as $index1 => $value1) {
		$notes = 'This was a differential backup of:
';
		if(is_dir($value1 . '/' . date("Y-m-d"))) {
			print('Hmm, seems like a backup was already done today since ' . $value1 . '/' . date("Y-m-d") . ' already exists; stopping.');exit(0);
		}
		$this->array_dates = array();
		$dir = opendir($value1);
		while(false !== ($entry = readdir($dir))) {
			if(($entry != '.') && ($entry != '..')) {
				//var_dump($value1 . '/' . $entry);
				if(is_dir($value1 . '/' . $entry)) {
					preg_match('/[0-9\-]+/is', $entry, $date_component_matches);
					//var_dump($date_component_matches);
					if(strlen($entry) === strlen($date_component_matches[0])) {
						$this->array_dates[] = $entry;
					}
				}
			}
		}
		closedir($dir);
		$this->array_dates = array_reverse($this->array_dates);
		//var_dump($this->array_dates);exit(0);
		foreach($this->important_directories as $index2 => $value2) {
			fs::differential_recursive_copy($value2, $value1 . '/{date}/' . str_replace(':/', '/', $value2));
			//exit(0);
			$notes .= $value2 . '
';
		}
		file_put_contents($value1 . '/' . date("Y-m-d") . '/notes.txt', $notes);
	}
	//print('</table>');
	print('Important files successfully differentially backed up. <a href="do.php">Back to menu</a>');
}

function recurse_clean_backup_folders($folder) {
    if(preg_match('/\-[0-9]{8}/is', $folder) || preg_match('/\-[0-9]{4}-[0-9]{2}-[0-9]{2}/is', $folder) || preg_match('/\- Copy/is', $folder)) {
		//chmod($folder, 0777);
		fs::recursiveChmod($folder);
		unlink($folder);
		print($folder . ' was deleted.<br>');
	} else {
		$dir = opendir($folder);
		while(false !== ($entry = readdir($dir))) {
			if(($entry != '.') && ($entry != '..')) {
				if(is_dir($folder . '/' . $entry)) {
					fs::recurse_clean_backup_folders($folder . '/' . $entry);
				}
			}
		}
		closedir($dir); 
	}
}

function analyze_drives_free_space_old() {
	$counter = 65;
	while($counter < 91) {
		$drive = chr($counter) . ':';
		if(is_dir($drive)) {
			print('Scanning drive ' . $drive . '<br>');
			//var_dump(disk_free_space($drive), disk_total_space($drive));
			$free_space = disk_free_space($drive);
			$total_space = disk_total_space($drive);
			print('Free space: ' . $free_space . ' bytes<br>');
			print('Total space: ' . $total_space . ' bytes<br>');
			if($free_space / $total_space < $this->space_threshold) {
				print('<span style="color: red;">This disk is low on space.</span><br>');
			}
			print('<br>');
		}
		$counter++;
	}
}

function test001() {
	//print('hi');exit(0);
	//fopen('C:\Windows\System32\notepad.exe', 'r');
	//shell_exec('C:\Windows\System32\notepad.exe'); // works in session 0
	//system('C:\Windows\System32\notepad.exe'); // works in session 0
	//exec('C:\Windows\System32\notepad.exe'); // works in session 0
	//exec('test002.bat'); // works in session 0
	//exec('C:\Windows\System32\SoundRecorder.exe'); // works in session 0
	//passthru('C:\Windows\System32\notepad.exe'); // works in session 0
	
	//exec('start /b C:\Windows\System32\notepad.exe'); // works in session 0
	
	//try {
	//$shell = new COM("WScript.Shell");
	//$shell->run('start /b C:\Windows\System32\notepad.exe', 0, false);
	//$command = 'C:\Windows\System32\notepad.exe';
	//$command = 'start /b C:\Windows\System32\notepad.exe';
	//$shell->run($command, 0, false);
	/*} catch (Exception $e) { 
	var_dump($e->getMessage(), $e->getCode()); 
	}
	*/
	//$shell->run('start /b C:\Windows\System32\notepad.exe', 0, false);
	//exec('start /b ' . $notepad);
	fs::recursive_cut('D:\Audio\Bands', 'S:\Audio\Bands');
}

function query_encode($string) {
	//var_dump(urlencode('&'));exit(0);
	$string = str_replace('&', '%26', $string);
	return $string;
}

function query_decode($string) {
	$string = str_replace('%26', '&', $string);
	return $string;
}

function navigate_files() {
	// notice that this function is expecting preceding foreslashes
	//$path = 'C:\Windows\System32\notepad.exe';
	if($_REQUEST["path"] == "") {
		$path = '';
	} else {
		$path = fs::query_decode($_REQUEST["path"]);
	}
	if($_REQUEST["fixed_drives"] == "") {
		$fso = new COM('Scripting.FileSystemObject'); 
		$D = $fso->Drives; 
		$type = array("Unknown", "Removable", "Fixed", "Network", "CD-ROM", "RAM Disk");
		$array_fixed_drives = array();
		foreach($D as $d){ 
			$dO = $fso->GetDrive($d);
			if($dO->DriveType == 2) {
				$array_fixed_drives[] = $dO->DriveLetter;
			}
		}
	} else {
		$fixed_drives_string = $_REQUEST["fixed_drives"];
		$array_fixed_drives = explode(',', $fixed_drives_string);
	}
	$array_entries = array();
	foreach($array_fixed_drives as $fixed_drive) {
		$compound_path = $fixed_drive . ':' . $path;
		if(is_dir($compound_path)) {
			$dir = opendir($compound_path);
			//var_dump($fixed_drive, $compound_path, $path, $dir);
			while(false !== ($entry = readdir($dir))) {
				if(($entry != '.') && ($entry != '..')) {
					$full_path = $compound_path . '/' . $entry;
					//var_dump($full_path);
					if(isset($array_entries[$entry])) {
						$array_full_paths = $array_entries[$entry];
						$array_full_paths[] = $full_path;
						//$count = $array_entries[$entry][0];
						//$count++;
						$array_entries[$entry] = $array_full_paths;
					} else {
						$array_entries[$entry] = array($full_path);
					}
					/*if(is_dir($full_path)) {
						print('<a href="do.php?action=navigate_files&path=' . substr($full_path, strpos($full_path, ':') + 1) . '&fixed_drives=' . implode(',', $array_fixed_drives) . '">' . $entry . '</a> (' . $full_path . ') (1)<br>');
					} else {
						print('<a href="do.php?action=open_file_bat_command&path=' . substr($full_path, strpos($full_path, ':') + 1) . '&fixed_drives=' . implode(',', $array_fixed_drives) . '">' . $entry . '</a> (' . $full_path . ') (2)<br>');
					}*/
				}
			}
			closedir($dir);
		}
	}
	ksort($array_entries);
	//var_dump($array_entries);
	foreach($array_entries as $entry => $entry_array) {
		$full_path = $entry_array[0];
		if(is_dir($full_path)) {
			print('<a href="do.php?action=navigate_files&path=' . fs::query_encode(substr($full_path, strpos($full_path, ':') + 1)) . '&fixed_drives=' . implode(',', $array_fixed_drives) . '">' . $entry . '</a><br>');
		}
	}
	foreach($array_entries as $entry => $entry_array) {
		$full_path = $entry_array[0];
		if(is_dir($full_path)) {
			
		} else {
			print('<a href="do.php?action=open_file&path=' . fs::query_encode($full_path) . '&fixed_drives=' . implode(',', $array_fixed_drives) . '">' . $entry . '</a><br>');
		}
	}
	/*$up_level_path = $full_path;
	$up_level_path = substr($up_level_path, strpos($up_level_path, ':') + 1);
	$up_level_path = substr($up_level_path, 0, fs::strpos_last($up_level_path, '/'));
	$up_level_path = substr($up_level_path, 0, fs::strpos_last($up_level_path, '/'));
	var_dump($full_path, $up_level_path);*/
	$up_level_path = $path;
	$up_level_path = substr($up_level_path, 0, fs::strpos_last($up_level_path, '/'));
	//var_dump($path, $up_level_path);
	print('<a href="do.php?action=navigate_files&path=' . fs::query_encode($up_level_path) . '&fixed_drives=' . implode(',', $array_fixed_drives) . '">Up one level</a> <a href="do.php">Back to menu</a> <a href="do.php?action=navigate_files_recursive_file_list&path=' . fs::query_encode($path) . '">Recursive directory list</a> <a href="do.php?action=navigate_files&path=&fixed_drives=' . implode(',', $array_fixed_drives) . '">Navigate Files</a>');
}

public function strpos_last($haystack, $needle) {
	//print('$haystack, $needle: ');var_dump($haystack, $needle);
	if(strlen($needle) === 0) {
		return false;
	}
	$len_haystack = strlen($haystack);
	$len_needle = strlen($needle);		
	$pos = strpos(strrev($haystack), strrev($needle));
	if($pos === false) {
		return false;
	}
	return $len_haystack - $pos - $len_needle;
}

function open_file() {
	//$contents = file_get_contents('fs.bat');
	if($_REQUEST["path"] == "") {
		print('Path not properly specified.<br>');
	} else {
		$path = fs::query_decode($_REQUEST["path"]);
		//file_put_contents('fs.bat', 'start "' . $path . '"');
		//function symlink($target, $link) {
        //if (! substr($link, -4, '.lnk'))
        /*$target = $path;
		$target = substr($target, 0, fs::strpos_last($target, '/'));
        $link = $target . '/fs.lnk';
        $shell = new COM('WScript.Shell');
        $shortcut = $shell->createshortcut($link);
		$shortcut->targetpath = $target;
        $shortcut->save();*/
		/*$target = $path;
		$link = 'fs_symlink.lnk';
		symlink($target, $link);

		echo readlink($link);*/
		//var_dump($path);
		fs::create_lnk_file($path);
		/*$target = $path; // This is the file that already exists
		$target = 'C:\Windows\System32\notepad.exe'; // This is the file that already exists
		$link = 'newfile.ext'; // This the filename that you want to link it to

		link($target, $link);*/
    //}
	}
	if($_REQUEST["fixed_drives"] == "") {
		$fixed_drives_string = '';
	} else {
		$fixed_drives_string = $_REQUEST["fixed_drives"];
	}
	$up_level_path = $path;
	$up_level_path = substr($up_level_path, strpos($up_level_path, ':') + 1);
	$up_level_path = substr($up_level_path, 0, fs::strpos_last($up_level_path, '/'));
	//print('fs.bat file altered<br>
	print('<a href="do.php?action=navigate_files&path=' . fs::query_encode($up_level_path) . '&fixed_drives=' . $fixed_drives_string . '">Up one level</a> <a href="do.php">Back to menu</a> <a href="do.php?action=navigate_files&path=&fixed_drives=' . $fixed_drives_string . '">Navigate Files</a><br>');
}

function browse() {
	if($_REQUEST["path"] == "") {
		print('Path not properly specified.<br>');
	} else {
		$path = fs::query_decode($_REQUEST["path"]);
		$last_path = fs::query_decode($_REQUEST["last_path"]);
		$contents = file_get_contents($path);
		//var_dump($path, $last_path, $contents);
		if(strlen($contents) == 0) {
			$path = $path . $last_path;
			$contents = file_get_contents($path);
		}
		if(substr_count($path, '/') < 3) {
			$site_root = $path;
		} else {
			//$site_root = substr($path, 0, fs::strpos_nth($path, '/', 3) + 1);
			$site_root = substr($path, 0, fs::strpos_nth($path, '/', 3)); // omit the last slash
		}
		//print('$site_root: ');var_dump($site_root);exit(0);
		preg_match_all('/<(link|script|img)([^<>]*?) (href|src)=("|\')([^"\']*?)\4([^<>]*?)>/is', $contents, $matches);
		//var_dump($matches);exit(0);
		foreach($matches[0] as $index => $value) {
			$path_in_code = $matches[5][$index];
			if($path_in_code[0] === '/') {
				$contents = str_replace($value, '<' . $matches[1][$index] . $matches[2][$index] . ' ' . $matches[3][$index] . '=' . $matches[4][$index] . $site_root . $path_in_code . $matches[4][$index] . $matches[6][$index] . '>', $contents);
			} elseif(strpos($path_in_code, '://') === false) {
				$contents = str_replace($value, '<' . $matches[1][$index] . $matches[2][$index] . ' ' . $matches[3][$index] . '=' . $matches[4][$index] . $path . $path_in_code . $matches[4][$index] . $matches[6][$index] . '>', $contents);
			} else {
				$contents = str_replace($value, '<' . $matches[1][$index] . $matches[2][$index] . ' ' . $matches[3][$index] . '=' . $matches[4][$index] . $path_in_code . $matches[4][$index] . $matches[6][$index] . '>', $contents);
			}
		}
		
		preg_match_all('/ style="([^"]*?)url\(\'([^\']*?)\'\)([^"]*?)"/is', $contents, $matches);
		//var_dump($matches);exit(0);
		foreach($matches[0] as $index => $value) {
			$path_in_code = $matches[2][$index];
			if($path_in_code[0] === '/') {
				$contents = str_replace($value, ' style="' . $matches[1][$index] . 'url(\'' . $site_root . $path_in_code . '\')' . $matches[3][$index] . '"', $contents);
			} elseif(strpos($path_in_code, '://') === false) {
				$contents = str_replace($value, ' style="' . $matches[1][$index] . 'url(\'' . $path . $path_in_code . '\')' . $matches[3][$index] . '"', $contents);
			} else {
				$contents = str_replace($value, ' style="' . $matches[1][$index] . 'url(\'' . $path_in_code . '\')' . $matches[3][$index] . '"', $contents);
			}
		}
		
		preg_match_all('/<(a|form)([^<>]*?) (href|action)=("|\')([^"\']*?)\4([^<>]*?)>/is', $contents, $matches);
		//var_dump($matches);exit(0);
		foreach($matches[0] as $index => $value) {
			$path_in_code = $matches[5][$index];
			if($path_in_code[0] === '/') {
				$contents = str_replace($value, '<' . $matches[1][$index] . $matches[2][$index] . ' ' . $matches[3][$index] . '=' . $matches[4][$index] . 'do.php?action=browse&path=' . $site_root . $path_in_code . '&last_path=' . $last_path . $matches[4][$index] . $matches[6][$index] . '>', $contents);
			} elseif(strpos($path_in_code, '://') === false) {
				$contents = str_replace($value, '<' . $matches[1][$index] . $matches[2][$index] . ' ' . $matches[3][$index] . '=' . $matches[4][$index] . 'do.php?action=browse&path=' . $path . $path_in_code . '&last_path=' . $last_path . $matches[4][$index] . $matches[6][$index] . '>', $contents);
			} else {
				$contents = str_replace($value, '<' . $matches[1][$index] . $matches[2][$index] . ' ' . $matches[3][$index] . '=' . $matches[4][$index] . 'do.php?action=browse&path=' . $path_in_code . '&last_path=' . $last_path . $matches[4][$index] . $matches[6][$index] . '>', $contents);
			}
		}
		
		/*$contents = preg_replace('/<link([^<>]*?) href="([^"]*?)"([^<>]*?)>/is', '<link$1 href="' . $path . '$2"$3>', $contents);
		$contents = preg_replace('/<script([^<>]*?) src="([^"]*?)"([^<>]*?)>/is', '<script$1 src="' . $path . '$2"$3>', $contents);
		$contents = preg_replace('/<form([^<>]*?) action="([^"]*?)"([^<>]*?)>/is', '<form$1 action="' . $path . '$2"$3>', $contents);
		$contents = preg_replace('/<a([^<>]*?) href="([^"]*?)"([^<>]*?)>/is', '<a$1 href="do.php?action=browse&path=' . $path . '&last_path=' . $last_path . '$2"$3>', $contents);
		$contents = preg_replace('/([^<]..|<[^s].|..[^p])an ([bcdfghlmnpqrstvxyz])/is', '$1a $2', $contents);*/
		
		$contents = str_replace('people', 'persons', $contents);
		$contents = str_replace('imo', 'in my opinion', $contents);
		$contents = str_replace('your a', 'you\'re a', $contents);
		$contents = str_replace('cum', 'ejaculating', $contents);
		print($contents);exit(0);
	}
	print('<a href="do.php?action=browse&path=' . fs::query_encode($path) . '">Up one level</a>');
}

function create_lnk_file($path) {
	//var_dump(chr(0x4C));exit(0);
	// 0x00 . 0x00 . 0x00 . 0x00 .
	/*
	$filename = substr($path, fs::strpos_last($up_level_path, '/') + 1);
	
	$file_length = chr(0xA0) . chr(0x86) . chr(0x00) . chr(0x00);
	$long_name = $filename;
	$short_name = strtoupper($long_name);
	$header = 
	chr(0x4C) . chr(0x00) . chr(0x00) . chr(0x00) .
	
	chr(0x01) . chr(0x04) . chr(0x02) . chr(0x00) .
	chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0xC0) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x00) . chr(0x00) . chr(0x00) . chr(0x46) .
	
	chr(0x3F) . chr(0x00) . chr(0x00) . chr(0x00) .
	
	chr(0x20) . chr(0x00) . chr(0x00) . chr(0x00) .
	
	chr(0xC0) . chr(0x0E) . chr(0x82) . chr(0xD5) .
	chr(0xC1) . chr(0x20) . chr(0xBE) . chr(0x01) .
	chr(0x00) . chr(0x08) . chr(0xBF) . chr(0x46) .
	chr(0xD5) . chr(0x20) . chr(0xBE) . chr(0x01) .
	chr(0x00) . chr(0x47) . chr(0xAA) . chr(0xEC) .
	chr(0xEC) . chr(0x15) . chr(0xBE) . chr(0x01) .
	
	$file_length .
	
	chr(0x05) . chr(0x00) . chr(0x00) . chr(0x00) .
	
	chr(0x01) . chr(0x00) . chr(0x00) . chr(0x00) .
	
	chr(0x46) . chr(0x06) . chr(0x00) . chr(0x00) .
	
	chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) .
	
	chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
	
	$item_ID_list = 
	
	chr(0x2A) . chr(0x00);
	
	$first_item = 
	
	chr(0x28) . chr(0x00) .
	
	chr(0x32) . chr(0x00) .
	
	$file_length .
	
	chr(0x76) . chr(0x25) . chr(0x71) . chr(0x3E) .
	
	chr(0x20) . chr(0x00) .
	
	$long_name .
	
	chr(0x00) . 
	
	$short_name .
	
	chr(0x00);
	
	$last_item = 
	
	chr(0x00) . chr(0x00);
	
	$file_location_info = 
	
	chr(0x74) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x1C) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x03) . chr(0x00) . chr(0x00) . chr(0x00) .
	
	chr(0x1C) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x34) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x40) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x5F) . chr(0x00) . chr(0x00) . chr(0x00);
	
	$local_volume_table = 
	
	chr(0x18) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x03) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0xD0) . chr(0x07) . chr(0x33) . chr(0x3A) .
	chr(0x10) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x44) . chr(0x52) . chr(0x49) . chr(0x56) . chr(0x45) . chr(0x20) . chr(0x43) . chr(0x00) .
	chr(0x43) . chr(0x3A) . chr(0x5C) . chr(0x57) . chr(0x49) . chr(0x4E) . chr(0x44) . chr(0x4F) . chr(0x57) . chr(0x53) . chr(0x5C) . chr(0x00);

	$network_volume_table = 
	
	chr(0x1F) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x02) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x14) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) .
	chr(0x00) . chr(0x00) . chr(0x02) . chr(0x00) .
	chr(0x5C) . chr(0x5C) . chr(0x4A) . chr(0x45) . chr(0x53) . chr(0x53) . chr(0x45) . chr(0x5C) . chr(0x57) . chr(0x44) . chr(0x00) .
	chr(0x44) . chr(0x65) . chr(0x73) . chr(0x6B) . chr(0x74) . chr(0x6F) . chr(0x70) . chr(0x5C) . chr(0x62) . chr(0x65) . chr(0x73) . chr(0x74) . chr(0x5F) . chr(0x37) . chr(0x37) . chr(0x33) . chr(0x2E) . chr(0x6D) . chr(0x69) . chr(0x64) . chr(0x00);
	
	$description_string = 
	
	chr(0x06) . chr(0x00) .
	'fs_lnk';
	
	$relative_path_string = '.\\' . $long_name;
	$relative_path_string_length = chr(strlen($relative_path_string));
	$relative_path = 
	$relative_path_string_length . chr(0x00) .
	$relative_path_string;
	
	$working_directory_string = substr($path, 0, fs::strpos_last($up_level_path, '/'));
	$working_directory_string_length = chr(strlen($working_directory_string));
	$working_directory = 
	$working_directory_string_length . chr(0x00) .
	$working_directory_string;
	
	$command_line_arguments = 
	
	chr(0x06) . chr(0x00) .
	chr(0x2F) . chr(0x63) . chr(0x6C) . chr(0x6F) . chr(0x73) . chr(0x65);
	
	$icon_file_string = 'C:\WINDOWS\Mplayer.exe';
	$icon_file_string_length = chr(strlen($icon_file_string));
	$icon_file = 
	$icon_file_string_length .
	$icon_file_string;
	
	$ending_stuff = 
	
	chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
	
	$lnk_content = 
	$header .
	$item_ID_list .
	$first_item .
	$last_item .
	$file_location_info .
	$local_volume_table .
	$description_string .
	$relative_path .
	$working_directory .
	$command_line_arguments .
	$icon_file .
	$ending_stuff;
	
	file_put_contents('C:\Users\scott\Desktop\fs_lnk.lnk', $lnk_content);*/
	
	/*$contents = file_get_contents('C:\Users\scott\Desktop\wesnoth.exe.lnk');
	//$contents = str_replace('P : \ G a m e s \ B a t t l e   f o r   W e s n o t h   1 . 1 0 . 6 ', 'P : \ G a m e s \ B a t t l e   f o r   W e s n o t h   1 . 6 . 5 ', $contents);
	$contents = str_replace('P:\Games\Battle for Wesnoth 1.10.6\wesnoth.exe', 'P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe', $contents);
	$contents = substr($contents, 0, strpos($contents, 'P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe') + strlen('P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe') + 2) . chr(ord(substr($contents, strpos($contents, 'P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe') + strlen('P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe') + 2, 1)) - 1) . substr($contents, strpos($contents, 'P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe') + strlen('P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe') + 3); // strlen of the exe is now one less
	//var_dump(strlen('P:\Games\Battle for Wesnoth 1.10.6\wesnoth.exe'), strlen('P:\Games\Battle for Wesnoth 1.6.5\wesnoth.exe'));
	file_put_contents('C:\Users\scott\Desktop\wesnoth.exe_altered.lnk', $contents);*/
	
	
	
	/*$target = $path;
	$target = substr($target, 0, fs::strpos_last($target, '/'));
	$link = $target . '/fs.lnk';
	$shell = new COM('WScript.Shell');
	$shortcut = $shell->createshortcut($link);
	$shortcut->targetpath = $target;
	$shortcut->save();
	
	
	
	$WshShell = New-Object -comObject WScript.Shell
	$Shortcut = $WshShell.CreateShortcut("$Home\Desktop\ColorPix.lnk")
	$Shortcut.TargetPath = "C:\Program Files (x86)\ColorPix\ColorPix.exe"
	$Shortcut.Save()

	you can create a powershell script save as set-shortcut.ps1 in your $pwd

	param ( [string]$SourceExe, [string]$DestinationPath )

	$WshShell = New-Object -comObject WScript.Shell
	$Shortcut = $WshShell.CreateShortcut($DestinationPath)
	$Shortcut.TargetPath = $SourceExe
	$Shortcut.Save()

	and call it like this

	Set-ShortCut "C:\Program Files (x86)\ColorPix\ColorPix.exe" "$Home\Desktop\ColorPix.lnk"*/
	
	/*$shell = new COM('WScript.Shell');
	$shortcut = $shell->createshortcut('C:\Users\scott\Desktop\test006.lnk');
	//print($shortcut);
	$shortcut->targetpath = 'P:/Games/Battle for Wesnoth 1.10.6/wesnoth.exe';
	$shortcut->workingdirectory = 'P:/Games/Battle for Wesnoth 1.10.6';
	$shortcut->save();*/
	
	// see http://msdn.microsoft.com/en-us/library/xk6kst2k%28v=vs.84%29.aspx
	/*
	set WshShell = WScript.CreateObject("WScript.Shell")
	strDesktop = WshShell.SpecialFolders("Desktop")
	set oShellLink = WshShell.CreateShortcut(strDesktop & "\Shortcut Script.lnk")
	oShellLink.TargetPath = WScript.ScriptFullName
	oShellLink.WindowStyle = 1
	oShellLink.Hotkey = "CTRL+SHIFT+F"
	oShellLink.IconLocation = "notepad.exe, 0"
	oShellLink.Description = "Shortcut Script"
	oShellLink.WorkingDirectory = strDesktop
	oShellLink.Save
	*/
	
	$shell = new COM('WScript.Shell');
	$shortcut = $shell->createshortcut('C:\Users\scott\Desktop\fs.lnk');
	$shortcut->targetpath = $path;
	$working_directory = substr($path, 0, fs::strpos_last($path, '/'));
	$shortcut->workingdirectory = $working_directory;
	//$shortcut->Hotkey = 'CTRL+SHIFT+F';
	$shortcut->save();
	print('C:\Users\scott\Desktop\fs.lnk updated.<br>');
}

function redistribute_files() {
	$fso = new COM('Scripting.FileSystemObject');
	$D = $fso->Drives; 
	$type = array("Unknown", "Removable", "Fixed", "Network", "CD-ROM", "RAM Disk");
	$array_drives_low_on_space = array();
	$array_drives_with_extra_space = array();
	foreach($D as $d){ 
		$dO = $fso->GetDrive($d);
		/*$s = ""; 
		if($dO->DriveType == 3) { 
			$n = $dO->Sharename; 
		} elseif($dO->IsReady) { 
			$n = $dO->VolumeName; 
		} else { 
			$n = "[Drive not ready]"; 
		}*/
		if($dO->DriveType == 2) {
			if($dO->FreeSpace / $dO->TotalSize < $this->space_threshold) {
				//$s = " - " . fs::file_size($dO->FreeSpace) . " free of: " . fs::file_size($dO->TotalSize) . ' <span style="color: red;">This disk is low on space.</span>'; 
				$space_that_needs_to_be_freed = $dO->TotalSize * $this->space_threshold - $dO->FreeSpace;
				$array_drives_low_on_space[$dO->DriveLetter] = $space_that_needs_to_be_freed;
			} else {
				//$s = " - " . fs::file_size($dO->FreeSpace) . " free of: " . fs::file_size($dO->TotalSize);
				$array_drives_with_extra_space[$dO->DriveLetter] = 0;
			}
		} elseif($dO->DriveType == 4 && $dO->IsReady) {
			//$s = " - " . fs::file_size($dO->FreeSpace) . " free of: " . fs::file_size($dO->TotalSize);
			$array_drives_with_extra_space[$dO->DriveLetter] = 0;
		}
		//echo "Drive " . $dO->DriveLetter . ": - " . $type[$dO->DriveType] . " - " . $n . $s . "<br>"; 
	}
	//print('$array_drives_low_on_space: ');var_dump($array_drives_low_on_space);
	//print('$array_drives_with_extra_space: ');var_dump($array_drives_with_extra_space);exit(0);
	$array_moves = array();
	foreach($array_drives_low_on_space as $drive => $space_that_needs_to_be_freed) {
		if($space_that_needs_to_be_freed > 0) {
			print('Looking for folders that could be moved on ' . $drive . ':<br>');
			$dir = opendir($drive . ':');
			$found_solution = false;
			while(false !== ($entry = readdir($dir))) {
				if(($entry != '.') && ($entry != '..') && ($entry != 'Backup')) {
					$path = $drive . ':/' . $entry;
					//var_dump($entry);
					//var_dump($drive . ':/' . $entry);
					if(is_dir($path)) {
						$fO = $fso->GetFolder($path);
						if($fO->size > $space_that_needs_to_be_freed) { // then we at least have to find another drive to accomodate this content
							//print('Moving ' . $entry . ' would fix the low space problem on drive ' . $drive . ':.<br>');
							foreach($array_drives_with_extra_space as $healthy_drive => $zero) {
								$hdO = $fso->GetDrive($healthy_drive);
								if(($hdO->FreeSpace - $fO->size) / $hdO->TotalSize > $this->space_threshold) {
									//print('Moving ' . $path . ' to ' . $healthy_drive . ': would fix the low space problem on drive ' . $drive . ':.<br>');
									$moving_string = 'Moving ';
									$potential_moves_array = array();
									$path_dir = opendir($path);
									while(false !== ($entry = readdir($path_dir))) {
										if(($entry != '.') && ($entry != '..')) {
											$full_path = $path . '/' . $entry;
											//var_dump($full_path);
											if(is_dir($full_path)) {
												$fO2 = $fso->GetFolder($full_path);
												$space_that_needs_to_be_freed -= $fO2->size;
												$moving_string .= $full_path . ' and<br>';
												$potential_moves_array[] = array($full_path, $drive, $healthy_drive);
												if($space_that_needs_to_be_freed <= 0) {
													$moving_string .= ' to ' . $healthy_drive . ': will fix the low space problem on drive ' . $drive . ':.<br>';
													foreach($potential_moves_array as $potential_move_array) {
														$array_moves[] = $potential_move_array;
													}
													print($moving_string);
													$found_solution = true;
													break 3;
												}
											}
										}
									}
									closedir($path_dir);
								}
							}
						}
					}
				}
			}
			if(!$found_solution) { // we might have to do complex replacements...
				print('Either there is not enough space on all drives to accomodate moving files from ' . $drive . ' to fix its being low on space problem or this program was not smart enough to find the solution.');exit(0);
			}
			closedir($dir);
		}
	}
	foreach($array_moves as $index => $move_array) {
		
		//var_dump($move_array);
		$src = $move_array[0];
		$dest = $move_array[2] . substr($src, 1);
		fs::recursive_cut($src, $dest);
		rmdir($src);
		//exit(0);
		
		// be careful with this (copying and deleting large numbers of files)
		// also stuff like japanese characters and read-only files are still not handled
	}
	print('Files successfully redistributed.<br>');
	print('<a href="do.php">Back to menu</a>');
}

function analyze_drives() {
	$fso = new COM('Scripting.FileSystemObject'); 
	$D = $fso->Drives; 
	$type = array("Unknown", "Removable", "Fixed", "Network", "CD-ROM", "RAM Disk"); 
	foreach($D as $d){ 
		$dO = $fso->GetDrive($d); 
		$s = ""; 
		if($dO->DriveType == 3) { 
			$n = $dO->Sharename; 
		} elseif($dO->IsReady) { 
			$n = $dO->VolumeName; 
		} else { 
			$n = "[Drive not ready]"; 
		}
		if($dO->DriveType == 2) {
			if($dO->FreeSpace / $dO->TotalSize < $this->space_threshold) {
				$s = " - " . fs::file_size($dO->FreeSpace) . " free of: " . fs::file_size($dO->TotalSize) . ' <span style="color: red;">This disk is low on space.</span>'; 
			} else {
				$s = " - " . fs::file_size($dO->FreeSpace) . " free of: " . fs::file_size($dO->TotalSize);
			}
		} elseif($dO->DriveType == 4 && $dO->IsReady) {
			$s = " - " . fs::file_size($dO->FreeSpace) . " free of: " . fs::file_size($dO->TotalSize);
		}
		echo "Drive " . $dO->DriveLetter . ": - " . $type[$dO->DriveType] . " - " . $n . $s . "<br>"; 
	}
	/*$dir = 'S:\Video\Telesensation';
	$handle = opendir($dir);
	while(false !== ($entry = readdir($handle))) {
		if($entry === '.' || $entry === '.') {
			
		} else {
			$full_path = $dir . '\\' . $entry;
			var_dump($full_path);print('<br>');
			if(filesize($full_path) === 802867959) {
				print('Found one!');exit(0);
			}
		}
	}
	closedir($handle);
	fs::recursive_file_list_to_array($dir);
	//var_dump($this->files);
	foreach($this->files as $full_path) {
		if(filesize($full_path) > 770000000 && filesize($full_path) < 830000000) {
			//print('Found one!');exit(0);
			print($full_path . '<br>');
		}
	}
	// find movies without a year indicated
	// find duplicated files (notice that this would require an array with millions of entries unless skillfully handled)
	*/
	print('<a href="do.php">Back to menu</a>');
}

function file_size($size) { 
	$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB"); 
	return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes'; 
} 

function recursiveChmod($path, $filePerm=0644, $dirPerm=0755) {
	// Check if the path exists
	if(!file_exists($path)) {
		return(false);
	}
	// See whether this is a file
	if(is_file($path)) {
		// Chmod the file with our given filepermissions
		chmod($path, $filePerm);
	} elseif(is_dir($path)) { // If this is a directory...
		// Then get an array of the contents
		$foldersAndFiles = scandir($path);
		// Remove "." and ".." from the list
		$entries = array_slice($foldersAndFiles, 2);
		// Parse every result...
		foreach($entries as $entry) {
			// And call this function again recursively, with the same permissions
			if(is_dir($path . DIRECTORY_SEPARATOR . $entry)) {
				fs::recursiveChmod($path . DIRECTORY_SEPARATOR . $entry, $filePerm, $dirPerm);
			} else {
				chmod($path . DIRECTORY_SEPARATOR . $entry, $dirPerm);
			}
		}
		// When we are done with the contents of the directory, we chmod the directory itself
		chmod($path, $dirPerm);
	}
	// Everything seemed to work out well, return true
	return(true);
}

function recursive_cut($src, $dst) {
    $dir = opendir($src);
	if(!is_dir($dst)) {
		mkdir($dst, 0, true);
	}
    while(false !== ($entry = readdir($dir))) {
		if(($entry != '.') && ($entry != '..')) {
            if(is_dir($src . '/' . $entry)) {
                fs::recursive_cut($src . '/' . $entry, $dst . '/' . $entry);
				rmdir($src . '/' . $entry);
            } else {
				copy($src . '/' . $entry, $dst . '/' . $entry);
				unlink($src . '/' . $entry);
            }
			//chmod($src . '/' . $entry, 0777);
			//chown($src . '/' . $entry, 666);
        }
    }
    closedir($dir); 
}

function recursive_copy($src, $dst) {
    $dir = opendir($src);
    //@mkdir($dst);
	if(!is_dir($dst)) {
		mkdir($dst, 0, true);
	}
    while(false !== ($entry = readdir($dir))) {
        /*if($this->file_counter === 100) {
			exit(0);
		}*/
		if(($entry != '.') && ($entry != '..')) {
            if(is_dir($src . '/' . $entry)) {
                fs::recursive_copy($src . '/' . $entry, $dst . '/' . $entry);
            } else {
                //print("copy($src . '/' . $entry, $dst . '/' . $entry)<br>");
				/*print('<tr>
<td>' . $src . '/' . $entry . '</td>
<td>' . $dst . '/' . $entry . '</td>
</tr>
');*/ // careful using this since it will overload firefox pretty quickly as the number of files increases
				copy($src . '/' . $entry, $dst . '/' . $entry);
				//$this->file_counter++;
				//exit(0);
            }
        }
    }
    closedir($dir); 
}

function differential_recursive_copy($src, $dst) {
    $dir = opendir($src);
    //@mkdir($dst);
	if(!is_dir(str_replace('{date}', date("Y-m-d"), $dst))) {
		mkdir(str_replace('{date}', date("Y-m-d"), $dst), 0, true);
	}
    while(false !== ($entry = readdir($dir))) {
		if(($entry != '.') && ($entry != '..')) {
            if(is_dir($src . '/' . $entry)) {
                fs::differential_recursive_copy($src . '/' . $entry, $dst . '/' . $entry);
            } else {
				//print('File: ' . $src . '/' . $entry . '<br>');
				$file_has_a_backup = false;
				foreach($this->array_dates as $index => $value) {
					if(file_exists(str_replace('{date}', $value, $dst) . '/' . $entry)) { // found a previously backed up version
						//print('found a previously backed up version: ' . str_replace('{date}', $value, $dst) . '/' . $entry . '<br>');
						//var_dump(filemtime($src . '/' . $entry), filemtime(str_replace('{date}', $value, $dst) . '/' . $entry), filesize($src . '/' . $entry), filesize(str_replace('{date}', $value, $dst) . '/' . $entry));
						if(filemtime(str_replace('{date}', $value, $dst) . '/' . $entry) >= filemtime($src . '/' . $entry) && filesize($src . '/' . $entry) === filesize(str_replace('{date}', $value, $dst) . '/' . $entry)) { // no need to back it up
						
						} else {
							copy($src . '/' . $entry, str_replace('{date}', date("Y-m-d"), $dst) . '/' . $entry);
						}
						$file_has_a_backup = true;
						break;
					}
				}
				if(!$file_has_a_backup) {
					copy($src . '/' . $entry, str_replace('{date}', date("Y-m-d"), $dst) . '/' . $entry);
				}
				//exit(0);
            }
        }
    }
    closedir($dir); 
}

function smartCopy($source, $dest, $options = array('folderPermission' => 0755, 'filePermission' => 0755)) {
	$result = false;
	if(is_file($source)) {
		if($dest[strlen($dest) - 1] == '/') {
			if(!file_exists($dest)) {
				cmfcDirectory::makeAll($dest, $options['folderPermission'], true);
			}
			$__dest = $dest . "/" . basename($source);
		} else {
			$__dest = $dest;
		}
		$result = copy($source, $__dest);
		chmod($__dest, $options['filePermission']);
	} elseif(is_dir($source)) {
		if($dest[strlen($dest) - 1] == '/') {
			if($source[strlen($source) - 1] == '/') {
				// Copy only contents
			} else {
				// Change parent itself and its contents
				$dest = $dest.basename($source);
				@mkdir($dest);
				chmod($dest, $options['filePermission']);
			}
		} else {
			if($source[strlen($source) - 1] == '/') {
				//Copy parent directory with new name and all its content
				@mkdir($dest, $options['folderPermission']);
				chmod($dest, $options['filePermission']);
			} else {
				//Copy parent directory with new name and all its content
				@mkdir($dest, $options['folderPermission']);
				chmod($dest, $options['filePermission']);
			}
		}
		$dirHandle = opendir($source);
		while($file = readdir($dirHandle)) {
			if($file!="." && $file!="..") {
				if(!is_dir($source . "/" . $file)) {
					$__dest = $dest . "/" . $file;
				} else {
					$__dest = $dest . "/" . $file;
				}
				echo "$source/$file ||| $__dest<br />";
				$result = fs::smartCopy($source . "/" . $file, $__dest, $options);
			}
		}
		closedir($dirHandle);
	} else {
		$result=false;
	}
	return $result;
} 

function navigate_files_recursive_file_list() {
	if($_REQUEST["path"] == "") {
		$path = '';
	} else {
		$path = fs::query_decode($_REQUEST["path"]);
	}
	fs::recursive_file_list($path);
}

function recursive_file_list($directory) {
	/*if($this->file_counter > 100) {
		print('recursive_file_list stopped since more than 100 files were listed.');
		var_dump($this->file_counter);
		exit(0);
	}*/
	if(is_dir($directory)) {
		$d = dir($directory);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $directory . '/' . $entry;
			if(is_dir($Entry)) {
				//print("folder: " . $Entry . "\r\n<br>");
				$this->folder_counter++;
				fs::recursive_file_list($Entry);
				continue;
			} else {
				print($Entry . "\r\n<br>");
				$this->file_counter++;
			}
		}
		$d->close();
	}
}

function recursive_file_list_to_array($directory) {
	if(is_dir($directory)) {
		$d = dir($directory);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $directory . '/' . $entry;
			if(is_dir($Entry)) {
				$this->folder_counter++;
				fs::recursive_file_list_to_array($Entry);
				continue;
			} else {
				//print($Entry . "\r\n<br>");
				$this->files[] = $Entry;
				$this->file_counter++;
			}
		}
		$d->close();
	}
}

function recursive_directory_list($directory) {
	if($this->file_counter > 100) {
		print('recursive_directory_list stopped since more than 100 files were listed.');
		var_dump($this->file_counter);
		exit(0);
	}
	if(is_dir($directory)) {
		$d = dir($directory);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $directory . '/' . $entry;
			if(is_dir($Entry)) {
				print("folder: " . $Entry . "\r\n<br>");
				$this->folder_counter++;
				fs::recursive_directory_list($Entry);
				continue;
			} else {
				print("file: " . $Entry . "\r\n<br>");
				$this->file_counter++;
			}
		}
		$d->close();
	}
}

function smart_array_merge($array1, $array2) {
	foreach($array2 as $value) {
		$array1[] = $value;
	}
	return $array1;
}

function transit_intensity_map_from_file() {
	$contents = file_get_contents("counted_folders.txt");
	$counted_folders_array = explode("\r\n", $contents);
	$counted_folders_array2 = array();
	$biggest_intensity = 0;
	foreach($counted_folders_array as $counted_folder) {
		$counted_folder2 = explode("\t", $counted_folder);
		if($counted_folder2[1] > $biggest_intensity) {
			$biggest_intensity = $counted_folder2[1];
		}
		$counted_folders_array2[] = $counted_folder2;
	}
	print("<p>");
	foreach($counted_folders_array2 as $counted_folder3) {
		$op1 = 50 + 450 * (bcdiv(log($counted_folder3[1], 10), log($biggest_intensity, 10), 3));
		print('<span style="font-size: ' . $op1 . '%;">' . $counted_folder3[0] . '</span> ');
	}
	print("</p>");
}

function get_counted_permutations_of_folder_relations_from_file() {
	$contents = file_get_contents("permutations.txt");
	$array_permutations = explode("\r\n", $contents);
	$counted_array_permutations = fs::counted_from_redundant_array($array_permutations);
	$counted_folders_permutations_contents = "";
	foreach($counted_array_permutations as $permutation => $count) {
		$counted_folders_permutations_contents .= $permutation . "\t" . $count . "\r\n";
	}
	file_put_contents("counted_permutations.txt", $counted_folders_permutations_contents);
}

function get_permutations_of_folder_relations_from_file_iteratively() {
	while(true) {
		if(fs::get_permutations_of_folder_relations_from_file()) {
		
		} else {
			break;
		}
	}
}

function get_permutations_of_folder_relations_from_file() {
	$contents = file_get_contents("directories.txt");
	$contents_array = explode("\r\n", $contents);
	$initial_directories_counter = $directories_counter = file_get_contents("directories_counter.txt");
	$array_permutations = array();
	$return = true;
	while($directories_counter - $initial_directories_counter < 1000) {
		if(!isset($contents_array[$directories_counter])) {
			$return = false;
			break;
		}
		$permutations = fs::get_permutations_of_folder_relations(explode("/", $contents_array[$directories_counter]));
		//$array_permutations = array_merge($array_permutations, $permutations);
		$array_permutations = fs::smart_array_merge($array_permutations, $permutations);
		$directories_counter++;
	}
	//$this->directories_counter = $directories_counter;
	file_put_contents("directories_counter.txt", $directories_counter);
	$permutations_contents = file_get_contents("permutations.txt");
	foreach($array_permutations as $permutation) {
		$permutations_contents .= implode("/", $permutation) . "\r\n";
	}
	file_put_contents("permutations.txt", $permutations_contents);
	return $return;
}

function get_permutations_of_folder_relations($folders) {
	$permutations = array();
	$index3 = 0;
	//print("Message0010<br>\r\n");
	while($index3 < sizeof($folders)) {
		//print("Message0011<br>\r\n");
		foreach($folders as $index => $folder) {
			//print("Message0012<br>\r\n");
			if($index3 + $index >= sizeof($folders)) {
				break;
			}
			$index2 = $index;
			//$index2 = $index3 + $index;
			$mid_step = array();
			while($index2 < sizeof($folders)) {
				if($index2 + $index3 >= sizeof($folders)) {
					break;
				}
				//print("Message0013<br>\r\n");
				$mid_step[] = $folders[$index2];
				$index2++;
			}
			$permutations[] = $mid_step;
		}
		$index3++;
	}
	return $permutations;
}

function counted_from_redundant_array($array) {
	//print("message1<br>\r\n");
	$counted_array = array();
	foreach($array as $index => $value) {
		//print("message2<br>\r\n");
		//foreach($counted_array as $index2 => $value2) {
		//	//print("message3<br>\r\n");
		//	if($value === $value2[0]) {
		//		$counted_array[$index2][1]++;
		//		continue 2;
		//	}
		//}
		if(isset($counted_array[$value])) {
			$counted_array[$value]++;
		} else {
			$counted_array[$value] = 1;
		}
	}
	return $counted_array;
}

function create_folders_array($directory) {
	$this->contents = file_get_contents("directories.txt");
	$array_directories = explode("\r\n", $this->contents);
	$array_folders = array();
//	$multi_array_folders = array();
	$array_permutations = array();
	foreach($array_directories as $index => $directory) {
//		$part_working_on = &$multi_array_folders;
		$folders = explode("/", $directory);
		//foreach($folders as $folder) {
		//	$array_folders[] = $folder;
//			if(isset($part_working_on[$folder])) {
//			
//			} else {
//				$part_working_on[$folder] = array();
//			}
//			$part_working_on = &$part_working_on[$folder];
		//}
		$permutations = fs::get_permutations_of_folder_relations($folders);
		$array_permutations = array_merge($array_permutations, $permutations);
	}
	//$counted_array_folders = fs::counted_from_redundant_array($array_folders);
//	$counted_array_permutations = fs::counted_from_redundant_array($array_permutations);
	//$array_folders = array_unique($array_folders);
//	$array_permutations = array_unique($array_permutations);
	//print("array_folders: ");var_dump($array_folders);print("<br>\r\n");
//	print("multi_array_folders: ");var_dump($multi_array_folders);print("<br>\r\n");
//	print("array_permutations: ");var_dump($array_permutations);print("<br>\r\n");
//	print("counted_array_folders: ");var_dump($counted_array_folders);print("<br>\r\n");
//	print("counted_array_permutations: ");var_dump($counted_array_permutations);print("<br>\r\n");
	//$folders_contents = "";
	//foreach($array_folders as $folder) {
	//	$folders_contents .= $folder . "\r\n";
	//}
	//file_put_contents("folders.txt", $folders_contents);
	$permutations_contents = "";
	foreach($array_permutations as $permutation) {
		$permutations_contents .= implode("/", $permutation) . "\r\n";
	}
	file_put_contents("permutations.txt", $permutations_contents);
	//$counted_folders_contents = "";
	//foreach($counted_array_folders as $folder => $count) {
	//	$counted_folders_contents .= $folder . "\t" . $count . "\r\n";
	//}
	//file_put_contents("counted_folders.txt", $counted_folders_contents);
//	$counted_folders_permutations_contents = "";
//	foreach($counted_array_permutations as $permutation => $count) {
//		$counted_folders_permutations_contents .= $permutation . "\t" . $count . "\r\n";
//	}
//	file_put_contents("counted_permutations.txt", $counted_folders_permutations_contents);
}

function write_folder_paths_to_file($directory) {
	$this->list_folder_paths($directory);
	file_put_contents("directories.txt", $this->contents);
}

function list_folder_paths($directory) {
	if(is_dir($directory)) {
		if(!dir($directory)) {
			print('<h1 style="color: red;">Error 0001</h1>');
			return;
		}
		$d = dir($directory);
		//if(!$d->read()) {
		//	print('<h1 style="color: red;">Error 0002</h1>');
		//}
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $directory . '/' . $entry;
			if(is_dir($Entry)) {
				$this->current_folder_array[] = $entry;
				fs::list_folder_paths($Entry);
				continue;
			}
		}
		$this->current_folder_array_less_last_folder = array();
		$folder_path = $directory . "/";
		foreach($this->current_folder_array as $index => $folder) {
			$folder_path .= $folder . "/";
			if($index < sizeof($this->current_folder_array) - 1) {
				$this->current_folder_array_less_last_folder[] = $folder;
			}
		}
		$this->current_folder_array = $this->current_folder_array_less_last_folder;
		//print("folder: " . $directory . "\r\n<br>");
		$this->contents .= $directory . "\r\n";
		$this->folder_counter++;
		$d->close();
	}
}

function strpos_nth($haystack, $needle, $n) {
	$counter = 0;
	//$substr = $haystack;
	while($counter < $n) {
		$strpos = strpos($haystack, $needle, $strpos + 1);
		//$substr = substr($haystack, $strpos + 1);
		$counter++;
	}
	return $strpos;
}

}

?>