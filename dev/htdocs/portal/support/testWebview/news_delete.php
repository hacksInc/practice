<?php 
require_once('../class/db.php');
$db = new db();

require_once('./sub/cmn_include.php');

if(!isset($_GET['news_id']) || empty($_GET['news_id'])) {
	$msg = '<div class="alert alert-danger">エラーが発生しました。</div>';
	$submit_buton = '';
} else {
	$_SESSION['news_id'] = $_GET['news_id'];
	$_SESSION['sr'] = "3";
	$submit_buton = '<button type="submit" name="news_sbm" value="news_sbm" class="btn btn-primary" onclick="return confirm( \'削除しますか？\')" >削 除</button>';
}

//ニュース情報取得
$db->connectDB();
$bind_param	= $db->initBindParam();
$sql		= "SELECT * FROM news WHERE id = ?;";
$bind_param = $db->addBind($bind_param, "i", $_GET['news_id']);
$db->setSql_str($sql);
$result		= $db->exeQuery($bind_param);
$count		= $db->getRows($result);
if ($count <= 0) {
	$msg = '<div class="alert alert-danger">エラーが発生しました。</div>';
	$submit_buton = '';
}
$news = $db->exeFetch($result);
$db->closeStmt($result);
if (empty($news)) {
	$msg = '<div class="alert alert-danger">エラーが発生しました。</div>';
	$submit_buton = '';
} else {
	$kubun		= $news['news_kbn'];
	$date_yy	= substr($news['disp_date'], 0, 4);
	$date_mm	= substr($news['disp_date'], 4, 2);
	$date_dd	= substr($news['disp_date'], 6, 2);
	$open_date_yy	= substr($news['open_date'], 0, 4);
	$open_date_mm	= substr($news['open_date'], 5, 2);
	$open_date_dd	= substr($news['open_date'], 8, 2);
	$open_date_hh	= substr($news['open_date'], 11, 2);
	$open_date_ii	= substr($news['open_date'], 14, 2);
	$open_date_ss	= substr($news['open_date'], 17, 2);
	$title		= $news['news_title'];
	$news		= $news['news_text'];
}

//ニュース区分
if($kubun== "1") {
	$kbn_name = "Topics";
}if($kubun == "2") {
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
				<div class="panel-heading"> 削 除 </div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<form role="form" method="post" action="news_finish.php">
								<?php echo $msg; ?>
								<div class="form-group">
									<label>ニュース区分</label>
									<p class="confirm_txt"><?php echo $kbn_name ?></p>
								</div>
								<div class="form-group">
									<label>表示日付</label>
									<p class="confirm_txt"><?php echo $date_yy; ?> 年 <?php echo $date_mm; ?> 月 <?php echo $date_dd; ?> 日</p>
								</div>
								<?php if ($open_date_yy > date('Y-m-d H:i:s')) : ?>
								<div class="form-group">
									<label>公開予約日時</label>
									<p class="confirm_txt"><?php echo $open_date_yy; ?> 年 <?php echo $open_date_mm; ?> 月 <?php echo $open_date_dd; ?> 日 
									<?php echo $open_date_hh; ?> 時 <?php echo $open_date_ii; ?> 分 <?php echo $open_date_ss; ?>　秒 </p>
								</div>
								<?php endif; ?>
								<div class="clr"></div>
								<div class="form-group">
									<label>タイトル</label>
									<p class="confirm_txt"><?php echo $title ?></p>
								</div>
								<div class="form-group">
									<label>本文</label>
									<p class="confirm_txt"><?php echo $news ?></p>
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