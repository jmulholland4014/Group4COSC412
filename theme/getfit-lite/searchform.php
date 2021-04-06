<?php
/**
 * The template for displaying search forms in Getfit Lite
 *
 * @package Getfit Lite
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search...', 'placeholder', 'getfit-lite' ); ?>" value="<?php echo get_search_query() ; ?>" name="s">
	</label>
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'getfit-lite' ); ?>">
</form>
