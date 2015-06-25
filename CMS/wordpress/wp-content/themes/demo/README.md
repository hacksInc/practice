####2015/06/23 豊作 記載


##概要
wordpress自作テーマ
下記命名規則あり。
基本的なことは盛り込んであり、簡単なレイアウトは組んであります。


##構成
・.sass-cache ・・・ sassのキャッシュ
・css ・・・ css格納場所
・images　・・・ 画像格納場所
・js ・・・ js格納場所
・sass ・・・ scss格納場所（コンパイルでcssを生成する）
・config.rb ・・・ compassの設定ファイル
・style.css ・・・ wordpress必須のcss（テーマの詳細情報を記載。ここにcssを記載してもよいが、cssフォルダにまとめた方がわかりやすい）
・functions.php　・・・ 共通の設定
・index.php ・・・ トップページ
・header.php ・・・ ヘッダー
・footer.php ・・・ フッター
・sidebar.php ・・・ サイドバー
・page.php ・・・ 固定ページ
・single.php ・・・ 投稿ページ
・archive.php ・・・ アーカイブページ
・search.php ・・・ 検索ページ
・searchform.php ・・・ 検索フォーム


##命名規則
トップページ ・・・ index.php, home.php
固定ページ ・・・ page.php, page-$slug.php, page-$id.php
投稿ページ ・・・ single.php, single-post.php, single-$posttype.php
ヘッダー ・・・ header.php, header-OO.php （OOには任意の文字）
フッター ・・・ fopter.php, footer-OO.php
サイドバー ・・・ sidebar.php, sidebar-OO.php
検索結果ページ ・・・ search.php
404エラー ・・・ 404.php
アーカイブページ ・・・ archive.php,
 - 作成者 ・・・ author.php, author-$id.php, author-$nicename.php
 - カテゴリ ・・・ category.php, category-$id.php, category-$slug.php
 - カスタム投稿 ・・・ archive-$posttype.php
 - タクソノミー ・・・ taxonomy.php, taxonomy-$taxonomy.php
 - 日付 ・・・ date.php
 - タグ ・・・ tag.php, tag-$id.php, tag-$slug.php

