<?php
/**
 *  Raid/Testdata/Party/Create.php
 *
 *  パーティおよびクエスト出撃のテストデータ作成
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

// inapi_raid_battle_initアクションと同じ処理をする為にインクルードおよびダミー派生クラス作成
// 作法的によろしくないので（Ethnaの標準的なアクション生成手順を踏まない）、
// この方式は他で多用しないこと
// （使用するのは、一時的に使用するCLIアクションのみにすること）
require_once BASE . '/app/action/Inapi/Raid/Battle/Init.php';

class Pp_Action_InapiRaidBattleInitDummy extends Pp_Action_InapiRaidBattleInit
{
	function __construct() {
		// 何もしない
	}
}

/**
 *  raid_testdata_party_create Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_RaidTestdataPartyCreate extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  raid_testdata_party_create action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_RaidTestdataPartyCreate extends Pp_CliActionClass
{
	protected $db_r = null;
	
    /**
     *  preprocess of raid_testdata_party_create Action.
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
     *  raid_testdata_party_create action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 第2引数以降を格納する
		// パーティ作成対象とするユーザー数
		if (isset($GLOBALS['argv'][2])) {
			$user_num = $GLOBALS['argv'][2];
		} else {
			$user_num = 10;
		}
		
		// 1ユーザーあたりの作成パーティ数
		if (isset($GLOBALS['argv'][3])) {
			$party_num_per_user = $GLOBALS['argv'][3];
		} else {
			$party_num_per_user = 2;
		}
		
		// クエスト出撃するか
		if (isset($GLOBALS['argv'][4])) {
			$quest_sally_flg = (strcmp($GLOBALS['argv'][4], '1') === 0);
		} else {
			$quest_sally_flg = true;
		}
		
		$this->db_r = $this->backend->getDB('r');
		
		for ($i = 0; $i < $user_num; $i++) {
			$user_base = $this->getRandomUserBase();
			$user_id = $user_base['user_id'];
			echo "user_id=[" . $user_id . "]\n";
			
			for ($j = 0; $j < $party_num_per_user; $j++) {
				$dungeon_id = $this->getRandomMasterRaidDungeonId();
//$dungeon_id = 1004;
				
				$party_id = $this->createParty($user_id, $dungeon_id);
				if (!$party_id || !is_numeric($party_id)) {
					continue;
				}
				
				$this->callRenewTmpDataAfterPartyInsert($party_id);
				$this->callRenewTmpDataAfterPartyMemberInsert($party_id, $user_id);
				
				if ($quest_sally_flg) {
					$this->initRaidBattle($party_id);
				}
				
				$rnd = mt_rand(0, 3);
				if (($rnd % 2) == 0) {
					if ($rnd == 2) {
						$status = Pp_RaidPartyManager::PARTY_STATUS_BREAKUP;
						$disconn = 0;
					} else {
						$status = Pp_RaidPartyManager::PARTY_STATUS_READY;
						$disconn = 1;
					}
					
					$this->callRenewTmpDataAfterPartyMemberUpdate($party_id, $user_id, $status, $disconn);
				}
			}
		}
		
		echo "Done.\n";
		
        return null;
    }
	
	// renewTmpDataAfterPartyInsertを呼ぶ
	function callRenewTmpDataAfterPartyInsert($party_id)
	{
		static $raid_party_m = null;
		if ($raid_party_m === null) {
			$raid_party_m = $this->backend->getManager('RaidParty');
		}
		
		static $raid_search_m = null;
		if ($raid_search_m === null) {
			$raid_search_m = $this->backend->getManager('RaidSearch');
		}		
		
		// 正常にパーティIDが取得できた
		$party_info = $raid_party_m->getParty( $party_id, false, true );	// 作成直後なのでマスターDBから取得
		if( empty( $party_info ) === true )
		{	// 取得エラー
//			throw new Exception( "ERROR: getParty( ${party_id} )" );
			error_log( "ERROR: getParty( ${party_id} )" );
		}
		
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($party_info, true));

		//-------------------------------------------------------------
		//	パーティ検索用テーブルにレコードを作成
		//-------------------------------------------------------------
		$ret = $raid_search_m->renewTmpDataAfterPartyInsert( $party_info );
		if( $ret === false )
		{	// エラー
//			throw new Exception( "ERROR: renewTmpDataAfterPartyInsert()" );
			error_log( "ERROR: renewTmpDataAfterPartyInsert()" );
		}
	}
	
	// パーティを作成する
	protected function createParty($user_id, $dungeon_id)
	{
		static $raid_party_m = null;
		if ($raid_party_m === null) {
			$raid_party_m = $this->backend->getManager('RaidParty');
		}

		$difficulty = mt_rand(1, 4);  // 難易度（1: 初級, 2:中級 3:上級 4:超級）
		$dungeon_lv = 1;
		$force_elimination = mt_rand(0, 1); // 強制退室設定（0:OFF, 1:ON）
		$play_style = mt_rand(1, 3); // プレイスタイル（1:初心者熱烈歓迎, 2:マイペースで, 3:トップを目指す！）
//		$login_passwd = array_rand(array("", "foo")); // 入室パスワード（空文字の場合は自動入室ON）
		$login_passwd = mt_rand(0, 1) ? "" : "foo"; // 入室パスワード（空文字の場合は自動入室ON）
		$message = 0; // メッセージコメント
		
		$party_id =  $raid_party_m->createParty($user_id, $dungeon_id, $difficulty, $dungeon_lv, $force_elimination, $play_style, $login_passwd, $message);
		if (!$party_id || !is_numeric($party_id)) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
		} else {
			echo "party_id=[" . $party_id . "]\n";
		}
		
		return $party_id;
	}
	
	// performInapiRaidBattleInitを呼び出す
	protected function initRaidBattle($party_id)
	{
		$this->af->set('party_id', $party_id);
		$this->af->set('play_id', uniqid());

		$forward_name = $this->performInapiRaidBattleInit();
		if ($forward_name != 'inapi_json') {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return;
		}

		$node_data = $this->af->getApp('node_data');
		echo "sally_no=[" . $node_data['sally_no'] . "]\n";
	}

	// inapi_raid_battle_initアクションのperformと同じ処理
	// 作法的によろしくないので（$action_class->afやbackendを外部から直接セットしたりとか）、
	// この方式は他で多用しないこと
	// （使用するのは、一時的に使用するCLIアクションのみにすること）
    function performInapiRaidBattleInit()
    {
		$action_class =& new Pp_Action_InapiRaidBattleInitDummy();
		$action_class->af =& $this->af;
		$action_class->backend =& $this->backend;
		
		return $action_class->perform();
	}
	
	// ランダムにユーザー基本情報を取得する
	protected function getRandomUserBase()
	{
		$rand = mt_rand(0, 65535);
		
		foreach (array('>=', '<') as $sign) {
			$param = array($rand, date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - 86400 * 100));
			$sql = "SELECT * FROM t_user_base"
				 . " WHERE login_rand $sign ? AND login_date > ?"
				 . " ORDER BY login_rand LIMIT 1";

			$row = $this->db_r->GetRow($sql, $param);
			if (is_array($row) && !empty($row)) {
				break;
			}
		}
		
		return $row;
	}
	
	// ランダムにダンジョンIDを取得する
	protected function getRandomMasterRaidDungeonId()
	{
		static $dungeon_id_list = null;
		static $dungeon_id_count = null;
		
		if ($dungeon_id_list === null) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
			$param = array($now, $now);
//$param = array('2001-01-01 00:00:00', '2030-12-31 23:59:59');
			$sql = "SELECT dungeon_id FROM m_raid_dungeon WHERE date_begin <= ? AND ? < date_end";

//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $sql);
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($param, true));
			$dungeon_id_list = $this->db_r->db->GetCol($sql, $param);
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($dungeon_id_list, true));
			$dungeon_id_count = count($dungeon_id_list);
		}
		
		$rnd = mt_rand(0, $dungeon_id_count - 1);
		
		return $dungeon_id_list[$rnd];
	}
	
	// renewTmpDataAfterPartyMemberInsertを呼ぶ
	function callRenewTmpDataAfterPartyMemberInsert($party_id, $user_id)
	{
		static $raid_search_m = null;
		if ($raid_search_m === null) {
			$raid_search_m = $this->backend->getManager('RaidSearch');
		}		
		
		//
		$ret = $raid_search_m->renewTmpDataAfterPartyMemberInsert(array(
			'party_id' => $party_id,
			'user_id'  => $user_id,
			'status'   => Pp_RaidPartyManager::PARTY_STATUS_READY,
			'disconn'  => 0,
		));
		if( $ret === false )
		{	// エラー
			error_log( "ERROR: renewTmpDataAfterPartyMemberInsert()" );
		}
	}
	
	// renewTmpDataAfterPartyMemberUpdateを呼ぶ
	function callRenewTmpDataAfterPartyMemberUpdate($party_id, $user_id, $status, $disconn)
	{
		static $raid_search_m = null;
		if ($raid_search_m === null) {
			$raid_search_m = $this->backend->getManager('RaidSearch');
		}		
		
		//
		$ret = $raid_search_m->renewTmpDataAfterPartyMemberUpdate(array(
			'party_id' => $party_id,
			'user_id'  => $user_id,
			'status'   => $status,
			'disconn'  => $disconn,
		));
		if( $ret === false )
		{	// エラー
			error_log( "ERROR: renewTmpDataAfterPartyMemberUpdate()" );
		}
	}
}
?>
