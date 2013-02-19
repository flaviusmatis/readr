<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Readr
 * @since Readr 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<div class="footer">
		<div class="footer-content">
			<p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. All Rights Reserved.</p>
			<p class="credits">Powered by <a href="http://wordpress.org/" target="_blank">WordPress</a> | <a href="http://flaviusmatis.github.com/" target="_blank">Readr Theme</a></p>
		</div>
	</div>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>