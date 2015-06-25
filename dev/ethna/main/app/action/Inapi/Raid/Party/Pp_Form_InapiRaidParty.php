<?php
/**
 *  inapi_raid_* で共通のアクションフォーム定義
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_RaidPartyManager.php';
require_once 'Pp_RaidQuestManager.php';

class Pp_Form_InApiRaidParty extends Pp_InapiActionForm
{
	function __construct(&$backend) {
		$form_template = array(
			'party_id' => array(
				'name'     => 'パーティID',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'require'  => true,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'play_style' => array(
				'name'     => 'プレイスタイル',
				'type'     => VAR_TYPE_INT,
				'min'      => Pp_RaidPartyManager::PLAY_STYLE_NONE,
				'max'      => Pp_RaidPartyManager::PLAY_STYLE_TOP,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'auto_join' => array(
				'name'     => '自動入室',
				'type'     => VAR_TYPE_INT,
				'min'      => 0,
				'max'      => 1,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'force_reject' => array(
				'name'     => '強制退室',
				'type'     => VAR_TYPE_INT,
				'min'      => 0,
				'max'      => 1,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'message' => array(
				'name'     => 'メッセージ',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'pass' => array(
				'name'     => '入室パスワード',
				'type'     => VAR_TYPE_STRING,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'dungeon_id' => array(
				'name'     => 'ダンジョンID',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'dungeon_rank' => array(
				'name'     => '難易度',
				'type'     => VAR_TYPE_INT,
				'min'      => Pp_RaidQuestManager::DIFFICULTY_BEGINNER,
				'max'      => Pp_RaidQuestManager::DIFFICULTY_EXTRA,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'dungeon_lv' => array(
				'name'     => 'ダンジョンLV',
				'type'     => VAR_TYPE_INT,
				'min'      => 1,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'user_name' => array(
				'name'     => 'ユーザー名',
				'type'     => VAR_TYPE_STRING,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'user_id' => array(
				'name'     => 'ユーザーID',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'leader_mons_id' => array(
				'name'     => 'リーダーモンスターID',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'leader_mons_lv' => array(
				'name'     => 'リーダーモンスターLV',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'entry_type' => array(
				'name'     => '入室トリガ',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'user_status' => array(
				'name'     => 'ユーザーステータス',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'leave_type' => array(
				'name'     => '退室理由',
				'type'     => VAR_TYPE_INT,
				'min'      => Pp_RaidPartyManager::LEAVE_TYPE_SELF,
				'max'      => Pp_RaidPartyManager::LEAVE_TYPE_AUTO,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'master_user_id' => array(
				'name'     => 'パーティマスターユーザーID',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'active_team_id' => array(
				'name'     => 'アクティブチームID',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'user_team_list' => array(
				'name'     => 'ユーザーチームリスト',
				'type'     => array( VAR_TYPE_INT ),
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'total_enemy_hp' => array(
				'name'     => 'クエスト中の敵HP合計値',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'user_total_damage' => array(
				'name'     => 'ユーザーが与えたダメージの総計',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'hash_key' => array(
				'name'     => '検索用ハッシュ文字列',
				'type'     => VAR_TYPE_STRING,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
			'end_timestamp' => array(
				'name'     => 'ダンジョン終了日時',
				'type'     => VAR_TYPE_INT,
				'min'      => null,
				'max'      => null,
				'regexp'   => null,
				'mbregexp' => null,
				'mbregexp_encoding' => 'UTF-8',
			),
		);
		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}
		parent::__construct($backend);
	}
}
?>