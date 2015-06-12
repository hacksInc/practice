<?php
/**
 *  ランキング集計データの更新
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  ranking_accumu Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_RankingRenew extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
       /*
        *  TODO: Write form definition which this action uses.
        *  @see http://ethna.jp/ethna-document-dev_guide-form.html
        *
        *  Example(You can omit all elements except for "type" one) :
        *
        *  'sample' => array(
        *      // Form definition
        *      'type'        => VAR_TYPE_INT,    // Input type
        *      'form_type'   => FORM_TYPE_TEXT,  // Form type
        *      'name'        => 'Sample',        // Display name
        *  
        *      //  Validator (executes Validator by written order.)
        *      'required'    => true,            // Required Option(true/false)
        *      'min'         => null,            // Minimum value
        *      'max'         => null,            // Maximum value
        *      'regexp'      => null,            // String by Regexp
        *      'mbregexp'    => null,            // Multibype string by Regexp
        *      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        *
        *      //  Filter
        *      'filter'      => 'sample',        // Optional Input filter to convert input
        *      'custom'      => null,            // Optional method name which
        *                                        // is defined in this(parent) class.
        *  ),
        */
    );

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  ranking_accumu action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_RankingRenew extends Pp_CliActionClass
{
    /**
     *  preprocess of ranking_accumu Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  ranking_accumu action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
	function perform()
	{
		echo "########################################################\n";
		echo "#\n";
		echo "#   ランキングデータ更新開始 : [".date("Y/m/d H:i:s")."]\n";
		echo "#\n";
		echo "########################################################\n";
		echo "\n";

		$ranking_m = $this->backend->getManager( 'Ranking' );
		$unit_m = $this->backend->getManager( 'Unit' );

		// 開催中のランキングマスタを取得
		echo "[".date("Y/m/d H:i:s")."] ◆ランキングマスタ取得\n";
		$masters = $ranking_m->getMasterValidRanking();
		if( empty( $masters ) === true )
		{	// 開催中のランキングなし
			echo "※開催期間中のランキングはありませんでした。\n";
		}
		$no_end_result_masters = $ranking_m->getMasterNoEndResultRanking();
		if( empty( $no_end_result_masters ) === true )
		{	// 最終結果を集計するランキングなし
			echo "※最終集計を行うランキングはありませんでした。\n";
		}

		if(( count( $masters ) + count( $no_end_result_masters )) === 0 )
		{	// 集計ランキングなし
			echo "※集計を行うランキングがなかったため処理を終了します。\n";
			echo "\n";
		}
		else
		{	// 集計ランキングあり
			echo "\n";

			// 更新対象のユニット一覧を取得
			echo "[".date("Y/m/d H:i:s")."] ◆更新対象ユニット取得\n";
			$unit_info = $unit_m->getUnitInfo();
			$unit_list = array_keys( $unit_info );
			echo "\n";

			if( empty( $masters ) === false )
			{
				echo "########### 開催中ランキングの集計を行います ###########\n";
				$this->_renewRanking( $masters, $unit_list, $ranking_m, $unit_m, false );
			}
			if( empty( $no_end_result_masters ) === false )
			{
				echo "######## 終了ランキングの最終結果集計を行います ########\n";
				$this->_renewRanking( $no_end_result_masters, $unit_list, $ranking_m, $unit_m, true );
			}
		}

		echo "########################################################\n";
		echo "#\n";
		echo "#   ランキングデータ更新終了 : [".date("Y/m/d H:i:s")."]\n";
		echo "#\n";
		echo "########################################################\n";
		return null;
    }

	/**
	 * ランキング情報を更新
	 * @param $masters 集計対象のランキングマスタ情報
	 * @param $unit_list 更新するユニットのリスト
	 * @param $ranking_m RankingManagerのインスタンス
	 * @param $unit_m UnitManagerのインスタンス
	 * @param $is_final true:最終結果 false:途中経過
	 */
	private function _renewRanking( $masters, $unit_list, $ranking_m, $unit_m, $is_final )
	{
		foreach( $masters as $master )
		{
			echo "[".date("Y/m/d H:i:s")."] ●RankingId[".$master['ranking_id']."] 『".$master['title']."』\n";

			// ランキングの集計
			$ranking_data = $ranking_m->countRanking( $master );
			if( is_null( $ranking_data ) === true )
			{	// ランキング情報取得エラー
				continue;
			}
			else if( empty( $ranking_data ) === false )
			{	// ランキング情報あり
				$this->_getUserName( $unit_m, $ranking_data );
			}

			echo "※ランキングレコードは".count( $ranking_data )."件です。\n";

			// ランキング管理情報の取得
			$info = $ranking_m->getRankingInfo( $master['ranking_id'] );
			if( empty( $info ) === true )
			{	// 管理情報なし
				$view_buffer = 0;
			}
			else
			{	// 管理情報あり
				$view_buffer = 1 - ( int )$info['view_buffer'];	// 次回参照するバッファ
			}

			// ランキング集計データ更新（全ユニットに対して更新を実行）
			foreach( $unit_list as $v )
			{
				echo "[".date("Y/m/d H:i:s")."] >> Unit".$v."を更新中 ... ";
				$record_count = $ranking_m->renewRankingData(
					$master['ranking_id'],
					$view_buffer,
					$v,
					$ranking_data
				);
				if( is_null( $record_count ) === true )
				{	// エラー
					echo "ERROR!!\n";
					echo "更新エラーが発生したため、このランキングの更新処理を中断します。\n";
					break;
				}
				echo "SUCCESS!!\n";
			}

			if( is_null( $record_count ) === false )
			{	// 最後まで正常に処理できたら管理情報を更新
				if( empty( $info ) === true )
				{
					$ranking_m->insertRankingInfo( $master['ranking_id'], $view_buffer, $record_count, $is_final );
					echo "[".date("Y/m/d H:i:s")."] >> 管理情報新規作成 参照バッファ[".$view_buffer."]\n";
				}
				else
				{
					$ranking_m->updateRankingInfo( $master['ranking_id'], $view_buffer, $record_count, $is_final );
					echo "[".date("Y/m/d H:i:s")."] >> 参照バッファ切り替え[".(1-$view_buffer)."⇒".$view_buffer."]\n";
				}
			}
			echo "\n";
		}
	}

	/**
	 * ユーザー名を取得
	 * @param $unit_m UnitManagerのインスタンス
	 * @param $ranking ランキングデータ
	 *
	 * 渡されたランキングデータにユーザー名を追加する（だから参照渡しになってる）
	 */
	private function _getUserName( &$unit_m, &$ranking )
	{
		// ユーザー名の取得
		$param = array();
		$where_user_id_in = array();
		for( $i = 0; $i < count( $ranking ); $i++ )
		{
			$param[] = $ranking[$i]['user_id'];
			$where_user_id_in[] = '?';
			$ranking[$i]['name'] = null;	// ついでに名前も初期化
		}
		$sql = 'SELECT user_id, name FROM t_user_base '
			 . 'WHERE user_id IN ('.implode(',', $where_user_id_in ).')';
		$name_list = $unit_m->getAllMultiUnit( $sql, $param, null, true );
		if( empty( $name_list ) === true )
		{	// ユーザー名取得エラー
			echo "※ユーザー名の取得ができませんでした。\n";
			return;
		}

		// 検索用テーブルの作成（user_idからindexを取得できるようにする）
		$index_table = array();
		foreach( $ranking as $k => $v )
		{
			$index_table[$v['user_id']] = $k;
		}

		// ユーザーIDに名前をセットする
		foreach( $name_list as $v )
		{
			$index = $index_table[$v['user_id']];
			$ranking[$index]['name'] = $v['name'];
		}
	}
}

?>