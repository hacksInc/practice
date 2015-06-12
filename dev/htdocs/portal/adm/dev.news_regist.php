<?php
require_once('../class/db_dev_adm.php');
$db = new db();

require_once('./sub/dev.cmn_include.php');

$msg = "";
$yy_select = "";
$mm_select = "";
$dd_select = "";
$open_yy_select = "";
$open_mm_select = "";
$open_dd_select = "";
$open_hh_select = "";
$open_ii_select = "";
$open_ss_select = "";
$news_kbn_checked1 = "";
$news_kbn_checked2 = "";

$kubun = "";
$date_yy = "";
$date_mm = "";
$date_dd = "";
$open_date_yy = "";
$open_date_mm = "";
$open_date_dd = "";
$open_date_hh = "";
$open_date_ii = "";
$open_date_ss = "";
$title = "";
$news = "";

$title_err = "";
$news_err = "";

if(isset($_POST['news_sbm']) && !empty($_POST['news_sbm'])) {
	$kubun		= htmlspecialchars($_POST["kubun"], ENT_QUOTES, 'UTF-8');
	$date_yy	= htmlspecialchars($_POST["date_yy"], ENT_QUOTES, 'UTF-8');
	$date_mm	= htmlspecialchars($_POST["date_mm"], ENT_QUOTES, 'UTF-8');
	$date_dd	= htmlspecialchars($_POST["date_dd"], ENT_QUOTES, 'UTF-8');
	$open_date_yy	= htmlspecialchars($_POST["open_date_yy"], ENT_QUOTES, 'UTF-8');
	$open_date_mm	= htmlspecialchars($_POST["open_date_mm"], ENT_QUOTES, 'UTF-8');
	$open_date_dd	= htmlspecialchars($_POST["open_date_dd"], ENT_QUOTES, 'UTF-8');
	$open_date_hh	= htmlspecialchars($_POST["open_date_hh"], ENT_QUOTES, 'UTF-8');
	$open_date_ii	= htmlspecialchars($_POST["open_date_ii"], ENT_QUOTES, 'UTF-8');
	$open_date_ss	= htmlspecialchars($_POST["open_date_ss"], ENT_QUOTES, 'UTF-8');
	$title		= htmlspecialchars($_POST["title"], ENT_QUOTES, 'UTF-8');
	$news		= htmlspecialchars($_POST["news"], ENT_QUOTES, 'UTF-8');
	$sr			= "1";

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
		$_SESSION["open_date_yy"]	= $open_date_yy;
		$_SESSION["open_date_mm"]	= $open_date_mm;
		$_SESSION["open_date_dd"]	= $open_date_dd;
		$_SESSION["open_date_hh"]	= $open_date_hh;
		$_SESSION["open_date_ii"]	= $open_date_ii;
		$_SESSION["open_date_ss"]	= $open_date_ss;
		$_SESSION["title"]		= $title;
		$_SESSION["news"]		= $news;
		$_SESSION["sr"]			= $sr;

		header("location:./dev.news_confirm.php");
	}

} else {

	clearNewsFromSession();
}

//ニュース区分checked
$news_kbn_checked2 = "checked";
if(!empty($kubun)) {
	if($kubun == "1") {
		$news_kbn_checked1 = "checked";
		$news_kbn_checked2 = "";
	}
}

