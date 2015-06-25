<?php
require_once('../class/db_adm.php');
$db = new db();

require_once('./sub/cmn_include.php');

if($_SESSION["sr"] == 1) {
	$exce_kbn = "登 録";
} else if($_SESSION["sr"] == 2){
	$exce_kbn = "更 新";
} else if($_SESSION["sr"] == 3){
	$exce_kbn = "削 除";
}

//送信元チェック
if( !strpos($_SERVER['HTTP_REFERER'], "/news_regist.php") && !strpos($_SERVER['HTTP_REFERER'], "/news_update.php")) {
	$msg = '<div class="alert alert-danger">エラーが発生しました。</div>';
	$submit_buton = '';
} else {
	$msg = '<div class="alert alert-info">内容を確認してよろしければ【'.$exce_kbn.'】ボタンをクリックして下さい。</div>';
	$submit_buton = '<button type="submit" name="news_sbm" value="news_sbm" class="btn btn-primary" >'.$exce_kbn.'</button>  '.
					'<input type="button" value="戻　る" onClick="history.back()" class="btn btn-outline btn-primary">';
}

//ニュース区分
if($_SESSION["kubun"] == "1") {
	$kbn_name = "Topics";
}if($_SESSION["kubun"] == "2") {
	$kbn_name = "News";
}

$opendate = $_SESSION["open_date_yy"].$_SESSION["open_date_mm"].$_SESSION["open_date_dd"].$_SESSION["open_date_hh"].$_SESSION["open_date_ii"].$_SESSION["open_date_ss"];
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
				<div class="panel-heading"> 確 認 </div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<form role="form" method="post" action="news_finish.php">
								<?php echo $msg; ?>
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
								<!-- <?php //else :　?>
								<p style="color:#f00;">※ 予約時刻が過ぎているか設定されていないため公開されます。</p>
								-->
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
								<?php echo $submit_buton; ?>
							</form>
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
