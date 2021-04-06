<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Getfit Lite
 */
?>


	
        <header>
            <h1 class="entry-title"><?php esc_attr_e( 'Nothing Found', 'getfit-lite' ); ?></h1>
        </header>

		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( esc_attr( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'getfit-lite' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_attr_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'getfit-lite' ); ?></p><br />
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_attr_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'getfit-lite' ); ?></p><br />
			<?php get_search_form(); ?>

		<?php endif; ?>
	

