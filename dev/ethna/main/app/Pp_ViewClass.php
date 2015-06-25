<?php
// vim: foldmethod=marker
/**
 *  Pp_ViewClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 * 値をJSON形式にして返す
 * 
 * JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHESの処理をPHP5.2でも行う為の実装
 */
function pp_view_json_encode($value)
{
	$json = json_encode($value);
	
	// Unicodeエスケープされた文字列をUTF-8文字列に戻す
	// http://d.hatena.ne.jp/iizukaw/20090422
	$json = preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", "pp_view_encode_callback", $json);
	
	// スラッシュのエスケープをアンエスケープする
	// http://kohkimakimoto.hatenablog.com/entry/2012/05/17/180738
	$json = preg_replace('/\\\\\//', '/', $json);
	
	return $json;
}

function pp_view_encode_callback($matches) {
  return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
}

// {{{ Pp_ViewClass
/**
 *  View class.
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_ViewClass extends Ethna_ViewClass
{
	/** 文字列型カラムのカラム名 */
	protected $string_cols = array(
		'name', 'name_ja', 'name_en', 'name_es', 
		'description', 'description_ja', 'description_en', 'description_es', 
		'comment', 'url_ja_ios', 'url_en_ios', 'url_es_ios', 'url_ja_android', 'url_en_android', 'url_es_android', 'url_ja', 'url_en', 'url_es', 
		'login_date', 'base_date', 'date_start', 'date_end', 'date_created', 'date_modified', 'date_bring', 'date_last_login',
		'time_start', 'time_end', 'week_element',
		'uipw', 'dmpw', 'account', 'dir', 'file_name', 'start_date', 'end_date', 'created_date', 'modify_date',
		'evolution_material', 'url_data', 'date_met', 'date_got', 'rp_base_date', 
		'product_id', 'display_name', 'game_transaction_id', 'point_output_sts',
		'comment_event', 'comment_baloon', 'server_time', 'play_id', 
		'banner_url', 'banner_attribute_value', 'screen_name',
		'dungeon_name', 'unlock_dungeon_name', 'raid_end_date', 'guerrilla_end_date', 'boss_name', 'user_name', 'raid_clear_time', 
		'badges', 'material_ids', 'create_start_time', 'create_end_time', 'view_start_time', 'view_end_time', 'start_time', 'end_time', 

		// サイコパス追加分
		'date_next_stress_care', 'base_stress_care_date', 'transaction_id',
		'install_pw', 'migrate_id', 'migrate_pw', 'shop_name', 'ave_area_stress', 'campaign_text', 'limit_time', 
		'icon_s_name', 'icon_l_name', 'unlock_character_ids', 'unlock_parameters', 'condition_best', 'model_id', 'ai_id', 'attribute', 'voice_name',
		'karanomori_adv_id', 'karanomori_adv_id_lost', 'karanomori_adv_id_find', 'karanomori_report_prob', 'karanomori_find_prob', 'message', 'mission_ids',
	);
	
	/** bigint型カラムのカラム名 */
	protected $bigint_cols = array(
		'user_monster_id', 'helper_user_monster_id',
		'pos1', 'pos2', 'pos3', 'pos4', 'pos5',
		'present_id',
	);
	

	/** 文字列型カラムだけど配列で値が文字列のカラム名 */
	protected $string_array_cols = array('banner_attribute_value');

	/**
	 *  set common default value.
	 *
	 *  @access protected
	 *  @param  object  Pp_Renderer  Renderer object.
	 */
	function _setDefault(&$renderer)
	{
	}

	/**
	 * 連想配列の所定の値を数値へ変換
	 * 
	 * キー名が"name"等の場合を除き、値をintvalする。
	 * JSON戻り値の生成時に使用する。
	 */
	protected function array_intval($value, $key = null)
	{
		if ($this->config->get('is_test_site')) { // 開発環境の場合
			// パスワード関連が戻り値に含まれている可能性があったら警告
			// (これらはもし存在してもintvalされて0が出力されるだけなので実害ないはずだが、念のため)
			if ($key && in_array($key, array(
				'uipw_hash',
				'dmpw_hash',
			))) {
				$this->backend->logger->log(LOG_WARNING, 
					'Possible security vulnerability. ' .
					'Output value of ' . $key . ' exists in ' . $this->backend->ctl->getCurrentActionName() . '.'
				);
			}
		}
		
		if (is_array($value)) {
			// 配列は再帰的に処理
			foreach ($value as $k => $v) {
				$colname = null;
				// 配列で値が文字列の場合int変換されるので元々のキー名を渡す
				if(in_array($key, $this->string_array_cols, true)) {
					$colname = $key;
				} else {
					$colname = $k;
				}
				$value[$k] = $this->array_intval($v, $colname);
			}
			return $value;
		} else if (in_array($key, $this->string_cols, true)) {
			// 文字列のはずの箇所は変換しない
			return $value;
		} else if (in_array($key, $this->bigint_cols, true)) {
			// bigintはクライアント側アプリでの扱いに難があるので文字列にする
			return strval($value);
		}
		
		// 数値へ変換する
		return intval($value);
	}
}
// }}}

?>
