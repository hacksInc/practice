<?php 
require_once('../class/db_adm.php');
$db = new db();

require_once('./sub/cmn_include.php');

$msg = "";
$opendate = $_SESSION["open_date_yy"].$_SESSION["open_date_mm"].$_SESSION["open_date_dd"].$_SESSION["open_date_hh"].$_SESSION["open_date_ii"].$_SESSION["open_date_ss"];

if( !strpos($_SERVER['HTTP_REFERER'], "/news_confirm.php") && !strpos($_SERVER['HTTP_REFERER'], "/news_delete.php")) {
	$msg = '<div class="alert alert-danger">エラーが発生しました。</div>';
	$submit_buton = '';
} else {
	//	ニュースの登録
	$db->connectDB();
	$bind_param	= $db->initBindParam();
	if($_SESSION["sr"] == 1) {
		$exce_kbn = "登録";
		$sql		= "INSERT INTO m_portal_news (news_kbn, disp_date, news_title, news_text, regist_user_id, update_date, open_date) VALUES (?, ?, ?, ?, ?, ?, ?);";
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["kubun"]);
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["date_yy"].$_SESSION["date_mm"].$_SESSION["date_dd"]);
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["title"]);
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["news"]);
		$bind_param = $db->addBind($bind_param, "i", $_SESSION["adm_user_id"]);
		$bind_param = $db->addBind($bind_param, "s", date("Y-m-d H:i:s"));
		$bind_param = $db->addBind($bind_param, "s", $opendate);
	} else if($_SESSION["sr"] == 2){
		$exce_kbn = "更新";
		$sql		= "UPDATE m_portal_news SET news_kbn = ?, disp_date = ?, news_title = ?, news_text = ? , regist_user_id = ?, update_date = ?, open_date = ? WHERE id= ?;";
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["kubun"]);
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["date_yy"].$_SESSION["date_mm"].$_SESSION["date_dd"]);
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["title"]);
		$bind_param = $db->addBind($bind_param, "s", $_SESSION["news"]);
		$bind_param = $db->addBind($bind_param, "i", $_SESSION["adm_user_id"]);
		$bind_param = $db->addBind($bind_param, "s", date("Y-m-d H:i:s"));
		$bind_param = $db->addBind($bind_param, "s", $opendate);
		$bind_param = $db->addBind($bind_param, "i", $_SESSION["news_id"]);
	} else if($_SESSION["sr"] == 3){		
		$exce_kbn = "削除";
		$sql		= "UPDATE m_portal_news SET update_date = ?, del_flg= ? WHERE id= ?;";
		$bind_param = $db->addBind($bind_param, "s", date("Y-m-d H:i:s"));
		$bind_param = $db->addBind($bind_param, "s", "1");
		$bind_param = $db->addBind($bind_param, "i", $_SESSION["news_id"]);
	}
	$db->setSql_str($sql);
	$result		= $db->exeQuery($bind_param);
	$count		= $db->getAffectedRows($result);
	
	if ($count <= 0) {
		$db->rollback();
		$msg = '<div class="alert alert-danger">エラーが発生しました。</div>';
	} else {
		$msg = '<div class="alert alert-success">'.$exce_kbn.'しました。</div>';	
		$db->closeStmt($result);
		//	コミット
		$db->commit();
	}
	//clearNewsFromSession();
}
//ニュース区分
if($_SESSION["kubun"] == "1") {
	$kbn_name = "Topics";
}if($_SESSION["kubun"] == "2") {
	$kbn_name = "News";
}
?>
<?php  include_once('./include/header.html'); ?>
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">News</h1>
		</div>
		<!-- /.col-lg-12 --> 
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading"> 完 了 </div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<?php echo $msg ?>
							<div class="form-group">
								<label>ニュース区分</label>
								<p class="confirm_txt"><?php echo $kbn_name; ?></p>
							</div>
							<div class="form-group">
								<label>表示日付</label>
								<p class="confirm_txt"><?php echo $_SESSION["date_yy"]; ?> 年 <?php echo $_SESSION["date_mm"]; ?> 月 <?php echo $_SESSION["date_dd"]; ?> 日</p>
							</div>
							<?php if ( $opendate > date('YmdHis') ) : ?>
							<div class="form-group">
								<label>予約日付</label>
								<p class="confirm_txt"><?php echo $_SESSION["open_date_yy"]; ?> 年 <?php echo $_SESSION["open_date_mm"]; ?> 月 <?php echo $_SESSION["open_date_dd"]; ?> 日 <?php echo $_SESSION["open_date_hh"]; ?> 時 <?php echo $_SESSION["open_date_ii"]; ?> 分 <?php echo $_SESSION["open_date_ss"]; ?> 秒</p>
							</div>
							<?php endif; ?>
							<div class="clr"></div>
							<div class="form-group">
								<label>タイトル</label>
								<p class="confirm_txt"><?php echo $_SESSION["title"]; ?></p>
							</div>
							<div class="form-group">
								<label>本文</label>
								<p class="confirm_txt"><?php echo $_SESSION["news"]; ?></p>
							</div>
							 <button type="button" class="btn btn-outline btn-success" onclick="location.href='./index'">一覧に戻る</button>
						</div>
						<!-- /.col-lg-6 (nested) --> 
					</div>
					<!-- /.row (nested) --> 
				</div>
				<!-- /.panel-body --> 
			</div>
			<!-- /.panel --> 
		</div>
		<!-- /.col-lg-12 --> 
	</div>
	<!-- /.row --> 
</div>
<!-- /#page-wrapper --> 
<?php  include_once('./include/footer.html'); ?>