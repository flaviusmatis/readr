<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Readr
 * @since Readr 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

		<?php if ( have_posts() ) : ?>

			<div class="page-header">
				<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'readr' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</div>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php readr_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<div id="post-0" class="post no-results not-found">
				<div class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'readr' ); ?></h1>
				</div>

				<div class="entry-content">
					<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'readr' ); ?></p>
				</div><!-- .entry-content -->
			</div><!-- #post-0 -->

		<?php endif; ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>