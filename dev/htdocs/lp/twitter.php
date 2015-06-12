<?php


/**********************
   設定
**********************/
$api_key = "F6NRO6sgMsKZodYY75kynCpIq";	//API Key
$api_secret = "dO1n0RppTOotzt4CMnAYCS3a6hLI8EtEDSmV3dnE0lV9D79bDF";	//API Secret
$access_token = "2574312738-5gVINi1QKKb8OoRsLgCt93v4EquMxF6ysbqzjpq";	//アクセストークン
$access_token_secret = "CMUOXmXSMYeFBNjYekLL254fuQtqKnTHIvEo1ew7URITE";	//アクセストークン・シークレット

//エンドポイント
$request_method = "GET";	//メソッド
$request_url = "https://api.twitter.com/1.1/statuses/user_timeline.json";	//URL

//オプション・パラメータを連想配列で設定
$params_a = array(
   "count" => 200,
//	"user_id" => "",
   "screen_name" => "@toyosakuyuta",
//	"max_id" => "",
//	"since_id" => "",
   "trim_user" => false,
   "exclude_replies" => false,
   "contributor_details" => false,
   "include_rts" => true,
);

/**********************
   署名作成
**********************/
//OAuth1.0認証用のパラメータを連想配列で用意
$params_b = array(
   "oauth_consumer_key" => $api_key,
   "oauth_token" => $access_token,
   "oauth_nonce" => microtime(),
   "oauth_signature_method" => "HMAC-SHA1",
   "oauth_timestamp" => time(),
   "oauth_version" => "1.0"
);

//キーを作成する
$signature_key = rawurlencode($api_secret)."&".rawurlencode($access_token_secret);

//オプション・パラメータ[$params_a]と認証用パラメータ[$params_b]を署名作成のため、合体させた[$params_c]を用意
$params_c = array_merge($params_a,$params_b);

//[$params_c]をアルファベット順に並び替える
ksort($params_c);

//配列[$params_c]を[キー=値&キー=値...]の文字列に変換
$signature_params = str_replace(array("+","%7E"),array("%20","~"),http_build_query($params_c,"","&"));

//リクエストメソッド、リクエストURL、パラメータを、URLエンコードしてから[&]で繋ぎ、データを作成する
$signature_data = rawurlencode($request_method)."&".rawurlencode($request_url)."&".rawurlencode($signature_params);

//キー[$signature_key]とデータ[$signature_data]をHMAC-SHA1方式のハッシュ値に変換し、base64エンコードして、署名を作成する
$signature = base64_encode(hash_hmac("sha1",$signature_data,$signature_key,TRUE));

/**********************
   リクエスト
**********************/
//[$params_c]に、作成した署名を加える
$params_c["oauth_signature"] = $signature;

//[$params_c]を[キー=値,キー=値,...]の文字列に変換する(ヘッダー用)
$header_params = http_build_query($params_c,"",",");

//[$params_a]を、リクエストURLの末尾に付けるクエリーに変換して付ける(GETの場合)
//[例] ?screen_name=arayutw
if($params_a && $request_method=="GET"){
   $request_url .= "?".http_build_query($params_a,"","&");
}

//TwitterにGETリクエストを送る [$json]にTwitterから返ってきたJSONが格納される
$json = @file_get_contents(
   $request_url,	//[第1引数：リクエストURL($request_url)]
   false,		//[第2引数：リクエストURLは相対パスか？(違うのでfalse)]
   stream_context_create(	//[第3引数：stream_context_create()でメソッドとヘッダーを指定]
      array(
         "http" => array(
            "method" => $request_method, //リクエストメソッド
            "header" => array(			 //カスタムヘッダー
               "Authorization: OAuth ".$header_params,
            ),
         )
      )
   )
);

//検証用のレスポンスヘッダー
$r_header_str = print_r($http_response_header,1);

//JSONをオブジェクト(stdClass)に変換
$obj = json_decode($json);

//取得件数
$count = count((array)$obj);

