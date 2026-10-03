<?php
/**
 * 搜索表单模板。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

$ink_search_id = 'ink-search-' . wp_rand( 1000, 9999 );
?>

<form role="search" method="get" class="ink-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="ink-screen-reader-text" for="<?php echo esc_attr( $ink_search_id ); ?>">
		<?php esc_html_e( '搜索本站文章', 'inkstone-blog' ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $ink_search_id ); ?>"
		class="ink-search-form__field"
		placeholder="<?php esc_attr_e( '输入关键词…', 'inkstone-blog' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	>
	<button type="submit" class="ink-search-form__submit">
		<?php esc_html_e( '搜索', 'inkstone-blog' ); ?>
	</button>
</form>
