<?php 
require_once('../class/db_dev_adm.php');
$db = new db();

require_once('./sub/dev.cmn_include.php');

//ニュース取得
$db->connectDB();
$bind_param	= $db->initBindParam();
$sql		= "SELECT * FROM m_portal_news WHERE del_flg = '0';";
$db->setSql_str($sql);
$result		= $db->exeQuery($bind_param);
$count		= $db->getRows($result);

$html_table= "";
if($count > 0){
	$html_table .= '<tbody>';
		
	//フェッチ処理
	while ($news = $db->exeFetch($result) ) {
		//ユーザIDと名前表示
		$date = new DateTime($news["disp_date"]);
		$date_fmt = $date->format('Y/m/d');
		
		//ニュース区分
		if($news["news_kbn"] == "1") {
			$kbn_name = "Topics";
		}if($news["news_kbn"] == "2") {
			$kbn_name = "News";
		}
		
		$html_table .= ' <tr class="odd gradeA">';
		$html_table .= ' 	<td>'.$date_fmt.'</td>';
		$html_table .= ' 	<td>'.$kbn_name.'</td>';
		$html_table .= ' 	<td>'.cutString($news["news_title"], 30).'</td>';
		$html_table .= ' 	<td>'.cutString($news["news_text"], 80).'</td>';
		$html_table .= ' 	<td><a href="dev.news_update.php?news_id='.$news["ID"].'"><i class="fa fa-pencil fa-fw">変更</i></a>';
		$html_table .= ' 	<a href="dev.news_delete.php?news_id='.$news["ID"].'"><i class="fa fa fa-eraser fa-fw" alt="">削除</i></a></td>';
		$html_table .= ' </tr>';
		
	}
	
	$html_table .= '</tbody>';
	$db->closeStmt($result);
}

/***************************************************
 関数
***************************************************/
/**
 * id から、会員情報を取得
 * return	array
 */
function cutString($val, $limit) {
 	$cnt = mb_strlen($val,"UTF-8");
 	$ex = "";
 	if ($cnt > $limit) {
 		$ex = "...";
 	}
 	$str = mb_substr($val, 0, $limit, "UTF-8").$ex;
	return $str;
}

?>
<?php  include_once('./include/dev.header.html'); ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">News 一覧</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
             <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="">
                                    <thead>
                                        <tr>
                                            <th class="cell_01">掲載日</th>
                                            <th class="cell_02">区分</th>
                                            <th class="cell_03">タイトル</th>
                                            <th class="cell_04">内容</th>
                                            <th class="cell_05">&nbsp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                 	   <?php echo $html_table; ?>
                                    </tbody>
                                </table>
                            </div>
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