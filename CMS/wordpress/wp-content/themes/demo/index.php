<!-- ヘッダーの呼び出し。文字列指定で任意のヘッダー取得 -->
<?php get_header(); ?>
<div class="content">
	<div class="news">
		<h2>最新ニュース</h2>
		<ul>
		<?php
		// 投稿の取得
		// 取得条件を変更する
		$query = new WP_Query(array(
			//"category_name" => "news", // 投稿時に設定したカテゴリー名
			//"tag" => "test" //タグのスラッグを指定
			//"post_type" =>'news', // カスタム投稿タイプの種類（デフォルトはPOST）
			"posts_per_page" => "5", // 1ページに表示する数
			"order" => "desc" // 表示順
			//"offset" => "1", // 最初にいくつ飛ばして表示するか
			//その他はリファレンスを参照ください。
			)
		);
		if($query->have_posts()) :
			while ($query->have_posts()) :
				$query->the_post();
				// カテゴリーの情報取得
				//$cat = get_the_category(); $cat = $cat[0];
		?>
			<li>
				<a href="<?php the_permalink(); //リンクの取得 ?>">
					<span class="date"><?php the_time("Y.m.d"); //時間の取得 ?></span>
					<?php  //サムネイル画像を取得する場合  the_post_thumbnail(array(100,100), array("class"=>"sub_image")); ?>
					<?php  //カテゴリー系を取得する場合  <span class="type <?php echo $cat->category_nicename; //カテゴリースラッグ取得 "><?php echo $cat->cat_name; //カテゴリー名取得 </span> ?>
					<h3 class="title"><?php the_title(); //記事のタイトル取得 ?></h3>
				</a>
			</li>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); //new WP_Queryの後は必ずresetすること ?>
		<?php endif; ?>
		</ul>
	<!-- news --></div>
	<div class="news">
		<?php
		// ニュースフィードの取得
		//WrodPressのfeed.phpの呼び出し
		include_once ABSPATH . WPINC . '/feed.php';

		// 任意のFeedを取得
		$feed = fetch_feed('http://gamebiz.jp/?feed');

		if (is_wp_error($feed)) {
			$maxitems = 0;
		} else {
			//5件取得
			$maxitems = $feed->get_item_quantity(5);
			$items = $feed->get_items(0, $maxitems);
		}
		?>

		<?php if ($maxitems): // データあったら表示 ?>
		<h2>ゲーム業界最新ニュース</h2>
		<ul>
			<?php foreach ($items as $item): ?>
		    <li>
		        <a href="<?php echo $item->get_permalink(); ?>" target="_blank"　>
		        	<span class="date"><?php echo $item->get_date('Y.m.d'); ?></span>
			        <h3 class="title"><?php echo mb_strimwidth($item->get_title(), 0, 60, '…'); ?></h3>
		        </a>
		    </li>
			<?php endforeach; ?>
		</ul>
		<?php else: // データ無しの場合 ?>
			<!-- データないよ -->
		<?php endif; ?>
	<!-- feed --></div>

	<div class="paginate">
		<?php
		// ページネイト
		global $wp_query, $paged;
		$big = 999999999; // need an unlikely integer
			echo paginate_links( array(
			    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			    'format' => '?paged=%#%',
			    'current' => max( 1, get_query_var('paged') ),
			    	'prev_text' => '<span><<次へ</span>',
       				'next_text' => '<span>前へ>></span>', 
			    'total' => $wp_query->max_num_pages
			));
		?>
	<!-- paginate --></div>
<!-- content --></div>

<?php get_sidebar(); //サイドバーの呼び出し ?>

<?php get_footer(); //フッターの呼び出し ?>
