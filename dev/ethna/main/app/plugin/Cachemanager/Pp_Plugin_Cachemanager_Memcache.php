<?php
/**
 *  Pp_Plugin_Cachemanager_Memcache.php
 *
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  キャッシュマネージャクラス(memcache版)
 *
 *  Ethna_Plugin_Cachemanager_Memcacheと同様に使用できるプラグインだが、
 *  PHPモジュールはMemcachedを用いる。（Memcacheモジュールではない）
 *  Ethna_Plugin_Cachemanager_Memcacheと異なり、
 *  コネクションプーリングや複数サーバーのハンドリングはできないので注意。
 *  @access     public
 *  @package    Pp
 */
class Pp_Plugin_Cachemanager_Memcache extends Ethna_Plugin_Cachemanager
{
    /**#@+  @access private */

    /** @var    object  MemCache    MemCacheオブジェクト */
    var $memcache = null;

    /** @var bool 圧縮フラグ */
    var $compress = true;

    /**#@-*/

    /**
     *  Pp_Plugin_Cachemanager_Memcacheクラスのコンストラクタ
     *
     *  @access public
     */
    function Pp_Plugin_Cachemanager_Memcache(&$controller)
    {
        parent::Ethna_Plugin_Cachemanager($controller);
        $this->memcache_pool = array();
		$this->_getMemcache(null);
    }

    /**
     *  memcacheキャッシュオブジェクトを生成、取得する
     *
     *  @access protected
     */
    function _getMemcache($cache_key, $namespace = null)
    {
        list($host, $port) = $this->_getMemcacheInfo($cache_key, $namespace);
        if (isset($this->memcache_pool["$host:$port"])) {
            // activate
            $this->memcache = $this->memcache_pool["$host:$port"];
            return $this->memcache;
        }
        $this->memcache_pool["$host:$port"] =& new MemCached();

		// この時点では、サーバーへの接続は確立されません。
		$r = $this->memcache_pool["$host:$port"]->addServer($host, $port);
        if ($r == false) {
            trigger_error("memcache: addServer failed");
        }

        $this->memcache = $this->memcache_pool["$host:$port"];
        return $this->memcache;
    }

    /**
     *  memcache接続情報を取得する
     *
     *  @access protected
     *  @todo   $cache_keyから$indexを決める方法を変更できるようにする
     */
    function _getMemcacheInfo($cache_key, $namespace)
    {
        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $memcache_info = $this->config->get('memcache');
        $default_memcache_host = $this->config->get('memcache_host');
        if ($default_memcache_host == "") {
            $default_memcache_host = "localhost";
        }
        $default_memcache_port = $this->config->get('memcache_port');
        if ($default_memcache_port == "") {
            $default_memcache_port = 11211;
        }
        if ($memcache_info == null || isset($memcache_info[$namespace]) == false) {
            return array($default_memcache_host, $default_memcache_port);
        }

        // namespace/cache_keyで接続先を決定
        $n = count($memcache_info[$namespace]);

        $index = $cache_key % $n;
        return array(
            isset($memcache_info[$namespace][$index]['memcache_host']) ?
                $memcache_info[$namespace][$index]['memcache_host'] :
                'localhost',
            isset($memcache_info[$namespace][$index]['memcache_port']) ?
                $memcache_info[$namespace][$index]['memcache_port'] :
                11211,
        );

        // for safe
        return array($default_memcache_host, $default_memcache_port);
    }

    /**
     *  キャッシュに設定された値を取得する
     *
     *  キャッシュに値が設定されている場合はキャッシュ値
     *  が戻り値となる。キャッシュに値が無い場合やlifetime
     *  を過ぎている場合、エラーが発生した場合はEthna_Error
     *  オブジェクトが戻り値となる。
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  int     $lifetime   キャッシュ有効期間
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return array   キャッシュ値
     */
    function get($key, $lifetime = null, $namespace = null)
    {
        if ( $this->config->get( 'memcache_valid' ) == 0 ) {
            //return Ethna::raiseError('', E_CACHE_NO_VALUE);
            return null;
        }

        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        $value = $this->memcache->get($cache_key);
        if ($value == null) {
            return Ethna::raiseError('no such cache', E_CACHE_NO_VALUE);
        }
        $time = $value['time'];
        $data = $value['data'];

        // ライフタイムチェック
        if (is_null($lifetime) == false) {
            if (($time+$lifetime) < time()) {
                return Ethna::raiseError('lifetime expired', E_CACHE_EXPIRED);
            }
        }

        return $data;
    }

    /**
     *  キャッシュの最終更新日時を取得する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return int     最終更新日時(unixtime)
     */
    function getLastModified($key, $namespace = null)
    {
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        $value = $this->memcache->get($cache_key);

        return $value['time'];
    }