//日付選択セレクトメニュー作成
//年
if(!empty($date_yy)) { 
	$current_y = $date_yy;
} else {
	$current_y = date('Y');
}
for ($i = 0; $i <= 2; $i++) {
	$yy = (int)$current_y + $i;	
	if($i == $current_y) {
		$yy_select .= '<option value="'.$yy.'" selected>'.$yy.'</option>';
	} else {
		$yy_select .= '<option value="'.$yy.'">'.$yy.'</option>';
	}
}
//月
if(!empty($date_mm)) {
	$current_m = (int)$date_mm;	
} else{
	$current_m = date('n');
}
for ($i = 1; $i <= 12; $i++) {
	if($i == $current_m) {
		$mm_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$mm_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}
//日
if(!empty($date_mm)) {
	$current_d = (int)$date_dd;	
} else{
	$current_d = date('j');
}
$days = date('t');
for ($i = 1; $i <= $days; $i++) {
	if($i == $current_d) {
		$dd_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$dd_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}

//予約日付選択セレクトメニュー作成
//年
$open_current_y = date('Y');

for ($i = 0; $i <= 2; $i++) {
	$open_yy = (int)$open_current_y + $i;
	if($i == $open_current_y) {
		$open_yy_select .= '<option value="'.$open_yy.'" selected>'.$open_yy.'</option>';
	} else {
		$open_yy_select .= '<option value="'.$open_yy.'">'.$open_yy.'</option>';
	}
}
//月
$open_current_m = date('n');
for ($i = 1; $i <= 12; $i++) {
	if($i == $open_current_m) {
		$open_mm_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$open_mm_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}
//日
$open_current_d = date('j');
$days = date('t');
for ($i = 1; $i <= $days; $i++) {
	if($i == $open_current_d) {
		$open_dd_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$open_dd_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}
//時
$open_current_h = date('G');
for ($i = 0; $i <= 23; $i++) {
	if($i == $open_current_h) {
		$open_hh_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$open_hh_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}

//分
$open_current_i = date('i');
for ($i = 0; $i <= 59; $i++) {
	if($i == $open_current_i) {
		$open_ii_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$open_ii_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}

//秒
$open_current_s = date('s');
for ($i = 0; $i <= 59; $i++) {
	if($i == $open_current_s) {
		$open_ss_select .= '<option value="'.sprintf("%02d", $i).'" selected>'.$i.'</option>';
	} else {
		$open_ss_select .= '<option value="'.sprintf("%02d", $i).'">'.$i.'</option>';
	}
}




?>
<?php  include_once('./include/dev.header.html'); ?>
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
				<div class="panel-heading"> 新規登録 </div>
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<form role="form" method="post">
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
								<a class="open_button">予約する</a>
								<a class="delete_button">取り消す</a>
								<div id="open">
									<div class="form-group day_select_group">
										<label class="fleft">予約日付</label>
										<select name="open_date_yy" class="form-control day_select clr 2">
											<?php echo $open_yy_select; ?>
										</select>
										<p class="day_select_txt">年</p>
										<select name="open_date_mm" class="form-control day_select 2">
											<?php echo $open_mm_select; ?>
										</select>
										<p class="day_select_txt">月</p>
										<select name="open_date_dd" class="form-control day_select 2">
											<?php echo $open_dd_select; ?>
										</select>
										<p class="day_select_txt">日</p>
										<select name="open_date_hh" class="form-control day_select 2">
											<?php echo $open_hh_select; ?>
										</select>
										<p class="day_select_txt">時</p>
										<select name="open_date_ii" class="form-control day_select 2">
											<?php echo $open_ii_select; ?>
										</select>
										<p class="day_select_txt">分</p>
										<select name="open_date_ss" class="form-control day_select 2">
											<?php echo $open_ss_select; ?>
										</select>
										<p class="day_select_txt">秒</p>
									</div>
								</div>
								<div class="clr"></div>
								<div  class="form-group <?php echo $title_err; ?>">
									<label>タイトル</label>
									<input name="title" value="<?php echo $title; ?>" class="form-control">
								</div>
								<div class="form-group <?php echo $news_err; ?>">
									<label>本文</label>
									<textarea name="news" value="<?php echo $news; ?>" class="form-control" rows="3"></textarea>
								</div>
								<button type="submit" name="news_sbm" value="news_sbm" class="btn btn-primary" >確　認</button>
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
<?php  include_once('./include/dev.footer.html'); ?>