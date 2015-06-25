<?php get_header(); ?>
<div class="content">
	<h2><?php the_title(); ?></h2>
	<div class="news">
		<?php
			if(have_posts()) :
				while (have_posts()) :
					the_post();
		?>
		<span class="date"><?php the_time("Y.m.d"); ?></span>
		<?php the_content(); ?>
	<!-- news --></div>
	<div class="paginate">
		<span class="nxt"><?php next_post_link('%link','< %title',TRUE); ?></span>
		<span class="prv"><?php previous_post_link('%link','%title >',TRUE); ?></span>
	</div>
		<?php endwhile; ?>
		<?php endif; ?>
<!-- conetnt --></div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>