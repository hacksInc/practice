<?php get_header(); ?>
<div class="content">
	<div class="news">
		<h2><?php wp_title(''); ?></h2>
		<ul>
			<?php
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 ;
				$query = new WP_Query(array(
					"paged" => $paged,
					//"category_name" =>'news',
					"posts_per_page" => "20",
					"order" => "desc"
				));
				if($query->have_posts()) :
					while ($query->have_posts()) :
						$query->the_post();
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
			$big = 999999999; // need an unlikely integer
			echo paginate_links( array(
			    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			    'format' => '?paged=%#%',
			    'current' => max( 1, get_query_var('paged') ),
			    	'prev_text' => '<span><<次へ</span>',
		        		'next_text' => '<span>前へ>></span>',
			    'total' => $query->max_num_pages
			));
		?>
	</div>
	<?php wp_reset_postdata(); ?>
	<?php endif; ?>
<!-- content --></div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>