<?php get_header(); ?>
	<!-- コンテンツが入る -->
	<div class="content">
	<?php
		if(have_posts()) {
			while (have_posts()) {
				the_post();
				the_content();
			}
		}
	?>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>