<?php

function readr_enqueue_assets(){
  $style_path = get_stylesheet_directory_uri();

  wp_enqueue_style( 'readr_custom_font', 'http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,900,400italic|Crimson+Text:400,600', array(), 1, 'all' );
  wp_enqueue_style( 'readr_font_awesome', $style_path . '/font-awesome.min.css', array(), 1, 'all' );
  wp_enqueue_style( 'readr_style', $style_path . '/style.css', array('readr_font_awesome', 'readr_custom_font'), 1, 'all' );
}
add_action( 'wp_enqueue_scripts', 'readr_enqueue_assets' );

/**
 * Sets up the content width value based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 600;

/**
 * Sets up theme defaults and registers the various WordPress features that
 * Readr supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 *
 * @since Readr 1.0
 */
function readr_setup() {
	/*
	 * Makes Readr available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 */
	load_theme_textdomain( 'readr', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

}
add_action( 'after_setup_theme', 'readr_setup' );

/**
 * Enqueues scripts and styles for front-end.
 *
 * @since Readr 1.0
 */
function readr_scripts_styles() {
	global $wp_styles;

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

}
add_action( 'wp_enqueue_scripts', 'readr_scripts_styles' );

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since Readr 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function readr_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'readr' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'readr_wp_title', 10, 2 );

/**
 * Registers our main widget area and the front page widget areas.
 *
 * @since Readr 1.0
 */
function readr_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'readr' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'readr' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'readr_widgets_init' );


/**
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Readr 1.0
 */
function readr_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'readr_page_menu_args' );

if ( ! function_exists( 'readr_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Readr 1.0
 */
function readr_content_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<p id="<?php echo $html_id; ?>" class="navigation clearfix" role="navigation">
			<span class="nav-previous alignleft"><?php next_posts_link( __( '<span class="icon-circle-arrow-left"></span>&nbsp; Older posts', 'readr' ) ); ?></span>
			<span class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts &nbsp;<span class="icon-circle-arrow-right"></span>', 'readr' ) ); ?></span>
		</p><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'readr_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own readr_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Readr 1.0
 */
function readr_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'readr' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'readr' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<div class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite class="fn">%1$s %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'readr' ) . '</span>' : ''
					);
				?>
			</div><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'readr' ); ?></p>
			<?php endif; ?>

			<div class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'readr' ), '<p class="edit-link">', '</p>' ); ?>
			</div><!-- .comment-content -->

			<p class="reply">
				<?php
                    printf( '<a class="comment-date" href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                        esc_url( get_comment_link( $comment->comment_ID ) ),
                        get_comment_time( 'c' ),
                        /* translators: 1: date, 2: time */
                        sprintf( __( '%1$s at %2$s', 'readr' ), get_comment_date(), get_comment_time() )
                    );
                ?>
                &nbsp;/&nbsp;
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'readr' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</p><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

function custom_body_class( $classes ) {
	$background_color = get_background_color();

	if ( empty( $background_color ) )
		$classes[] = 'custom-background-empty';
	elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
		$classes[] = 'custom-background-white';

	return $classes;
}

add_filter( 'body_class', 'custom_body_class' );

function custom_search_form( $form ) {

    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
	    <label class="icon-search icon-large" for="s"></label>
	    <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="'. esc_attr__('Search') .'"/>
	    <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'"/>
    </form>';

    return $form;
}

add_filter( 'get_search_form', 'custom_search_form' );

if ( ! function_exists( 'readr_entry_date' ) ) :
function readr_entry_date() {

	$tag_list = get_the_tag_list( '', __( ', ', 'readr' ) );

	$date = sprintf( '<p title="%1$s" class="entry-date" datetime="%2$s">%3$s</p>',
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	printf(
		$date
	);
}
endif;

// Remove the version number of WP
// Warning - this info is also available in the readme.html file in your root directory - delete this file!
remove_action('wp_head', 'wp_generator');

// Customise the footer in admin area
function wpfme_footer_admin () {
	echo 'Theme by <a href="http://flaviusmatis.github.com/" target="_blank">Flavius Matis</a>. Powered by <a href="http://wordpress.org" target="_blank">WordPress</a>.';
}
add_filter('admin_footer_text', 'wpfme_footer_admin');

class Readr_Customize
{
   public static function register ( $wp_customize )
   {

      $wp_customize->add_setting( 'readr[style]',
         array(
            'default' => 'dark',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'transport' => 'refresh',
         )
      );

      $wp_customize->add_control( 'style_select_box', array(
          'settings' => 'readr[style]',
          'label' => __('Select Theme Style:', 'readr'),
          'section' => 'colors',
          'type' => 'select',
          'choices' => array(
              'dark' => 'Dark Style',
              'light' => 'Light Style',
	      ),
      ));

	  $wp_customize->add_section( 'readr_social' , array(
	      'title'      => __('Social Links','readr'),
	      'priority'   => 50,
	  ));

      $wp_customize->add_setting('readr[social_facebook]', array(
          'default' => '',
          'capability' => 'edit_theme_options',
          'type' => 'option',
          'transport' => 'refresh',
      ));

      $wp_customize->add_control('social_facebook', array(
          'label' => __('Facebook', 'readr'),
          'section' => 'readr_social',
          'settings' => 'readr[social_facebook]',
      ));

      $wp_customize->add_setting('readr[social_twitter]', array(
          'default' => '',
          'capability' => 'edit_theme_options',
          'type' => 'option',
          'transport' => 'refresh',
      ));

      $wp_customize->add_control('social_twitter', array(
          'label' => __('Twitter', 'readr'),
          'section' => 'readr_social',
          'settings' => 'readr[social_twitter]',
      ));

      $wp_customize->add_setting('readr[social_github]', array(
          'default' => '',
          'capability' => 'edit_theme_options',
          'type' => 'option',
          'transport' => 'refresh',
      ));

      $wp_customize->add_control('social_github', array(
          'label' => __('Github', 'readr'),
          'section' => 'readr_social',
          'settings' => 'readr[social_github]',
      ));

      $wp_customize->add_setting('readr[social_linkedin]', array(
          'default' => '',
          'capability' => 'edit_theme_options',
          'type' => 'option',
          'transport' => 'refresh',
      ));

      $wp_customize->add_control('social_linkedin', array(
          'label' => __('LinkedIn', 'readr'),
          'section' => 'readr_social',
          'settings' => 'readr[social_linkedin]',
      ));

      $wp_customize->add_setting('readr[social_pinterest]', array(
          'default' => '',
          'capability' => 'edit_theme_options',
          'type' => 'option',
          'transport' => 'refresh',
      ));

      $wp_customize->add_control('social_pinterest', array(
          'label' => __('Pinterest', 'readr'),
          'section' => 'readr_social',
          'settings' => 'readr[social_pinterest]',
      ));

      $wp_customize->add_setting('readr[social_google]', array(
          'default' => '',
          'capability' => 'edit_theme_options',
          'type' => 'option',
          'transport' => 'refresh',
      ));

      $wp_customize->add_control('social_google', array(
          'label' => __('Google+', 'readr'),
          'section' => 'readr_social',
          'settings' => 'readr[social_google]',
      ));

   }
}

//Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'Readr_Customize' , 'register' ) );
