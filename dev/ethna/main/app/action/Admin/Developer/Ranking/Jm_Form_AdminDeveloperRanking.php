<?php
/**
 *  admin_developer_ranking_* で共通のアクションフォーム定義
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminRankingPrizeManager.php';
require_once 'Pp_AdminRankingManager.php';

class Pp_Form_AdminDeveloperRanking extends Pp_AdminActionForm
{
	function __construct(&$backend) {
		$form_template = array(
			'ranking_id' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_HIDDEN,
				'name'      => 'ランキングID',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'title' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'タイトル',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'subtitle' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'サブタイトル',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'target_type' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => 'ターゲット種別',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'targets' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'ターゲット',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'processing_type' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '獲得方法',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'clear_target_dungeon_rank3' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'レイド上級クリア対象ダンジョンID',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'clear_target_dungeon_rank4' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'レイド超級クリア対象ダンジョンID',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'threshold' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '集計順位閾値',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'view_higher' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '上位表示数',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'view_lower' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '下位表示数',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'view_ranking_top' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'ランキングTOP表示数',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'date_start' => array(
				'type'      => VAR_TYPE_DATETIME,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '集計開始日時',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
				'required'  => false,
				'filter'    => null,
				'custom'    => null,
			),
			'date_end' => array(
				'type'      => VAR_TYPE_DATETIME,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '集計終了日時',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
				'required'  => false,
				'filter'    => null,
				'custom'    => null,
			),
			'banner_url' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'バナーURL',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'url' => array(
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => 'URL',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),

			'id' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '賞品配布情報管理ID',
				'min'       => 1,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'distribute_start' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '配布先頭順位',
				'min'       => 1,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'distribute_end' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '配布末尾順位',
				'min'       => 1,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'prize_type' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => '賞品タイプ',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'prize_id' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '賞品ID',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'lv' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '初期レベル',
				'min'       => 1,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'number' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_TEXT,
				'name'      => '配布数',
				'min'       => 1,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'status' => array(
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => '状態',
				'min'       => null,
				'max'       => null,
				'regexp'    => null,
				'mbregexp'  => null,
				'mbregexp_encoding' => 'UTF-8',
			),
		);
		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}
		parent::__construct($backend);
	}

	/**
	 * ランキングの終了日時が開始日時より未来かどうかをチェック
	 */
	function checkRankingDatetimeEnd( $name )
	{
		$date_end = date_create( $this->form_vars[$name] );
		$date_start = date_create( $this->form_vars['date_start'] );

		if( $date_start >= $date_end )
		{	// 終了日時が開始日時と同じか過去の日付になっている
			$this->ae->add( $name, "集計終了日時は集計開始日時よりも未来に設定する必要があります" );
		}
	}

	/**
	 * 賞品IDの必須入力チェック
	 */
	function checkRequiredPrizeId( $name )
	{
		$prize_type = intval( $this->form_vars['prize_type'] );
		$prize_id = $this->form_vars[$name];

		// 賞品タイプによって必須どうかが決まる
		switch( $prize_type )
		{
			// 入力必須のもの
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_ITEM:		// 通常アイテム
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_MONSTER:	// モンスター
				if(( is_null( $prize_id ) === true )||( strlen( $prize_id ) === 0 ))
				{	// 入力なし
					$this->ae->add( $name, "賞品IDが入力されていません" );
				}
				break;

			// 入力必須でないもの
			default:
				break;
		}
	}

	/**
	 * LVの必須入力チェック
	 */
	function checkRequiredLv( $name )
	{
		$prize_type = intval( $this->form_vars['prize_type'] );
		$lv = $this->form_vars[$name];

		// 賞品タイプによって必須どうかが決まる
		switch( $prize_type )
		{
			// 入力必須のもの
			case Pp_AdminRankingPrizeManager::PRIZE_TYPE_MONSTER:	// モンスター
				if(( is_null( $lv ) === true )||( strlen( $lv ) === 0 ))
				{	// 入力なし
					$this->ae->add( $name, "初期レベルが入力されていません" );
				}
				break;

			// 入力必須でないもの
			default:
				break;
		}
	}

	/**
	 * 配布順位末尾のチェック
	 */
	function checkDistributeEnd( $name )
	{
		$distribute_start = intval( $this->form_vars['distribute_start'] );
		$distribute_end = intval( $this->form_vars[$name] );

		if( empty( $distribute_end ) === false )
		{
			if( $distribute_start > $distribute_end )
			{
				$this->ae->add( $name, "配布順位末尾は配布順位先頭よりも大きい値を入力してください" );
			}
		}
	}

	/**
	 * ターゲットの必須入力チェック
	 */
	function checkRequiredTargets( $name )
	{
		$target_type = intval( $this->form_vars['target_type'] );
		$targets = $this->form_vars[$name];
		switch( $target_type )
		{
			case Pp_RankingManager::TARGET_TYPE_MONSTER:
				if(( is_null( $targets ) === true )||( strlen( $targets ) === 0 ))
				{	// 入力なし
					$this->ae->add( $name, "ターゲットが入力されていません" );
				}
				break;

			default:
				break;
		}
	}

	/**
	 * 獲得方法の必須入力チェック
	 */
	function checkRequiredProcessingType( $name )
	{
		$target_type = intval( $this->form_vars['target_type'] );
		$processing_type = $this->form_vars[$name];
		switch( $target_type )
		{
			case Pp_RankingManager::TARGET_TYPE_MONSTER:
				if(( is_null( $processing_type ) === true )||( strlen( $processing_type ) === 0 ))
				{	// 入力なし
					$this->ae->add( $name, "獲得方法が入力されていません" );
				}
				break;

			default:
				break;
		}
	}

	/**
	 * ターゲットの入力フォーマットチェック
	 */
	function checkTargetFormat( $name )
	{
		$target_type = intval( $this->form_vars['target_type'] );
		if( $target_type === Pp_RankingManager::TARGET_TYPE_MONSTER )
		{	// ターゲット種別がモンスターの場合は、"[monster_id]-[point]"の形式入力する必要がある
			$targets = trim( $this->form_vars['targets'] );
			if( strlen( $targets ) === 0 )
			{	// ターゲットが空欄ならチェックしない
				return;
			}

			$buff = explode( ',', $targets );
			foreach( $buff as $target )
			{
				$temp = explode( '-', $target );
				if(( count( $temp ) != 2 )||( strlen( trim( $temp[1] )) === 0 ))
				{	// 入力フォーマットが“????????-???”の形式ではない
					$this->ae->add( $name, "ターゲット種別がモンスターの場合は、“【モンスターID】－【獲得ポイント】”の形式で入力してください" );
					break;
				}
				else if( ctype_digit( $temp[1] ) === false )
				{	// 獲得ポイントに整数値以外が指定されている
					$this->ae->add( $name, "ターゲットの獲得ポイントには数字（整数）を指定してください" );
					break;
				}
				else if(( int )$temp[1] === 0 )
				{	// 獲得ポイントが０ポイント
					$this->ae->add( $name, "獲得ポイントが０ポイントのターゲットが設定されています" );
					break;
				}
			}
		}
	}

	/**
	 * クリア対象レイドダンジョンの入力フォーマットチェック
	 */
	function checkClearTargetDungeonFormat( $name )
	{
		$target_type = intval( $this->form_vars['target_type'] );
		if( $target_type === Pp_RankingManager::TARGET_TYPE_RAID_RANKING )
		{	// レイドランキングポイントの場合のみチェック
			$target = trim( $this->form_vars[$name] );
			if( strlen( $target ) === 0 )
			{	// クリア対象のダンジョンIDがない
				$this->ae->add( $name, "ダンジョン制覇の対象となるダンジョンIDが指定されていません" );
			}
			else
			{
				$buff = explode( ',', $target );
				foreach( $buff as $dungeon_id )
				{
					if( ctype_digit( $dungeon_id ) === false )
					{	// ダンジョンIDに整数値以外が指定されている
						$this->ae->add( $name, "ダンジョンIDには数字（整数）を指定してください" );
						break;
					}
				}
			}
		}
	}
}

?>