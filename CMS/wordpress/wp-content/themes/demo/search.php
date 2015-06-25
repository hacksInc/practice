<?php get_header(); ?>
<div class="content">
	<div class="news">
		<?php if (isset($_GET['s']) && empty($_GET['s'])) :?>
		<h2><?php echo $wp_query->post_count; ?><span> 件の記事が見つかりました</span></h2>
  		<?php else: ?>
		<h2><?php echo get_search_query(); ?><span> の検索結果：</span><?php echo $wp_query->post_count; ?><span> 件の記事が見つかりました</span></h2>
		<?php endif; ?>

		<ul>
			<?php
				if(have_posts()) :
					while (have_posts()) :
						the_post();
			?>
			<li>
				<a href="<?php echo the_permalink(); ?>">
					<span class="date"><?php the_time("Y.m.d"); ?></span>
					<span class="title"><?php the_title(); ?></span>
				</a>
			</li>
			<?php endwhile; ?>
		</ul>
	<!-- news --></div>
	<div class="paginate">
		<?php
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
	</div>
	<?php else : ?>
		<h2>記事が見つかりませんでした。</h2>
	<?php endif; ?>
<!-- content --></div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>