    /**
     *  値がキャッシュされているかどうかを取得する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  int     $lifetime   キャッシュ有効期間
     *  @param  string  $namespace  キャッシュネームスペース
     */
    function isCached($key, $lifetime = null, $namespace = null)
    {
        $r = $this->get($key, $lifetime, $namespace);

        return Ethna::isError($r) ? false: true;
    }

    /**
     *  キャッシュに値を設定する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  mixed   $value      キャッシュ値
     *  @param  int     $timestamp  キャッシュ最終更新時刻(unixtime)
     *  @param  string  $namespace  キャッシュネームスペース
     */
    function set($key, $value, $timestamp = null, $namespace = null)
    {
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        $time = $timestamp ? $timestamp : time();
        $this->memcache->set($cache_key, array('time' => $time, 'data' => $value));
    }

    /**
     *  キャッシュ値を削除する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  string  $namespace  キャッシュネームスペース
     */
    function clear($key, $namespace = null)
    {
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_NO_VALUE);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;

        $cache_key = $this->_getCacheKey($namespace, $key);
        if ($cache_key == null) {
            return Ethna::raiseError('invalid cache key (too long?)', E_CACHE_NO_VALUE);
        }

        //$this->memcache->delete($cache_key, -1);
        $this->memcache->delete($cache_key);
    }

    /**
     *  キャッシュデータをロックする
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  int     $timeout    ロックタイムアウト
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return bool    true:成功 false:失敗
     */
    function lock($key, $timeout = 5, $namespace = null)
    {
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_LOCK_ERROR);
        }

        // ロック用キャッシュデータを利用する
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_key = "lock::" . $this->_getCacheKey($namespace, $key);
        $lock_lifetime = 30;

        do {
            $r = $this->memcache->add($cache_key, true, false, $lock_lifetime);
            if ($r != false) {
                break;
            }
            sleep(1);
            $timeout--;
        } while ($timeout > 0);

        if ($r == false) {
            return Ethna::raiseError('lock timeout', E_CACHE_LOCK_TIMEOUT);
        }

        return true;
    }

    /**
     *  キャッシュデータのロックを解除する
     *
     *  @access public
     *  @param  string  $key        キャッシュキー
     *  @param  string  $namespace  キャッシュネームスペース
     *  @return bool    true:成功 false:失敗
     */
    function unlock($key, $namespace = null)
    {
        $this->_getMemcache($key, $namespace);
        if ($this->memcache == null) {
            return Ethna::raiseError('memcache server not available', E_CACHE_LOCK_ERROR);
        }

        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $cache_key = "lock::" . $this->_getCacheKey($namespace, $key);

        //$this->memcache->delete($cache_key, -1);
        $this->memcache->delete($cache_key);
    }

    /**
     *  ネームスペースからキャッシュキーを生成する
     *
     *  @access private
     */
    function _getCacheKey($namespace, $key)
    {
        // 少し乱暴だけど...
        $key = str_replace(":", "_", $key);
        $cache_key = $namespace . "::" . $key;
        if (strlen($cache_key) > 250) {
            return null;
        }
        return $cache_key;
    }

    /**
     * 圧縮フラグを立てる
     *
     * MySQLなどいくつかの子クラスで有効
     * 
     * @access public
     * @param bool $flag フラグ
     */
    function setCompress($flag) {
        $this->compress = $flag;
		$this->memcache->setOption(Memcached::OPT_COMPRESSION, $this->compress);
    }
	
	/**
	 * 既存のアイテムの前にデータを付加する、または新しいキーで追加する
	 * 
	 * この関数は基底クラスに無い独自拡張なので注意
	 * キーは、このキャッシュマネージャでのネームスペースの処理は行わずにそのままMemcachedのキーとして使用するので、取得時に注意
     * @param  string  $key        キャッシュキー
     * @param  string  $value      キャッシュ値
	 */
	function prependOrAdd($key, $value)
	{
//		$this->backend->logger->log(LOG_DEBUG, 'prependOrAdd. key[%s] value[%s]', $key, $value);
		
		$memcached = $this->memcache;
		
		// 圧縮を無効にする
		$compression = $memcached->getOption(Memcached::OPT_COMPRESSION);
		$memcached->setOption(Memcached::OPT_COMPRESSION, false);

		// prependする
		$ret = $memcached->prepend($key, $value);
		if (!$ret) {
			// まだキーが存在しない場合はprepend失敗する。代わりにaddを実行する。
			$ret = $memcached->add($key, $value);
			if (!$ret) {
				// 別プロセスが既にadd済みだった場合はaddに失敗する。再度prependを試みる。
				$ret = $memcached->prepend($key, $value);
				if (!$ret) {
					// 再度のprependも失敗したらエラー
					$this->backend->logger->log(LOG_ERR, 'Memcached::prepend failed. key[%s] value[%s]', $key, $value);
				}
			}
		}

		// 圧縮設定を元に戻す
		$memcached->setOption(Memcached::OPT_COMPRESSION, $compression);
	}
}
?>
