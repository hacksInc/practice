<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require('../wp-blog-header.php');
/** wordpress内のページを参照(URL（スラッグ)をwordpressと合わせる)
 *	例）　ここが hoge.com/contact/の場合、woredpress内でhoge.com/contact/となるようなスラッグのページを作る
 */