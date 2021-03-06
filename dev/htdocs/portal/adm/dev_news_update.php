<?php 
require_once('../class/db_adm.php');
$db = new db();

require_once('./sub/cmn_include.php');

$msg = "";
$yy_select = "";
$mm_select = "";
$dd_select = "";
$news_kbn_checked1 = "";
$news_kbn_checked2 = "";
$submit_buton = "";

$kubun = "";
$date_yy = "";
$date_mm = "";
$date_dd = "";
$title = "";
$news = "";

$title_err = "";
$news_err = "";

if(!isset($_GET['news_id']) || empty($_GET['news_id'])) {
	$msg = '<div class="alert alert-danger">エラーが発生しました。</div>';
	$submit_buton = '';
} else {
	$_SESSION['news_id'] = $_GET['news_id'];
	$submit_buton = '<button type="submit" name="news_sbm" value="news_sbm" class="btn btn-primary" >確　認</button>';
}

if(isset($_POST['news_sbm']) && !empty($_POST['news_sbm'])) {
	$kubun		= htmlspecialchars($_POST["kubun"], ENT_QUOTES, 'UTF-8');
	$date_yy	= htmlspecialchars($_POST["date_yy"], ENT_QUOTES, 'UTF-8');
	$date_mm	= htmlspecialchars($_POST["date_mm"], ENT_QUOTES, 'UTF-8');
	$date_dd	= htmlspecialchars($_POST["date_dd"], ENT_QUOTES, 'UTF-8');
	$title		= htmlspecialchars($_POST["title"], ENT_QUOTES, 'UTF-8');
	$news		= htmlspecialchars($_POST["news"], ENT_QUOTES, 'UTF-8');
	$sr			= "2";
	
	if(empty($kubun) || empty($date_yy) || empty($date_mm) || empty($date_dd) || empty($title) || empty($news)) {
		$msg = '<div class="alert alert-danger">未入力の項目があります。</div>';
		if(empty($title)) {
			$title_err = "has-error";
		}
		if(empty($news)) {
			$news_err = "has-error";
		}
	} else {
		//入力値セッション格納
		$_SESSION["kubun"]		= $kubun;
		$_SESSION["date_yy"]	= $date_yy;
		$_SESSION["date_mm"]	= $date_mm;
		$_SESSION["date_dd"]	= $date_dd;
		$_SESSION["title"]		= $title;
		$_SESSION["news"]		= $news;
		$_SESSION["sr"]			= $sr;
		
		header("location:./news_confirm.php");
	}
} else {
	clearNewsFromSession();
	
	//ニュース情報取得
	$db->connectDB();
	$bind_param	= $db->initBindParam();
	$sql		= "SELECT * FROM m_portal_news WHERE id = ?;";
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
		$title		= $news['news_title'];
		$news		= $news['news_text'];
	}
}
//ニュース区分checked
$news_kbn_checked2 = "checked";
if($kubun == "1") {
	$news_kbn_checked1 = "checked";
	$news_kbn_checked2 = "";
}

//日付選択セレクトメニュー作成
//年
$current_y = date('Y');
for ($i = 0; $i <= 2; $i++) {
	$yy = (int)$current_y + $i;	
	if($i == $date_yy) {
		$yy_select .= '<option value="'.$yy.'" selected>'.$yy.'</option>';
	} else {
		$yy_select .= '<option value="'.$yy.'">'.$yy.'</option>';
	}
}
//月
for ($i = 1; $i <= 12; $i++) {
	if($i == (int)$date_mm) {
		$mm_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$mm_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}
//日
$days = date('t');
for ($i = 1; $i <= $days; $i++) {
	if($i ==  (int)$date_dd) {
		$dd_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$dd_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
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
				<div class="panel-heading"> 変 更 </div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<form role="form" method="post"　>
								<?php echo $msg ?>
								<div class="form-group">
									<label>ニュース区分</label>
									<label class="radio-inline">
										<input type="radio" name="kubun" id="optionsRadiosInline1" value="1" <?php echo $news_kbn_checked1; ?>>
										トピックス </label>
									<label class="radio-inline">
										<input type="radio" name="kubun" id="optionsRadiosInline2" value="2" <?php echo $news_kbn_checked2; ?>>
										ニュース </label>
								</div>
								<div class="form-group day_select_group">
									<label class="fleft">表示日付</label>
									<select name="date_yy" class="form-control day_select clr">
										<?php echo $yy_select; ?>
									</select>
									<p class="day_select_txt">年</p>
									<select name="date_mm" class="form-control day_select">
										<?php echo $mm_select; ?>
									</select>
									<p class="day_select_txt">月</p>
									<select name="date_dd" class="form-control day_select">
										<?php echo $dd_select; ?>
									</select>
									<p class="day_select_txt">日</p>
								</div>
								<div class="clr"></div>
								<div  class="form-group <?php echo $title_err; ?>">
									<label>タイトル</label>
									<input name="title" value="<?php echo $title; ?>" class="form-control">
								</div>
								<div class="form-group <?php echo $news_err; ?>">
									<label>本文</label>
									<textarea name="news"class="form-control" rows="3"><?php echo $news; ?></textarea>
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