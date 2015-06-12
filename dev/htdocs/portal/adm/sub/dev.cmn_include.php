<?php
session_start();

var_dump($_SESSION);

if(!isset($_SESSION['adm_user_id'])){
	header("location:login.php");
}

//ユーザーチェック
$db->connectDB();
$bind_param	= $db->initBindParam();
$sql		= "SELECT ID FROM adm_user WHERE id = ? LIMIT 1;";
$db->setSql_str($sql);
$bind_param	= $db->addBind($bind_param, "s", $_SESSION['adm_user_id']);
$result		= $db->exeQuery($bind_param);
$count		= $db->getRows($result);
if($count == 0) {
	header("location:login.php");
} else {
	$db->closeStmt($result);
}

function clearNewsFromSession() {
	if(isset($_SESSION['kubun'])){
		unset($_SESSION['kubun']);
	}
	if(isset($_SESSION['date_yy'])){
		unset($_SESSION['date_yy']);
	}
	if(isset($_SESSION['date_mm'])){
		unset($_SESSION['date_mm']);
	}
	if(isset($_SESSION['date_dd'])){
		unset($_SESSION['date_dd']);
	}
	if(isset($_SESSION['open_date_yy'])){
		unset($_SESSION['open_date_yy']);
	}
	if(isset($_SESSION['open_date_mm'])){
		unset($_SESSION['open_date_mm']);
	}
	if(isset($_SESSION['open_date_dd'])){
		unset($_SESSION['open_date_dd']);
	}
	if(isset($_SESSION['open_date_hh'])){
		unset($_SESSION['open_date_hh']);
	}
	if(isset($_SESSION['open_date_ii'])){
		unset($_SESSION['open_date_ii']);
	}
	if(isset($_SESSION['open_date_ss'])){
		unset($_SESSION['open_date_ss']);
	}
	if(isset($_SESSION['title'])){
		unset($_SESSION['title']);
	}
	if(isset($_SESSION['news'])){
		unset($_SESSION['news']);
	}
	if(isset($_SESSION['sr'])){
		unset($_SESSION['sr']);
	}
	return;
}
