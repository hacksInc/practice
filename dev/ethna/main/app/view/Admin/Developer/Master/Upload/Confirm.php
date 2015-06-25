<?php
/**
 *  Admin/Developer/Master/Upload/Confirm.php
 *
 *  このビューはadmin_developer_master_upload_confirmアクション以外からも呼ばれる。
 *  （他のアクションから、Pp_AdminActionClass の performMasterUploadConfirm 経由で、このビューへ遷移する）
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_upload_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterUploadConfirm extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
/*
    function preforward()
    {
		$xml = $this->af->get( 'xml' );
		$table = $this->af->get('table');
		
		$developer_m =& $this->backend->getManager('Developer');
		
		$area = $developer_m->getMasterList($table);
		$label = $developer_m->getMasterColumnsLabel($table);
		$label_cnt = count($label);
		$colnames = array_keys($label);

		$table_label = $developer_m->getMasterTableLabel($table);
		
		$buf = mb_convert_encoding( file_get_contents( $xml['tmp_name'] ), 'utf-8', 'sjis-win' );
		
		$fp = tmpfile();
		fwrite( $fp, $buf );
		rewind( $fp );
		
		$list = array();
		
		$cnt = 0;
		
		setlocale(LC_ALL, 'ja_JP.UTF-8');
		while ( !feof( $fp ) ) {
			$row = fgetcsv( $fp, 10240 );
			$cnt++;
			
			// 一行目は破棄
			if ( $cnt == 1 ) {
				// 要素数が足りなければ処理を終了する
				if ( count( $row ) < $label_cnt ) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__ );
					break;
				}

				continue;
			}

			// 要素数を調整する
			$row = Pp_AdminActionForm::adjustCsvRow($row, $label_cnt);
			if ($row === false) {
				continue;
			}
			
			// 日付の書式を正規化
			for ($i = 0; $i < $label_cnt; $i++) {
				if ($developer_m->isDateColumnName($colnames[$i])) {
					$row[$i] = Pp_AdminActionForm::normalizeDateString($row[$i]);
				}
			}
			
			$id = $developer_m->getRowIdFromArray($table, $row);
			$list[$id] = $row;
		}
		
		fclose( $fp );
		
		// ファイルの位置を変更
		$fname = explode( "/", $xml['tmp_name'] );
		$fname[count( $fname ) - 1] = "tmp_" . $table . "_upd.csv";
		$fname = implode( "/", $fname );
		
		move_uploaded_file( $xml['tmp_name'], $fname );
		
		// 追加・変更箇所を判別
		$row_crud = array(); // 行ごとのCRUD種別  $row_crud[エリアID] = タイプ（新規追加の場合は'c', 更新の場合は'u'）
		$cell_update = array(); // セルごとの更新箇所　更新ある箇所は  $cell_update[ID][index値] = true  （index値はCSVで何列目かの値。0から数える）
		foreach ($list as $key => $row) {
			if (isset($area[$key])) {
				$cell_update[$key] = array();
				for ($i = 0; $i < $label_cnt; $i++) {
					if ($row[$i] != $area[$key][$colnames[$i]]) {
						$cell_update[$key][$i] = true;
						$row_crud[$key] = 'u';
					}
				}
			} else {
				$row_crud[$key] = 'c';
			}
		}
		
		$this->af->setApp( "list", $list );
		$this->af->setApp('label', $label);
		$this->af->setApp( "cell_update", $cell_update );
		$this->af->setApp( "row_crud", $row_crud );
		$this->af->setApp( "fname", $fname );
		$this->af->setApp('table_label', $table_label);
		
		parent::preforward();
    }
*/
}

?>