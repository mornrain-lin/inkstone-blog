<?php
/**
 * 主模板文件（fallback template）。
 *
 * 当没有更具体的模板（single/page/archive/search/404）匹配时使用。
 *
 * @package InkstoneBlog
 * @since   1.0.0
 */

get_header();
?>

<div class="ink-container">
	<?php inkstone_breadcrumb(); ?>

	<div class="ink-content-area">
		<div class="ink-primary">
			<?php if ( have_posts() ) : ?>

				<?php
				if ( is_home() && ! is_front_page() ) {
					inkstone_page_header();
				}
				?>

				<div class="ink-post-list">
					<?php
					while ( have_posts() ) {
						the_post();
						inkstone_post_item();
					}
					?>
				</div>

				<?php inkstone_pagination(); ?>

			<?php else : ?>

				<div class="ink-no-results">
					<h2 class="ink-no-results__title"><?php esc_html_e( '暂时没有内容', 'inkstone-blog' ); ?></h2>
					<p><?php esc_html_e( '这里还没有发布任何文章。可以换个关键词，或从首页开始阅读。', 'inkstone-blog' ); ?></p>
					<?php get_search_form(); ?>
				</div>

			<?php endif; ?>
		</div>

		<?php inkstone_sidebar( 'sidebar-1' ); ?>
	</div>
</div>

<?php
get_footer();
