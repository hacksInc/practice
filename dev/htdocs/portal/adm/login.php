<?php
	require_once('./sub/conf.php');
	require_once('../class/db_adm.php');
	
	$db = new db();
	
	ob_start();
	
	session_start();
	
// 	if(isset($_SESSION['adm_user_id'])){
// 		header("location:index.php");
// 	}	
	
	$msg = "";
	$login_id = "";
	$password = "";
	
	if($_POST['login']){
		
		// Define $myusername and $mypassword
		$login_id = $_POST['login_id'];
		$password = $_POST['password'];
		
		if (empty($login_id) || empty($password)) {
			$msg = "ID、パスワードを入力して下さい。";
		} else {
			
			// 暗号化
			$key = md5(ENC_KEY);
			$td  = mcrypt_module_open('des', '', 'ecb', '');
			$key = substr($key, 0, mcrypt_enc_get_key_size($td));
			$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
			if (mcrypt_generic_init($td, $key, $iv) < 0) {
				$msg = "エラーが発生しました";
			}		
			$crypt_pass = base64_encode(mcrypt_generic($td, $password));
			mcrypt_generic_deinit($td);
			mcrypt_module_close($td);
			
			$db->connectDB();
			$bind_param	= $db->initBindParam();
			$sql		= "SELECT ID, adm_name FROM adm_user WHERE login_id = ? AND password = ? LIMIT 1;";
			$db->setSql_str($sql);
			$bind_param	= $db->addBind($bind_param, "s", $login_id);
			$bind_param	= $db->addBind($bind_param, "s", $crypt_pass);
			$result		= $db->exeQuery($bind_param);
			$count		= $db->getRows($result);
			
			// If result matched $myusername and $mypassword, table row must be 1 row
			if($count==1){
				$row = $db->exeFetch($result);
				$db->closeStmt($result);
				$_SESSION['adm_user_id'] = $row['ID'];
				header("location:index.php");
			}
			else {
				$msg = "ID又はパスワードが間違っています。";
			}		
		}
	}
	ob_end_flush();
	
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PSYCHO-PASS 管理画面</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">ログイン</h3>
                    </div>
                    <div class="panel-body">
                    	<p><?php echo $msg ?></p>
                        <form role="form" id="login_form" method="post" action="login.php">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Login ID" name="login_id" type="text" class="required"  value="<?php echo $login_id?>" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="" class="required">
                                </div>
                                <button type="submit" name="login" value="login" class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery Version 1.11.0 -->
    <script src="js/jquery-1.11.0.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
    
    <!-- Custom Theme JavaScript -->
	<script src="js/jquery.validate.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#login_form").validate({
			});
		});
	</script>
</body>

</html>
