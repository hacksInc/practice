<?php
// word press テンプレートの読み込み
require('../../wp-blog-header.php');

// 入力値チェック
function checkInput($var) {
	if (is_array($var)) {
		return array_map('checkInput', $var);
	} else {
		if (get_magic_quotes_gpc()) {
			$var = stripslashes($var);
		}
		if (preg_match('/\0/', $var)) {
			die('不正な入力です。');
		}
	return $var;
	}
}

// 文字列エスケープ
function h($string) {
	return htmlspecialchars($string, ENT_QUOTES);
}

// 長文文字列の改行を有効に
function hbr($str){
	echo nl2br(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}

session_start();

$_POST = checkInput($_POST);

// ランダム生成した数値のセッション値とPOST値の照合で不正アクセスチェック
if (isset($_POST['ticket']) && isset($_SESSION['ticket'])) {
	$ticket = $_POST['ticket'];
	if ($ticket != $_SESSION['ticket']) {
		die('不正なアクセスの疑いがあります。');
	}
} else {
	die('不正なアクセスの疑いがあります。');
}

// POSTされた値を$nameのみで呼び出せるようにする
foreach($_POST as $key => $value){
	if(is_array($value)){
		$value = implode("　",$value);
	}
	$_SESSION[$key] = trim($value);
}


// header.phpの読み込み
get_header('contact');
?>

<div id="contact" class="clearfix">
	<h2>お問い合わせ内容のご入力</h2>
	<form method="post" action="/contact/send/">
	<table>
		<tr>
			<th>お問い合わせ種別</th>
			<td><?php echo h($_SESSION['contactType']);?></td>
		</tr>
		<tr>
			<th>氏名</th>
			<td><?php echo h($_SESSION['name']);?></td>
		</tr>
		<tr>
			<th>貴社名</th>
			<td><?php echo h($_SESSION['companyName']);?></td>
		</tr>
		<tr>
			<th>メールアドレス</th>
			<td><?php echo h($_SESSION['email']);?><br></td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td><?php echo h($_SESSION['tel']);?></td>
		</tr>
		<tr>
			<th>お問い合わせ内容</th>
			<td><?php echo hbr($_SESSION['content']);?></td>
		</tr>
	</table>
	<div class="rewrite"><a href="javascript:history.back();">修正する</a></div>
	<div class="confirmForm">
		<script>
			$(function(){
				var key = '<?php echo h($ticket); ?>';
				$('#ticket').val(key);
			});
		</script>
		<input type="hidden" id="ticket" name="ticket" value="<?php echo h($ticket);?>">
		<input  class="submit" type="submit" value="送信する"/>
	</div>
	</form>
<!-- contact --></div>

<?php get_footer(); ?>