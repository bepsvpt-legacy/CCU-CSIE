<?php	session_start();	require_once("../global_config/globalbannedlist.php");		if ($_SESSION['permission'] != 'access' or $_SERVER['HTTP_REFERER'] != 'http://www.cs.ccu.edu.tw/~cys102u/dormflows/downloads.php') {		unset($_SESSION['permission']);		$sql = "INSERT INTO `dormflows_downloadlog` (`ip_address`, `download_log`) VALUES ('".$ip."', 'Access Deny!')";		mysqli_query($mysqli_connecting, $sql);				$sql = "SELECT `id` FROM `dormflows_downloadlog` WHERE `ip_address` = '".$ip."' AND `download_log` = 'Access Deny!'";		$result = mysqli_query($mysqli_connecting, $sql);		$quantity = mysqli_num_rows($result);		if ($quantity >= 3) {			$sql = "INSERT INTO `global_bannedlist` (`ip_address`, `reason`, `time_unix`) VALUES ('".$ip."', '異常訪問', '".time()."')";			mysqli_query($mysqli_connecting, $sql);		}				header ("Location: http://www.cs.ccu.edu.tw/~cys102u/dormflows/downloads.php");		exit();	} else {		unset($_SESSION['permission']);		if(isset($_GET["file"])){			$filename = mysqli_real_escape_string($mysqli_connecting, $_GET["file"]);			$sql = "SELECT `file_path` FROM `file_download_path` WHERE `file_name` = '".$filename."'";			$result = mysqli_query($mysqli_connecting, $sql);			if($filepath = mysqli_fetch_array($result, MYSQLI_ASSOC)) {				$file = $filepath['file_path'];				chdir("../");				if(file_exists($file)){					header('Pragma: public');					header('Expires: 0');					header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');					header('Cache-Control: private', false);					header('Content-Transfer-Encoding: binary');					header('Content-Disposition: attachment; filename="'.$filename.'";');					header("content-type: application/octet-stream");					header("Content-Length: ".filesize($file));					readfile($file);					$sql = "INSERT INTO `dormflows_downloadlog` (`ip_address`, `download_log`) VALUES ('".$ip."', 'File : ".$filename."')";					mysqli_query($mysqli_connecting, $sql);				} else{					$sql = "INSERT INTO `dormflows_downloadlog` (`ip_address`, `download_log`) VALUES ('".$ip."', '檔案不存在 : ".$filename."')";					mysqli_query($mysqli_connecting, $sql);					header ("Location: http://www.cs.ccu.edu.tw/~cys102u/dormflows/downloads.php");					exit();				}			} else {				$sql = "INSERT INTO `dormflows_downloadlog` (`ip_address`, `download_log`) VALUES ('".$ip."', '未找到檔案 : ".$filename."')";				mysqli_query($mysqli_connecting, $sql);				header ("Location: http://www.cs.ccu.edu.tw/~cys102u/dormflows/downloads.php");				exit();			}		} else{			$sql = "INSERT INTO `dormflows_downloadlog` (`ip_address`, `download_log`) VALUES ('".$ip."', '未設置 file')";			mysqli_query($mysqli_connecting, $sql);			header ("Location: http://www.cs.ccu.edu.tw/~cys102u/dormflows/downloads.php");			exit();		}	}?>