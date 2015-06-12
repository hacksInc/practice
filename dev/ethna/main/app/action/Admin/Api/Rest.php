<?php
/**
 *  Admin/Api/Rest.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_api_rest Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminApiRest extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'_table' => array(
			'type'        => VAR_TYPE_STRING,
			'required'    => true,
			'regexp'      => '/^[m_|ut_]/',
			'filter'      => 'table',
		),
		'_id' => array(
			'type'        => VAR_TYPE_STRING,
			'required'    => false,
			'filter'      => 'id',
		),
	);

	/**
	 * REST形式のURLからテーブル名を取り出すフィルタ
	 *
	 * URLのパスが、
	 * HTTPメソッドがPOSTの場合は、/admin/api/rest/テーブル名
	 * HTTPメソッドがPOST以外の場合は、/admin/api/rest/テーブル名/ID
	 * となっている中から、テーブル名部分を取り出す。
	 * @see template/ja_JP/admin/developer/master/edit.tpl
	 */
	function _filter_table($value)
	{
		$dirs = explode('/', $_SERVER['SCRIPT_URI']);

		$method = $this->getHttpMethod();
		if ($method != 'POST') {
			$id = array_pop($dirs);
		}
		$table = array_pop($dirs);

		return $table;
	}

	/**
	 * REST形式のURLからIDを取り出すフィルタ
	 */
	function _filter_id($value)
	{
		$dirs = explode('/', $_SERVER['SCRIPT_URI']);

		$method = $this->getHttpMethod();
		if ($method != 'POST') {
			$id = array_pop($dirs);
		} else {
			$id = null;
		}

		return $id;
	}

	/**
	 *  HTTPメソッドを取得する
	 *
	 *  @see http://backbonejs.org/#Sync-emulateHTTP
	 *  @param boolean $override X-HTTP-Method-Overrideヘッダを参照するか
	 */
	function getHttpMethod($override = true)
	{
		$method = null;

		if ($override) {
			$headers = getallheaders();
			if (isset($headers['X-HTTP-Method-Override'])) {
				$method = $headers['X-HTTP-Method-Override'];
			}
		}

		if (!$method) {
			if (isset($_SERVER['REQUEST_METHOD'])) {
				$method = $_SERVER['REQUEST_METHOD'];
			}
		}

		if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'PATCH'))) {
			return null;
		}

		return $method;
	}

	/**
	 * Backbone.jsからのJSONリクエスト値を取得する
	 *
	 * DeveloperManagerでのメタデータを参照して型チェックも行なう
	 * @param string $name カラム名
	 * @return int|bool|string リクエスト値
	 */
	function getBackbone($name)
	{
		if (!isset($_POST['model'])) {
			return null;
		}

		if (get_magic_quotes_gpc()) {
			$model = stripslashes($_POST['model']);
		} else {
			$model = $_POST['model'];
		}

		$model = json_decode($model, true);
		if (!$model || !isset($model[$name])) {
			return null;
		}

		$form_var = $model[$name];

		$table = $this->get('_table');

		if (strstr($table, 'ut_')) {
			$user_m =& $this->backend->getManager('AdminUser');
			$metadata = $user_m->getEditableGridMetadata($table);

		} else {
			$developer_m = $this->backend->getManager('Developer');
			$metadata = $developer_m->getEditableGridMetadata($table);
		}

		foreach ($metadata as $row) {
			if ($row['name'] != $name) {
				continue;
			}

			$datatype = $row['datatype'];
			if ($datatype == 'number') {
				if (is_numeric($form_var)) {
					return $form_var;
				}
			} else if ($datatype == 'boolean') {
				if (is_bool($form_var)) {
					return $form_var;
				}
			} else if ($datatype == 'string') {
				return $form_var;
			}

			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $name . ':' . $form_var);
		}

		error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $name . ':' . $form_var);
	}

	function getBackboneAll()
	{
		if (!isset($_POST['model'])) {
			return null;
		}

		if (get_magic_quotes_gpc()) {
			$model = stripslashes($_POST['model']);
		} else {
			$model = $_POST['model'];
		}

		$model = json_decode($model, true);
		$all = array();
		foreach ($model as $name => $value) {
			//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $name . ':' . $value);
			if ($name == 'action') {
				continue;
			}

			if ($this->getBackbone($name) == $value) {
				$all[$name] = $value;
			}
		}

		error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($all, true));
		return $all;
	}
}

/**
 *  admin_api_rest action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminApiRest extends Pp_AdminActionClass
{
	// マスタテーブルに紐付くキャッシュのキー名
	private $cachekey_associated_table = array(
		'm_item'             => array('master_item'),
		'm_consume_price'    => array('master_consume_price'),
		'm_achievement_type' => array('master_achievement_type_assoc'),
		'm_achievement_rank' => array('master_achievement_rank_assoc'),
	);

	/**
	 *  preprocess of admin_api_rest Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
/*
	function prepare()
	{
		// アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
	}
 */

	/**
	 *  admin_api_rest action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$method = $this->af->getHttpMethod();
		$table = $this->af->get('_table');

		if (strstr($table, 'ut_')) {
			$restserver_m =& $this->backend->getManager('Restserveruser');
		} else {
			$restserver_m =& $this->backend->getManager('Restserver');
		}

		$admin_m =& $this->backend->getManager('Admin');
		$developer_m =& $this->backend->getManager('Developer');

		switch ($method)
		{
			case 'POST':
				$columns = $this->af->getBackboneAll();
				$ret = $restserver_m->post($table, $columns);
				break;

			case 'PATCH':
				$id = $this->af->get('_id');
				$columns = $this->af->getBackboneAll();
				$ret = $restserver_m->patch($table, $id, $columns);
				break;

			case 'DELETE':
				$id = $this->af->get('_id');
				$ret = $restserver_m->delete($table, $id);
				break;
		}

		$http_status = $ret ? 204 : 500;

		// ログ
		$log_columns = array(
			'user'        => $this->session->get('lid'),
			'method'      => $method,
			'http_status' => $http_status,
		);

		if (isset($id)) {
			$log_columns['id'] = $id;
		}

		if (isset($columns)) foreach ($columns as $key => $value) {
			if (!isset($log_columns[$key])) {
				$log_columns[$key] = $value;
			}
		}

		$admin_m->addAdminOperationLog('/api/rest', $table, $log_columns);

		if (strncmp($table, 'm_', strlen('m_')) === 0) {
			$developer_m ->logMasterModify(array(
				'account_reg' => $log_columns['user'],
				'table_name'  => $table,
				'action'      => $this->backend->ctl->getCurrentActionName(),
			));
		}

		// ビューへ遷移
		switch ($http_status) {
		case 204:
			// キャッシュを保持しているテーブルに変更が入った場合、キャッシュ情報のクリアを行う
			if (isset($this->cachekey_associated_table[$table])) {
				$cache_m =& Ethna_CacheManager::getInstance('memcache');
				foreach ($this->cachekey_associated_table[$table] as $table => $cache_key) {
					$cache_m->clear($cache_key);
				}
			}
			return 'admin_ok_204';
		}

		return 'admin_error_500';
	}
}
