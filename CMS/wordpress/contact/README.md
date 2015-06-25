####2015/06/23 豊作 記載


##概要
WordPress内でPOSTでデータを送ると上手く動作しないため、外部に置く。
共通部分などはWordPress内からincludeする


##構成
index.php ・・・ wordpress内のページを参照(URL（スラッグ)をwordpressと合わせる)
			　例）　ここが hoge.com/contact/の場合、woredpress内でhoge.com/contact/となるようなスラッグのページを作る
entry ・・・ 送信フォーム部分
