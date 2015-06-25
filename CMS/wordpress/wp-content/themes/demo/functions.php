<?php

// 管理画面じゃなかったら
if(!is_admin()) {

	// jsを登録して呼び出す処理
	function scripts() {
		// wordpress標準のjQueryを削除
		wp_deregister_script('jquery');
		// GoogleCDNのjQueryを登録
		wp_register_script('jquery', "https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js");
		wp_enqueue_script('jquery');

		// 独自のファイルは削除処理はいらない
		wp_register_script('commonjs', get_stylesheet_directory_uri().'/js/common.js', array());
		wp_enqueue_script('commonjs');
	}
	// add_actionで関数トリガーを登録する
	add_action('wp_enqueue_scripts', 'scripts');

	// cssも同様に
	function styles() {
		wp_register_style('common', get_stylesheet_directory_uri() . '/css/common.css', array());
		wp_enqueue_style('common');

		// 条件分岐をする場合
/*		// トップページの場合
		if (is_home()) {
			wp_register_style('home', get_stylesheet_directory_uri() . '/css/home.css', array());
			wp_enqueue_style('home');
		}

		// スラッグがニュースの固定ページもしくはその子ページもしくは投稿タイプがnewsの場合
		if(is_page('news') || is_parent_slug() == 'news' || is_single('news')) {
			wp_register_style('news', get_stylesheet_directory_uri() . '/css/news.css', array());
			wp_enqueue_style('news');
		}
*/
	}
	add_action('wp_print_styles', 'styles');


	// menuなどで自動的に付与されるclassを削除。'current-menu-item'は自動追加されるclass達
	function my_css_attributes_filter($var) {
		return is_array($var) ? array_intersect($var,  array( 'current-menu-item') ) : '';
	}
	add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
	add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
	add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);

	// header内の不要なデフォルト表示のものを消す
	remove_action('wp_head', 'rsd_link');                         	// Really Simple Discoveryリンクの削除
	remove_action('wp_head', 'wlwmanifest_link');                 	// Windows Live Writerの削除
	remove_action('wp_head', 'wp_generator');                     	// WPのバージョン削除
	remove_action('wp_head', 'rel_canonical');
	remove_action('wp_head', 'index_rel_link' );                  	// linkタグの削除
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');  	// link rel='next…'関連の削除
	remove_action('wp_head', 'parent_post_rel_link', 10, 0 );     	// link rel='next…'関連の削除
	remove_action('wp_head', 'start_post_rel_link', 10, 0 );      	// link rel='next…'関連の削除
	remove_action('wp_head', 'wp_shortlink_wp_head');             	// ショートリンクの削除
	remove_action('wp_head', 'feed_links_extra',3);               	// コメントフィードの削除
	remove_action('wp_head', 'print_emoji_detection_script', 7);	// 絵文字のjs削除
	remove_action('wp_print_styles', 'print_emoji_styles');			// 絵文字のcss削除

}

// 親子関係があるページで。子ページから親ページのスラッグを取得する関数
function is_parent_slug() {
  global $post;
  if ($post->post_parent) {
    $post_data = get_post($post->post_parent);
    return $post_data->post_name;
  }
}


// メニューの登録
register_nav_menus( array(
	'menu' => 'menu',
	'footer' => 'footer'
	)
);

// サイドバーの登録 第1引数がサイドバーの数
register_sidebars(2, array(
	'name' => __('side widget %d'),
	'id' => 'side-widget',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '',
	'after_title' => ''
	)
);

// アーカイブを表示する際に追記したテキストもリンクに含める関数
function archives_link($html){
  return preg_replace('@</a>(.+?)</li>@', '\1</a></li>', $html);
}
add_filter('get_archives_link', 'archives_link');


// 投稿を表示する際のオプション
function single_news($query) {

	if( is_post_type_archive('news') ){
		$query->set('posts_per_page', 30);
		$query->set('order', 'desc');
		return;
	}
}
add_action('pre_get_posts', 'single_news');


// the_excerpで何文字表示するかの設定
function excerpt_length( $length ) {
    return 45;
}
add_filter( 'excerpt_length', 'excerpt_length', 999 );

// サムネイル画像を使用できるようにする
function default_thumbnail( $post_id ) {
  $post_thumbnail = get_post_meta( $post_id, $key = '_thumbnail_id', $single = true );
  if ( !wp_is_post_revision( $post_id ) ) {
    if ( empty( $post_thumbnail ) ) {
      update_post_meta( $post_id, $meta_key = '_thumbnail_id', $meta_value = '199' );
    }
  }
}
add_theme_support( "post-thumbnails" );
set_post_thumbnail_size( 150, 130 ); // ここのサイズを変更でデフォルトサイズを変更する。（x, y）
add_action( 'save_post', 'default_thumbnail' );

/*
// カスタム投稿タイプを追加
add_action( 'init', 'create_post_type' );
function create_post_type() {
    register_post_type( 'news', //カスタム投稿タイプ名を指定
        array(
            'labels' => array(
            'name' => __( 'ニュース' ),
            'singular_name' => __( 'ニュース' )
        ),
        'public' => true,
        'has_archive' => true, // アーカイブページを持つ
        'menu_position' =>5, //管理画面のメニュー順位
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ,'comments' ),
        )
    );

// カテゴリタクソノミー(カテゴリー分け)を使えるように設定する
  register_taxonomy(
    'orijinal_themes_cat', // タクソノミーの名前
    'news', // 使用するカスタム投稿タイプ名
    array(
      'hierarchical' => true, //trueだと親子関係が使用可能。falseで使用不可
      'update_count_callback' => '_update_post_term_count',
      'label' => '2015年',
      'singular_label' => '2015年',
      'public' => true,
      'show_ui' => true
    )
  );
}

// カスタム投稿タイプの年別リストに「年」をつける設定
function add_year_archives( $link_html ) {
    $regex = array (
        "/ title='([\d]{4})'/"  => " title='$1年'",
        "/ ([\d]{4}) /"         => " $1年 ",
        "/>([\d]{4})<\/a>/"        => ">$1年</a>"
    );
    $link_html = preg_replace( array_keys( $regex ), $regex, $link_html );
    return $link_html;
}
add_filter( 'get_archives_link', 'add_year_archives' );
*/

?>