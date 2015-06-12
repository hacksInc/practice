<?php

interface InterfacePlatform
{

	
	/**
	 * トランザクションIDを生成する
	 */
	public function genTransId();

	/**
	 *  出金
	 *
	 *  @access public
	 *  @param  string   $transaction_id 購入ID
	 *  @param  integer  $puid  PlatformユーザーID
	 *  @param  integer  $item_id  アイテムID
	 *  @param  integer  $price  価格
	 *  @return array  結果
	 *
	 */
	public function requestPaymentUse($transaction_id, $puid, $item_id, $price);

	/**
	 *  残高参照
	 *
	 *  @access public
	 *  @param  integer  $puid  PlatformユーザーID
	 *  @return array  残高情報
	 *
	 */
	public function requestPaymentCheck($puid);

	/**
	 *  アカウント登録
	 *
	 *  @access public
	 *  @param  integer  $account  アカウント
	 *  @param  integer  $password  パスワード
	 *  @return array  登録結果
	 *
	 */
	public function requestUserRegist($account, $password);

	/**
	 *  ユーザー情報参照
	 *
	 *  @access public
	 *  @param  integer  $account  アカウント
	 *  @return array  登録情報
	 *
	 */
	public function requestUserCheck($account);

	/**
	 *  ユーザー情報変更
	 *
	 *  @access public
	 *  @param  integer  $puid  PlatformユーザーID
	 *  @param  integer  $type  変更種別(1:パスワード、2:アカウント、3:メールアドレス、4:UIID)
	 *  @param  integer  $value  変更値
	 *  @return array  変更結果
	 *
	 */
	public function requestUserEdit($puid, $type, $value);


	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  string  $user_id  ユーザーID
	 *  @return array  DBデータ
	 *
	 */
	public function dbGetUser($user_id);

	/**
	 *  ユーザーのパラメータを取得
	 *
	 *  @access public
	 *  @param  integer  $puid  PlatformのユーザーID
	 *  @return array  DBデータ
	 *
	 */
	public function dbGetUserByPuid($puid);

	/**
	 *  ユーザーのパラメータを更新
	 *
	 *  @access public
	 *  @param  string  $user_id  ユーザーID
	 *  @param  array  $columns  カラム
	 *  @return boolean  true :成功, false: 失敗
	 *
	 */
	public function dbUpdateUser($user_id, $columns);

	/**
	 *  ユーザーのパラメータを作成
	 *
	 *  @access public
	 *  @param  string  $user_id  ユーザーID
	 *  @param  array  $columns  カラム
	 *  @return boolean  true :成功, false: 失敗
	 *
	 */
	public function dbInsertUser($user_id, $columns);
	
}
?>