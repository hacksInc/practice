
<div class="sidebar">
	<?php get_search_form(); ?>
	<?php
		wp_nav_menu( array('container' => false, 'theme_location' => 'footer', 'items_wrap' => '<ul>%3$s</ul>'));

	/*		アーカイブを取得する場合
			wp_get_archives(array(
				'post_type' => 'news',
	   		 	'type' => 'yearly',
	    		'show_post_count' => 1
	   			)
			);
	*/
	?>
<!-- sidebar --></div>